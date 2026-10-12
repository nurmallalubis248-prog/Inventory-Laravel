@extends('layouts.app')

@section('content')
<div class="card">
    <div class="search-bar">
        <div style="display:flex;align-items:center;gap:14px;"><h3 style="margin-bottom:0;">Daftar Produk</h3><a href="{{ route('products.create') }}" class="btn-pink">+ Tambah Produk</a></div>
        <form method="GET" action="{{ route('products.index') }}" class="filters">
            <input type="text" name="search" placeholder="Cari nama / kode..." value="{{ request('search') }}">
            <select name="category_id">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" @selected(request('category_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
            <select name="sort">
                <option value="id" @selected($sort === 'id')>Urut: Terbaru</option>
                <option value="name" @selected($sort === 'name')>Urut: Nama</option>
                <option value="sku" @selected($sort === 'sku')>Urut: Kode</option>
                <option value="price" @selected($sort === 'price')>Urut: Harga</option>
                <option value="stock" @selected($sort === 'stock')>Urut: Stok</option>
            </select>
            <select name="direction">
                <option value="desc" @selected($direction === 'desc')>Z-A / Besar</option>
                <option value="asc" @selected($direction === 'asc')>A-Z / Kecil</option>
            </select>
            <button type="submit" class="btn-pink">Terapkan</button>
        </form>
    </div>
    <table>
        <thead><tr><th>NO</th><th>KODE</th><th>NAMA PRODUK</th><th>KATEGORI</th><th>STOK</th><th>HARGA</th><th>AKSI</th></tr></thead>
        <tbody>
            @forelse ($products as $p)
                <tr>
                    <td>{{ $products->firstItem() + $loop->index }}</td>
                    <td>{{ $p->sku }}</td>
                    <td><strong>{{ $p->name }}</strong></td>
                    <td>{{ $p->category->name ?? 'Tanpa Kategori' }}</td>
                    <td>
                        <strong @if ($p->isLowStock()) style="color:#e11d48;" @endif>{{ $p->stock }}</strong>
                        @if ($p->isLowStock()) <small style="color:#e11d48;">(menipis)</small> @endif
                    </td>
                    <td>Rp {{ number_format($p->price, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('products.show', $p) }}" class="action-btn">Detail</a>
                        <a href="{{ route('products.edit', $p) }}" class="action-btn edit">Ubah</a>
                        <form method="POST" action="{{ route('products.destroy', $p) }}" class="inline-form" onsubmit="return confirm('Hapus produk ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn del link-btn">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="muted-empty">Belum ada data produk.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $products->links() }}
</div>
@endsection
