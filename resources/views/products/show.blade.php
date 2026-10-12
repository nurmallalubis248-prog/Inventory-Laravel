@extends('layouts.app')

@section('content')
<div class="card">
    <h3>Detail Produk</h3>
    <table>
        <tbody>
            <tr><th style="width:200px;">Kode (SKU)</th><td>{{ $product->sku }}</td></tr>
            <tr><th>Nama Produk</th><td><strong>{{ $product->name }}</strong></td></tr>
            <tr><th>Kategori</th><td>{{ $product->category->name ?? 'Tanpa Kategori' }}</td></tr>
            <tr>
                <th>Stok</th>
                <td>
                    <strong @if ($product->isLowStock()) style="color:#e11d48;" @endif>{{ $product->stock }}</strong>
                    @if ($product->isLowStock()) <small style="color:#e11d48;">(menipis)</small> @endif
                </td>
            </tr>
            <tr><th>Stok Minimum</th><td>{{ $product->minimum_stock }}</td></tr>
            <tr><th>Harga</th><td>Rp {{ number_format($product->price, 0, ',', '.') }}</td></tr>
        </tbody>
    </table>
    <p style="margin-top:20px;">
        <a href="{{ route('products.edit', $product) }}" class="btn-pink">Ubah</a>
        <a href="{{ route('products.index') }}" class="cancel-link">Kembali ke daftar</a>
    </p>
</div>

<div class="card">
    <h3>10 Transaksi Stok Terakhir</h3>
    <table>
        <thead><tr><th>TANGGAL</th><th>TIPE</th><th>JUMLAH</th><th>SUPPLIER</th><th>REFERENSI</th></tr></thead>
        <tbody>
            @forelse ($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->type === 'IN' ? 'Masuk' : 'Keluar' }}</td>
                    <td>{{ $m->qty }}</td>
                    <td>{{ $m->supplier->name ?? '-' }}</td>
                    <td>{{ $m->reference ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted-empty">Belum ada transaksi stok.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
