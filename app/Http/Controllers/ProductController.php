<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Resource controller produk: index, create, store, show, edit, update, destroy.
 * Alur: Request -> Route -> Controller -> Model (Eloquent) -> Database -> Response (view/redirect).
 */
class ProductController extends Controller
{
    // GET /products : daftar produk + search, filter kategori, sorting, pagination
    public function index(Request $request)
    {
        $sortable = ['id', 'sku', 'name', 'price', 'stock'];
        $sort = in_array($request->sort, $sortable, true) ? $request->sort : 'id';
        $direction = $request->direction === 'asc' ? 'asc' : 'desc';

        $products = Product::with('category')
            ->when($request->search, function ($q, $s) {
                $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%"));
            })
            ->when($request->category_id, fn ($q, $c) => $q->where('category_id', $c))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('products.index', [
            'products'   => $products,
            'categories' => Category::orderBy('name')->get(),
            'sort'       => $sort,
            'direction'  => $direction,
        ]);
    }

    // GET /products/create : form tambah produk
    public function create()
    {
        return view('products.create', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    // POST /products : validasi -> simpan -> redirect
    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id'   => ['required', 'exists:categories,id'],
            'sku'           => ['required', 'string', 'max:50', 'unique:products,sku'],
            'name'          => ['required', 'string', 'max:150'],
            'price'         => ['required', 'numeric', 'min:0'],
            'stock'         => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            $product = Product::create($data);

            // Stok awal dicatat sebagai barang masuk supaya riwayat dan stok selalu sinkron
            if ($product->stock > 0) {
                $product->stockMovements()->create([
                    'type' => 'IN',
                    'qty' => $product->stock,
                    'reference' => 'Stok awal',
                ]);
            }
        });

        return redirect()->route('products.index')->with('success', 'Produk berhasil disimpan.');
    }

    // GET /products/{product} : detail produk + 10 transaksi stok terakhir
    public function show(Product $product)
    {
        $product->load('category');

        return view('products.show', [
            'product'   => $product,
            'movements' => $product->stockMovements()->with('supplier')->latest()->limit(10)->get(),
        ]);
    }

    // GET /products/{product}/edit : form edit produk
    public function edit(Product $product)
    {
        return view('products.edit', [
            'product'    => $product,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    // PUT /products/{product} : validasi -> perbarui -> redirect
    public function update(Request $request, Product $product)
    {
        // Stok tidak diubah di sini; stok hanya berubah lewat Barang Masuk / Keluar
        $product->update($request->validate([
            'category_id'   => ['required', 'exists:categories,id'],
            'sku'           => ['required', 'string', 'max:50', Rule::unique('products', 'sku')->ignore($product->id)],
            'name'          => ['required', 'string', 'max:150'],
            'price'         => ['required', 'numeric', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
        ]));

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    // DELETE /products/{product}
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
