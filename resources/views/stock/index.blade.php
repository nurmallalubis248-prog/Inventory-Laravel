@extends('layouts.app')

@php
    $isIn = $type === 'IN';
    $label = $isIn ? 'Masuk' : 'Keluar';
@endphp

@section('content')
@error('qty')
    <x-alert type="error">{{ $message }}</x-alert>
@enderror

<div class="card">
    <h3>{{ $editing ? "Edit Barang {$label}" : "Catat Barang {$label}" }}</h3>
    <form method="POST" action="{{ $editing ? route($prefix . '.update', $editing) : route($prefix . '.store') }}">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="form-grid">
            <div class="form-group">
                <label>Pilih Produk</label>
                <select name="product_id" required>
                    <option value="">-- Pilih Produk --</option>
                    @foreach ($products as $p)
                        <option value="{{ $p->id }}" @selected(old('product_id', $editing->product_id ?? '') == $p->id)>{{ $p->name }} (Stok: {{ $p->stock }})</option>
                    @endforeach
                </select>
                @error('product_id') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Jumlah {{ $label }} (Qty)</label>
                <input type="number" name="qty" value="{{ old('qty', $editing->qty ?? 1) }}" min="1" required>
            </div>
            @if ($isIn)
                <div class="form-group">
                    <label>Supplier (opsional)</label>
                    <select name="supplier_id">
                        <option value="">-- Tanpa Supplier --</option>
                        @foreach ($suppliers as $s)
                            <option value="{{ $s->id }}" @selected(old('supplier_id', $editing->supplier_id ?? '') == $s->id)>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('supplier_id') <div class="field-error">{{ $message }}</div> @enderror
                </div>
            @endif
            <div class="form-group">
                <label>{{ $isIn ? 'Keterangan' : 'Keterangan / Keperluan' }}</label>
                <input type="text" name="reference" value="{{ old('reference', $editing->reference ?? "Barang {$label}") }}" required>
                @error('reference') <div class="field-error">{{ $message }}</div> @enderror
            </div>
        </div>
        <button type="submit" class="btn-pink">
            {{ $editing ? "Perbarui Barang {$label}" : ($isIn ? 'Tambah Stok Masuk' : 'Kurangi Stok Keluar') }}
        </button>
        @if ($editing)
            <a href="{{ route($prefix . '.index') }}" class="cancel-link">Batal</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="search-bar">
        <h3>Riwayat Barang {{ $label }}</h3>
        <form method="GET" action="{{ route($prefix . '.index') }}">
            <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
        </form>
    </div>
    <table>
        <thead>
            <tr>
                <th>NO</th><th>PRODUK</th><th>JUMLAH</th>
                @if ($isIn) <th>SUPPLIER</th> @endif
                <th>KETERANGAN</th><th>TANGGAL</th><th>AKSI</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($movements as $m)
                <tr>
                    <td>{{ $movements->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $m->product->name }}</strong></td>
                    <td>
                        @if ($isIn)
                            <strong style="color:#db2777;">+{{ $m->qty }}</strong>
                        @else
                            <strong style="color:#e11d48;">-{{ $m->qty }}</strong>
                        @endif
                    </td>
                    @if ($isIn) <td>{{ $m->supplier->name ?? '-' }}</td> @endif
                    <td>{{ $m->reference }}</td>
                    <td>{{ $m->created_at->format('Y-m-d') }}</td>
                    <td>
                        <a href="{{ route($prefix . '.edit', $m) }}" class="action-btn edit">Ubah</a>
                        <form method="POST" action="{{ route($prefix . '.destroy', $m) }}" class="inline-form" onsubmit="return confirm('Hapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn del link-btn">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="{{ $isIn ? 7 : 6 }}" class="muted-empty">Belum ada riwayat barang {{ strtolower($label) }}.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $movements->links() }}
</div>
@endsection
