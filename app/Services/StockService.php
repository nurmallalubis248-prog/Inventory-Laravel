<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Semua perubahan stok lewat sini, dibungkus DB transaction
 * (business rule: stok tidak boleh negatif, histori dan stok harus sinkron).
 */
class StockService
{
    public function record(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $product = Product::lockForUpdate()->findOrFail($data['product_id']);
            $this->apply($product, $data['type'], (int) $data['qty']);

            return StockMovement::create($data);
        });
    }

    public function update(StockMovement $movement, array $data): StockMovement
    {
        return DB::transaction(function () use ($movement, $data) {
            $movement = StockMovement::lockForUpdate()->findOrFail($movement->id);

            // 1. Batalkan efek transaksi lama
            $old = Product::lockForUpdate()->findOrFail($movement->product_id);
            $this->apply($old, $movement->type === 'IN' ? 'OUT' : 'IN', (int) $movement->qty);

            // 2. Terapkan efek transaksi baru (baca ulang agar stok terbaru terpakai)
            $new = Product::lockForUpdate()->findOrFail($data['product_id']);
            $this->apply($new, $movement->type, (int) $data['qty']);

            $movement->update($data);

            return $movement;
        });
    }

    public function remove(StockMovement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $movement = StockMovement::lockForUpdate()->findOrFail($movement->id);
            $product = Product::lockForUpdate()->findOrFail($movement->product_id);

            $this->apply($product, $movement->type === 'IN' ? 'OUT' : 'IN', (int) $movement->qty);

            $movement->delete();
        });
    }

    private function apply(Product $product, string $type, int $qty): void
    {
        if ($type === 'OUT') {
            if ($qty > $product->stock) {
                throw ValidationException::withMessages([
                    'qty' => "Gagal! Stok produk '{$product->name}' tidak mencukupi. Sisa stok saat ini: {$product->stock}.",
                ]);
            }
            $product->decrement('stock', $qty);
        } else {
            $product->increment('stock', $qty);
        }
    }
}
