<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Services\StockService;
use Illuminate\Http\Request;

/**
 * Basis untuk Barang Masuk (IN) dan Barang Keluar (OUT).
 * Aturan stok ada di StockService.
 */
abstract class StockMovementController extends Controller
{
    protected string $type;
    protected string $prefix;
    protected string $defaultReference;

    public function __construct(protected StockService $stock)
    {
    }

    public function index(Request $request)
    {
        return $this->page($request);
    }

    public function edit(Request $request, StockMovement $movement)
    {
        abort_unless($movement->type === $this->type, 404);

        return $this->page($request, $movement);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['type'] = $this->type;

        $this->stock->record($data);

        return redirect()->route($this->prefix . '.index')->with('success', 'Transaksi berhasil disimpan.');
    }

    public function update(Request $request, StockMovement $movement)
    {
        abort_unless($movement->type === $this->type, 404);

        $this->stock->update($movement, $this->validated($request));

        return redirect()->route($this->prefix . '.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(StockMovement $movement)
    {
        abort_unless($movement->type === $this->type, 404);

        $this->stock->remove($movement);

        return redirect()->route($this->prefix . '.index')->with('success', 'Transaksi berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'product_id'  => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'qty'         => ['required', 'integer', 'min:1'],
            'reference'   => ['nullable', 'string', 'max:100'],
        ]);

        $data['reference'] = $data['reference'] ?? $this->defaultReference;

        return $data;
    }

    private function page(Request $request, ?StockMovement $editing = null)
    {
        $movements = StockMovement::with(['product', 'supplier'])
            ->where('type', $this->type)
            ->when($request->search, function ($q, $s) {
                $q->whereHas('product', fn ($p) => $p->where('name', 'like', "%{$s}%"));
            })
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('stock.index', [
            'type'      => $this->type,
            'prefix'    => $this->prefix,
            'movements' => $movements,
            'products'  => Product::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'editing'   => $editing,
        ]);
    }
}
