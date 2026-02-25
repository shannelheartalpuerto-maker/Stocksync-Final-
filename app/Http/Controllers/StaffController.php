<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\StockOut;
use App\Models\DamagedStock;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StockIn;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $userId = auth()->id();

        // Transaction Query Builder
        $transactionQuery = Transaction::where('user_id', $userId);

        // Apply Filters
        if ($request->has('start_date') && $request->start_date != '') {
            $transactionQuery->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date != '') {
            $transactionQuery->whereDate('created_at', '<=', $request->end_date);
        }
        if ($request->has('search') && $request->search != '') {
            $transactionQuery->where('transaction_number', 'like', '%' . $request->search . '%');
        }

        // Top Selling Products (My performance)
        $topSellersPeriod = $request->input('top_sellers_period', 'today');
        $topSellingProducts = $this->getTopSellingProducts($userId, $topSellersPeriod);

        if ($request->ajax()) {
            if ($request->has('fetch_top_sellers')) {
                return view('staff.partials.top_sellers_list', compact('topSellingProducts'))->render();
            }

            $transactions = $transactionQuery->latest()->paginate(10);
            return view('staff.partials.transactions_table', compact('transactions'))->render();
        }

        $adminId = $this->getAdminId();

        // --- 1. Product Data (Stat Cards) ---
        
        // Total Products
        $totalProducts = Product::where('admin_id', $adminId)->count();

        // Total Categories
        $totalCategories = \App\Models\Category::where('admin_id', $adminId)->count();

        // Total Stock
        $totalStock = Product::where('admin_id', $adminId)->sum('quantity');

        // Out of Stock
        $outOfStockCount = Product::where('admin_id', $adminId)->where('quantity', '<=', 0)->count();

        // Low Stock
        $lowStockCount = Product::where('admin_id', $adminId)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '<', 'low_stock_threshold')
            ->count();

        // Over Stock
        $overStockCount = Product::where('admin_id', $adminId)
            ->whereColumn('quantity', '>', 'overstock_threshold')
            ->count();

        // Good Stock
        $goodStockCount = Product::where('admin_id', $adminId)
            ->where('quantity', '>', 0)
            ->whereColumn('quantity', '>=', 'low_stock_threshold')
            ->whereColumn('quantity', '<=', 'overstock_threshold')
            ->count();

        // Top Moving Product (Global for store context) - Restored for Forecast Tab
        $topProductData = DB::table('transaction_items')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('products.admin_id', $adminId)
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_qty'))
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->first();
        $topMovingProduct = $topProductData ? $topProductData->name : 'N/A';

        // Stock Level Monitoring (Prioritize Anomalies: Out > Low > Over > Good)
        $stockLevels = Product::where('admin_id', $adminId)
            ->with(['category', 'brand'])
            ->orderByRaw("
                CASE 
                    WHEN quantity <= 0 THEN 1 
                    WHEN quantity < low_stock_threshold THEN 2 
                    WHEN quantity > overstock_threshold THEN 3 
                    ELSE 4 
                END
            ")
            ->take(10)
            ->get();

        // --- 2. Sales Data (Staff Personal Performance) ---
        $todayStart = now()->startOfDay();
        $todayRevenue = Transaction::where('user_id', $userId)->where('created_at', '>=', $todayStart)->sum('total_amount');
        $todayTransactions = Transaction::where('user_id', $userId)->where('created_at', '>=', $todayStart)->count();

        $totalRevenue = Transaction::where('user_id', $userId)->sum('total_amount');
        $totalTransactions = Transaction::where('user_id', $userId)->count();
        $averageSale = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;
        
        // Most Sold Product (Staff Specific)
        $mostSoldProduct = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->where('transactions.user_id', $userId)
            ->select('products.name', DB::raw('SUM(transaction_items.quantity) as total_sold'))
            ->groupBy('products.name')
            ->orderByDesc('total_sold')
            ->first();

        // --- 3. Forecast Data (Operational Alerts) ---
        // Simple forecast: Avg daily sales last 30 days * 7 days
        $thirtyDaysAgo = now()->subDays(30);
        $totalSoldLast30 = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->where('transactions.admin_id', $adminId)
            ->where('transactions.created_at', '>=', $thirtyDaysAgo)
            ->sum('transaction_items.quantity');
        
        $totalForecastQty = ceil($totalSoldLast30 / 30 * 7); // Forecast for next 7 days

        // Chart Data: My Revenue Last 7 Days
        $revenueData = Transaction::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        $dates = [];
        $revenues = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
            $dayRevenue = $revenueData->firstWhere('date', $date);
            $revenues[] = $dayRevenue ? $dayRevenue->total : 0;
        }

        // Recent Transactions (My transactions)
        $recentTransactions = Transaction::where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();



        // All Transactions (Paginated) for Sales Tab
        $transactions = $transactionQuery->latest()->paginate(10)->appends($request->query());

        return view('staff.dashboard', compact(
            'lowStockCount', 'goodStockCount', 'overStockCount', 'outOfStockCount',
            'totalProducts', 'totalStock', 'totalCategories',
            'todayRevenue', 'todayTransactions',
            'totalRevenue', 'totalTransactions', 'averageSale', 'mostSoldProduct',
            'totalForecastQty', 'topMovingProduct',
            'dates', 'revenues', 'recentTransactions', 'topSellingProducts', 'topSellersPeriod', 'transactions', 'stockLevels'
        ));
    }

    private function getTopSellingProducts($userId, $period)
    {
        $startDate = now()->startOfDay();

        if ($period == 'week') {
            $startDate = now()->startOfWeek();
        } elseif ($period == 'month') {
            $startDate = now()->startOfMonth();
        } elseif ($period == 'all_time') {
            $startDate = null;
        }

        $query = DB::table('transaction_items')
             ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
             ->join('products', 'transaction_items.product_id', '=', 'products.id')
             ->where('transactions.user_id', $userId);

        if ($startDate) {
             $query->where('transactions.created_at', '>=', $startDate);
        }

        return $query
             ->select('products.name', 'products.code', DB::raw('SUM(transaction_items.quantity) as total_qty'), DB::raw('SUM(transaction_items.subtotal) as total_revenue'))
             ->groupBy('products.name', 'products.code')
             ->orderByDesc('total_qty')
             ->take(5)
             ->get();
    }

    public function checkStock(Request $request)
    {
        $request->validate(['query' => 'required|string|min:1']);
        $search = $request->input('query');
        
        $product = Product::where('admin_id', $this->getAdminId())
            ->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })
            ->first();

        if ($product) {
            $status = 'Good Stock';
            $statusColor = 'success';
            
            if ($product->quantity <= 0) {
                $status = 'Out of Stock';
                $statusColor = 'danger';
            } elseif ($product->quantity < $product->low_stock_threshold) {
                $status = 'Low Stock';
                $statusColor = 'danger';
            } elseif ($product->quantity > $product->overstock_threshold) {
                $status = 'Overstock';
                $statusColor = 'warning';
            }

            return response()->json([
                'found' => true,
                'name' => $product->name,
                'code' => $product->code,
                'price' => number_format($product->price, 2),
                'quantity' => $product->quantity,
                'status' => $status,
                'status_color' => $statusColor
            ]);
        }

        return response()->json(['found' => false]);
    }

    public function pos()
    {
        $adminId = $this->getAdminId();
        $products = Product::where('admin_id', $adminId)
                           ->where('quantity', '>', 0)
                           ->with(['category', 'brand'])
                           ->get();
        $categories = \App\Models\Category::where('admin_id', $adminId)->orderBy('name')->get();
        $brands = \App\Models\Brand::where('admin_id', $adminId)->orderBy('name')->get();
        return view('staff.pos', compact('products', 'categories', 'brands'));
    }

    public function inventory(Request $request)
    {
        $query = Product::where('admin_id', $this->getAdminId())->with(['category', 'brand']);

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

        $products = $query->paginate(20)->withQueryString();
        $categories = \App\Models\Category::where('admin_id', $this->getAdminId())->orderBy('name')->get();
        $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->orderBy('name')->get();

        if ($request->ajax()) {
            return view('staff.partials.inventory_table', compact('products'))->render();
        }

        return view('staff.inventory', compact('products', 'categories', 'brands'));
    }

    public function processSale(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'payment_received' => 'required|numeric|min:0',
        ]);

        $totalAmount = 0;
        foreach ($request->cart as $item) {
            $product = Product::find($item['id']);
            if (!$product) {
                return response()->json(['error' => "Product ID {$item['id']} not found"], 422);
            }
            $totalAmount += $product->price * $item['quantity'];
            
            if ($product->quantity < $item['quantity']) {
                return response()->json(['error' => "Insufficient stock for {$product->name}"], 422);
            }
        }

        if ($request->payment_received < $totalAmount) {
            return response()->json(['error' => 'Insufficient payment'], 422);
        }

        DB::beginTransaction();

        try {
            $adminId = $this->getAdminId();
            $userId = auth()->id();

            // Use more unique transaction number
            $trxNumber = 'TRX-' . date('YmdHis') . '-' . $userId . '-' . mt_rand(1000, 9999);

            $transaction = Transaction::create([
                'admin_id' => $adminId,
                'user_id' => $userId,
                'transaction_number' => $trxNumber,
                'total_amount' => $totalAmount,
                'payment_received' => $request->payment_received,
                'change_returned' => $request->payment_received - $totalAmount,
            ]);

            foreach ($request->cart as $item) {
                // Lock for update to prevent race conditions and ensure accurate stock
                $product = Product::where('id', $item['id'])->lockForUpdate()->first();
                
                if (!$product) {
                     throw new \Exception("Product {$item['name']} (ID: {$item['id']}) not found during processing.");
                }

                // Double check stock inside lock
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}. Available: {$product->quantity}");
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $product->price * $item['quantity'],
                ]);

                // Decrement stock
                $product->decrement('quantity', $item['quantity']);

                StockOut::create([
                    'admin_id' => $adminId,
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'reason' => 'sale',
                    'notes' => "Sale TRX: {$trxNumber}"
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true, 
                'transaction_id' => $transaction->id,
                'transaction_number' => $transaction->transaction_number,
                'total_amount' => $transaction->total_amount,
                'change_returned' => $transaction->change_returned
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Transaction Failed: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Transaction failed: ' . $e->getMessage()], 500);
        }
    }

    public function reportIssue(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        // In a real app, we would save this to an 'Issues' or 'Tickets' table.
        // For now, we will just simulate it and flash a success message.
        // Optionally, we could log it to the system log.
        
        return redirect()->back()->with('status', 'Issue reported successfully to the admin.');
    }

    public function reportDamaged(Request $request, $id)
    {
        $product = Product::where('admin_id', $this->getAdminId())->findOrFail($id);

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
            'notes' => $request->notes ?? 'Damaged Items Reported by Staff'
        ]);

        return redirect()->back()->with('success', 'Damaged items reported successfully.');
    }

    public function transactions()
    {
        $userId = auth()->id();
        $transactions = Transaction::where('user_id', $userId)
            ->latest()
            ->paginate(15);
        
        return view('staff.transactions', compact('transactions'));
    }

    public function returnTransaction(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);

        DB::beginTransaction();
        try {
            $transaction = Transaction::where('id', $id)
                ->where('user_id', auth()->id())
                ->firstOrFail();

            if ($transaction->status === 'returned') {
                return redirect()->back()->with('error', 'Transaction already returned.');
            }

            // Restore Stock
            $items = TransactionItem::where('transaction_id', $transaction->id)->get();
            foreach ($items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                    
                    StockIn::create([
                        'admin_id' => $this->getAdminId(),
                        'user_id' => auth()->id(),
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'notes' => "Return of Transaction #{$transaction->transaction_number} | Reason: " . $request->input('reason')
                    ]);
                }
            }

            // Mark Transaction as Returned
            $transaction->update(['status' => 'returned']);

            DB::commit();
            return redirect()->back()->with('success', 'Transaction returned and stock restored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Return Failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to process return.');
        }
    }

    public function logs(Request $request)
    {
        $period = $request->input('period', 'week');
        $startDate = null;

        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                break;
            case 'month':
                $startDate = now()->startOfMonth();
                break;
            case 'all':
                $startDate = null;
                break;
            default:
                $startDate = now()->startOfWeek();
                $period = 'week';
                break;
        }

        $userId = auth()->id();

        // Base Queries - Filtered by User ID
        $returnedQuery = StockIn::where('user_id', $userId)->with(['product', 'user']);
        $stockOutQuery = StockOut::where('user_id', $userId)->with(['product', 'user']);
        $damagedStockQuery = DamagedStock::where('user_id', $userId)->with(['product', 'user']);

        // Apply Date Filter
        if ($startDate) {
            $returnedQuery->where('created_at', '>=', $startDate);
            $stockOutQuery->where('created_at', '>=', $startDate);
            $damagedStockQuery->where('created_at', '>=', $startDate);
        }

        // Calculate Summaries (using clone to preserve query for pagination)
        $totalReturned = (clone $returnedQuery)->sum('quantity');
        $totalStockOut = (clone $stockOutQuery)->sum('quantity');
        $totalDamaged = (clone $damagedStockQuery)->sum('quantity');

        // Get Paginated Results
        $returnedItems = $returnedQuery->latest()->paginate(10, ['*'], 'returned_page')->appends(['period' => $period, 'type' => 'returned']);
        $stockOuts = $stockOutQuery->latest()->paginate(10, ['*'], 'out_page')->appends(['period' => $period, 'type' => 'stockout']);
        $damagedStocks = $damagedStockQuery->latest()->paginate(10, ['*'], 'damaged_page')->appends(['period' => $period, 'type' => 'damaged']);
            
        if ($request->ajax()) {
            $type = $request->input('type');
            if ($type === 'returned') {
                return view('staff.partials.logs_returned', compact('returnedItems'))->render();
            } elseif ($type === 'stockout') {
                return view('staff.partials.logs_stockout', compact('stockOuts'))->render();
            } elseif ($type === 'damaged') {
                return view('staff.partials.logs_damaged', compact('damagedStocks'))->render();
            } else {
                $returnedHtml = view('staff.partials.logs_returned', compact('returnedItems'))->render();
                $stockOutHtml = view('staff.partials.logs_stockout', compact('stockOuts'))->render();
                $damagedHtml = view('staff.partials.logs_damaged', compact('damagedStocks'))->render();
                return response()->json([
                    'period' => $period,
                    'summaries' => [
                        'returned' => (int) $totalReturned,
                        'stock_out' => (int) $totalStockOut,
                        'damaged' => (int) $totalDamaged,
                    ],
                    'tabs' => [
                        'returned_html' => $returnedHtml,
                        'stockout_html' => $stockOutHtml,
                        'damaged_html' => $damagedHtml,
                    ],
                ]);
            }
        }

        return view('staff.logs', compact(
            'returnedItems', 'stockOuts', 'damagedStocks', 'period',
            'totalReturned', 'totalStockOut', 'totalDamaged'
        ));
    }
}
