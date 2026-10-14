<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ], [
            'name.unique' => 'Kategori dengan nama tersebut sudah ada.',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')
                         ->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan!");
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:100', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')
                         ->with('success', "Kategori '{$category->name}' berhasil diperbarui!");
    }

    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('categories.index')
                             ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh produk!");
        }

        $category->delete();

        return redirect()->route('categories.index')
                         ->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }
}
