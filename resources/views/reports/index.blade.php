@extends('layouts.app')

@section('content')
<div class="card">
    <h3>Laporan Stok &amp; Valuasi Aset Inventory</h3>
    <div style="background:#fdf2f8; border:1px solid #fbcfe8; padding:20px; border-radius:8px; margin-bottom:24px; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <p style="font-size:13px; color:#881337; font-weight:600; text-transform:uppercase;">Total Keseluruhan Valuasi Aset</p>
            <h2 style="color:#db2777; font-size:28px; margin-top:4px;">Rp {{ number_format($totalVal, 0, ',', '.') }}</h2>
        </div>
        <div>
            <span style="background: #db2777; color: white; padding: 8px 16px; border-radius: 20px; font-size: 13px; font-weight: 600;">
                Total Jenis Barang: {{ $products->count() }}
            </span>
        </div>
    </div>
    <table>
        <thead><tr><th>NO</th><th>NAMA PRODUK</th><th>KATEGORI</th><th>STOK</th><th>HARGA SATUAN</th><th>TOTAL NILAI</th></tr></thead>
        <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ $p->category->name ?? 'Tanpa Kategori' }}</td>
                    <td><strong>{{ $p->stock }}</strong></td>
                    <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($p->stock * $p->price, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted-empty">Belum ada data produk untuk dilaporkan.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
