{{-- Form bersama untuk create dan edit. Variabel: $categories, $product (opsional) --}}
@php $editing = isset($product); @endphp

<div class="form-grid">
    <div class="form-group">
        <label>Kode Produk (SKU)</label>
        <input type="text" name="sku" value="{{ old('sku', $product->sku ?? '') }}" required autocomplete="off">
        @error('sku') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Nama Produk</label>
        <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" required autocomplete="off">
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Kategori</label>
        <select name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id ?? '') == $c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Stok {{ $editing ? '(ubah lewat Barang Masuk / Keluar)' : '(stok awal)' }}</label>
        @if ($editing)
            <input type="number" value="{{ $product->stock }}" disabled>
        @else
            <input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" required>
            @error('stock') <div class="field-error">{{ $message }}</div> @enderror
        @endif
    </div>
    <div class="form-group">
        <label>Stok Minimum</label>
        <input type="number" name="minimum_stock" value="{{ old('minimum_stock', $product->minimum_stock ?? 5) }}" min="0" required>
        @error('minimum_stock') <div class="field-error">{{ $message }}</div> @enderror
    </div>
    <div class="form-group">
        <label>Harga (Rp)</label>
        <input type="number" name="price" value="{{ old('price', $editing ? (int) $product->price : 0) }}" min="0" step="0.01" required>
        @error('price') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>
