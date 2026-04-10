<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\DamagedStock;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::where('admin_id', $this->getAdminId())->with(['category', 'brand']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        // Brand Filter
        if ($request->filled('brand') && $request->brand !== 'all') {
            $query->where('brand_id', $request->brand);
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status;
            if ($status === 'out') {
                $query->where('quantity', '<=', 0);
            } elseif ($status === 'low') {
                $query->where('quantity', '>', 0)
                      ->whereColumn('quantity', '<=', 'low_stock_threshold');
            } elseif ($status === 'over') {
                $query->whereColumn('quantity', '>=', 'overstock_threshold');
            } elseif ($status === 'good') {
                $query->whereColumn('quantity', '>', 'low_stock_threshold')
                      ->whereColumn('quantity', '<', 'overstock_threshold');
            }
        }

        $products = $query->paginate(10)->withQueryString();
        $categories = \App\Models\Category::where('admin_id', $this->getAdminId())->get();
        $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->get();
        
            if ($request->ajax()) {
            $rowsHtml = view('admin.products.partials.rows', compact('products'))->render();
            $paginationHtml = view('admin.products.partials.pagination', compact('products'))->render();
            $modalsHtml = view('admin.products.partials.modals', compact('products', 'categories', 'brands'))->render();
            return response()->json([
                'rows_html' => $rowsHtml,
                'pagination_html' => $paginationHtml,
                'modals_html' => $modalsHtml,
            ]);
        }

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'code' => 'required|string|unique:products,code',
            'image' => 'nullable|image|max:2048', // Max 2MB
            'low_stock_threshold' => 'nullable|integer|min:0',
            'good_stock_threshold' => 'nullable|integer|gt:low_stock_threshold',
            'overstock_threshold' => 'nullable|integer|gt:good_stock_threshold',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = \App\Models\Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'admin_id' => $this->getAdminId(),
            'price' => $request->price,
            'quantity' => $request->quantity,
            'code' => $request->code,
            'description' => $request->description,
            'image' => $imagePath,
            'low_stock_threshold' => $request->low_stock_threshold ?? 10,
            'good_stock_threshold' => $request->good_stock_threshold ?? 50,
            'overstock_threshold' => $request->overstock_threshold ?? 100,
        ]);

        // Log initial stock
        StockIn::create([
            'admin_id' => $this->getAdminId(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'notes' => 'Initial stock'
        ]);

        return redirect()->back()->with('success', 'Product added successfully.');
    }

    public function update(Request $request, $id)
    {
        $product = \App\Models\Product::where('admin_id', $this->getAdminId())->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'code' => 'required|string|unique:products,code,'.$product->id,
            'image' => 'nullable|image|max:2048',
            'low_stock_threshold' => 'nullable|integer|min:0',
            'good_stock_threshold' => 'nullable|integer|gt:low_stock_threshold',
            'overstock_threshold' => 'nullable|integer|gt:good_stock_threshold',
        ]);

        // Calculate quantity difference for logging
        $quantityDiff = $request->quantity - $product->quantity;

        // Detect threshold changes
        $newLow = $request->low_stock_threshold ?? $product->low_stock_threshold;
        $newGood = $request->good_stock_threshold ?? $product->good_stock_threshold;
        $newOver = $request->overstock_threshold ?? $product->overstock_threshold;

        $thresholdChanges = [];
        if ($newLow != $product->low_stock_threshold) $thresholdChanges[] = "Low: {$product->low_stock_threshold} -> {$newLow}";
        if ($newGood != $product->good_stock_threshold) $thresholdChanges[] = "Good: {$product->good_stock_threshold} -> {$newGood}";
        if ($newOver != $product->overstock_threshold) $thresholdChanges[] = "Over: {$product->overstock_threshold} -> {$newOver}";

        $data = [
            'name' => $request->name,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'code' => $request->code,
            'low_stock_threshold' => $newLow,
            'good_stock_threshold' => $newGood,
            'overstock_threshold' => $newOver,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        if ($quantityDiff != 0) {
            \App\Models\StockLog::create([
                'admin_id' => $this->getAdminId(),
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'action' => 'edit',
                'quantity_change' => $quantityDiff,
                'notes' => 'Stock updated manually'
            ]);
        }

        if (!empty($thresholdChanges)) {
            \App\Models\StockLog::create([
                'admin_id' => $this->getAdminId(),
                'user_id' => auth()->id(),
                'product_id' => $product->id,
                'action' => 'edit',
                'quantity_change' => 0,
                'notes' => 'Thresholds updated: ' . implode(', ', $thresholdChanges)
            ]);
        }

        return redirect()->back()->with('success', 'Product updated successfully.');
    }

    public function destroy($id)
    {
        $product = \App\Models\Product::where('admin_id', $this->getAdminId())->findOrFail($id);
        
        // Delete image if exists
        if ($product->image && file_exists(public_path('storage/' . $product->image))) {
            unlink(public_path('storage/' . $product->image));
        }
        
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }

    public function stockIn(Request $request, $id)
    {
        $product = \App\Models\Product::where('admin_id', $this->getAdminId())->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string'
        ]);

        $product->increment('quantity', $request->quantity);

        // Create StockIn record for the Logs page
        StockIn::create([
            'admin_id' => $this->getAdminId(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? 'Manual Stock In'
        ]);

        // Keep StockLog for Dashboard compatibility if needed, 
        // or we could rely on StockIn if we updated the dashboard query. 
        // For now, let's ensure it appears in the Logs section as requested.
        \App\Models\StockLog::create([
            'admin_id' => $this->getAdminId(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'action' => 'stock_in',
            'quantity_change' => $request->quantity,
            'notes' => $request->notes ?? 'Manual Stock In'
        ]);

        return redirect()->back()->with('success', 'Stock added successfully.');
    }

    public function reportDamaged(Request $request, $id)
    {
        $product = \App\Models\Product::where('admin_id', $this->getAdminId())->findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->quantity,
            'notes' => 'nullable|string'
        ]);

        $product->decrement('quantity', $request->quantity);
        $product->increment('damaged_quantity', $request->quantity);

        DamagedStock::create([
            'admin_id' => $this->getAdminId(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'quantity' => $request->quantity,
            'notes' => $request->notes ?? 'Damaged Items Reported'
        ]);

        return redirect()->back()->with('success', 'Damaged items reported successfully.');
    }

    public function updateThresholds(Request $request, $id)
    {
        $product = \App\Models\Product::where('admin_id', $this->getAdminId())->findOrFail($id);

        $request->validate([
            'low_stock_threshold' => 'required|integer|min:0',
            'good_stock_threshold' => 'required|integer|gte:low_stock_threshold',
            'overstock_threshold' => 'required|integer|gte:good_stock_threshold',
        ]);

        $product->update([
            'low_stock_threshold' => $request->low_stock_threshold,
            'good_stock_threshold' => $request->good_stock_threshold,
            'overstock_threshold' => $request->overstock_threshold,
        ]);

        \App\Models\StockLog::create([
            'admin_id' => $this->getAdminId(),
            'user_id' => auth()->id(),
            'product_id' => $product->id,
            'action' => 'edit',
            'quantity_change' => 0,
            'notes' => "Thresholds updated via Quick Fix"
        ]);

        return redirect()->back()->with('success', 'Thresholds updated successfully.');
    }
}
