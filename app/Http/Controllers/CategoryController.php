<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        return $this->page($request);
    }

    public function edit(Request $request, Category $category)
    {
        return $this->page($request, $category);
    }

    public function store(Request $request)
    {
        Category::create($request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil disimpan.');
    }

    public function update(Request $request, Category $category)
    {
        $category->update($request->validate([
            'name' => ['required', 'string', 'max:100'],
        ]));

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Business rule: kategori yang masih dipakai produk tidak boleh dihapus
        if ($category->products()->exists()) {
            return redirect()->route('categories.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih dipakai oleh produk.');
        }

        $category->delete();

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }

    private function page(Request $request, ?Category $editing = null)
    {
        $categories = Category::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('categories.index', compact('categories', 'editing'));
    }
}
