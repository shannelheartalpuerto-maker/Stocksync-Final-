<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = \App\Models\Category::where('admin_id', $this->getAdminId())->withCount('products')->get();
        $brands = \App\Models\Brand::where('admin_id', $this->getAdminId())->withCount('products')->get();
        if (request()->ajax()) {
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
        return view('admin.categories.index', compact('categories', 'brands'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        \App\Models\Category::create([
            'name' => $request->name,
            'admin_id' => $this->getAdminId()
        ]);
        if ($request->ajax()) {
            return $this->index();
        }
        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function update(Request $request, $id)
    {
        $category = \App\Models\Category::where('admin_id', $this->getAdminId())->findOrFail($id);
        $request->validate(['name' => 'required|string|max:255']);
        $category->update(['name' => $request->name]);
        if ($request->ajax()) {
            return $this->index();
        }
        return redirect()->back()->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = \App\Models\Category::where('admin_id', $this->getAdminId())->findOrFail($id);
        $category->delete();
        if (request()->ajax()) {
            return $this->index();
        }
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
