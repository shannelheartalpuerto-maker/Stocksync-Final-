<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        // This method might not be used if we display brands in categories index
        // But good to have for API or standalone view if needed
        $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->get();
        return view('admin.brands.index', compact('brands'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        \App\Models\Brand::create([
            'name' => $request->name,
            'admin_id' => $this->getAdminId()
        ]);
        if ($request->ajax()) {
            $categories = \App\Models\Category::where('admin_id', $this->getAdminId())->withCount('products')->get();
            $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->withCount('products')->get();
            $categoriesRows = view('admin.categories.partials.categories_rows', compact('categories'))->render();
            $brandsRows = view('admin.categories.partials.brands_rows', compact('brands'))->render();
            $categoriesModals = view('admin.categories.partials.categories_modals', compact('categories'))->render();
            $brandsModals = view('admin.categories.partials.brands_modals', compact('brands'))->render();
            return response()->json([
                'categories_rows_html' => $categoriesRows,
                'brands_rows_html' => $brandsRows,
                'categories_modals_html' => $categoriesModals,
                'brands_modals_html' => $brandsModals,
            ]);
        }
        return redirect()->back()->with('success', 'Brand created successfully.');
    }

    public function update(Request $request, $id)
    {
        $brand = \App\Models\Brand::where('admin_id', $this->getAdminId())->findOrFail($id);
        $request->validate(['name' => 'required|string|max:255']);
        $brand->update(['name' => $request->name]);
        if ($request->ajax()) {
            $categories = \App\Models\Category::where('admin_id', $this->getAdminId())->withCount('products')->get();
            $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->withCount('products')->get();
            $categoriesRows = view('admin.categories.partials.categories_rows', compact('categories'))->render();
            $brandsRows = view('admin.categories.partials.brands_rows', compact('brands'))->render();
            $categoriesModals = view('admin.categories.partials.categories_modals', compact('categories'))->render();
            $brandsModals = view('admin.categories.partials.brands_modals', compact('brands'))->render();
            return response()->json([
                'categories_rows_html' => $categoriesRows,
                'brands_rows_html' => $brandsRows,
                'categories_modals_html' => $categoriesModals,
                'brands_modals_html' => $brandsModals,
            ]);
        }
        return redirect()->back()->with('success', 'Brand updated successfully.');
    }

    public function destroy($id)
    {
        $brand = \App\Models\Brand::where('admin_id', $this->getAdminId())->findOrFail($id);
        $brand->delete();
        if (request()->ajax()) {
            $categories = \App\Models\Category::where('admin_id', $this->getAdminId())->withCount('products')->get();
            $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->withCount('products')->get();
            $categoriesRows = view('admin.categories.partials.categories_rows', compact('categories'))->render();
            $brandsRows = view('admin.categories.partials.brands_rows', compact('brands'))->render();
            $categoriesModals = view('admin.categories.partials.categories_modals', compact('categories'))->render();
            $brandsModals = view('admin.categories.partials.brands_modals', compact('brands'))->render();
            return response()->json([
                'categories_rows_html' => $categoriesRows,
                'brands_rows_html' => $brandsRows,
                'categories_modals_html' => $categoriesModals,
                'brands_modals_html' => $brandsModals,
            ]);
        }
        return redirect()->back()->with('success', 'Brand deleted successfully.');
    }
}
