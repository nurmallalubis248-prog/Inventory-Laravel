@extends('layouts.app')

@section('content')
<div class="card">
    <h3>{{ $editing ? 'Edit Supplier' : 'Tambah Supplier Baru' }}</h3>
    <form method="POST" action="{{ $editing ? route('suppliers.update', $editing) : route('suppliers.store') }}">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="form-grid">
            <div class="form-group">
                <label>Nama Supplier</label>
                <input type="text" name="name" value="{{ old('name', $editing->name ?? '') }}" required autocomplete="off">
                @error('name') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>No Telepon</label>
                <input type="text" name="phone" value="{{ old('phone', $editing->phone ?? '') }}" autocomplete="off">
                @error('phone') <div class="field-error">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="form-group" style="margin-bottom: 12px;">
            <label>Alamat</label>
            <input type="text" name="address" value="{{ old('address', $editing->address ?? '') }}" autocomplete="off">
            @error('address') <div class="field-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn-pink">{{ $editing ? 'Perbarui Supplier' : 'Simpan Supplier' }}</button>
        @if ($editing)
            <a href="{{ route('suppliers.index') }}" class="cancel-link">Batal</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="search-bar">
        <h3>Daftar Supplier</h3>
        <form method="GET" action="{{ route('suppliers.index') }}">
            <input type="text" name="search" placeholder="Cari supplier..." value="{{ request('search') }}">
        </form>
    </div>
    <table>
        <thead><tr><th>NO</th><th>NAMA</th><th>TELEPON</th><th>ALAMAT</th><th>AKSI</th></tr></thead>
        <tbody>
            @forelse ($suppliers as $s)
                <tr>
                    <td>{{ $suppliers->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $s->name }}</strong></td>
                    <td>{{ $s->phone }}</td>
                    <td>{{ $s->address }}</td>
                    <td>
                        <a href="{{ route('suppliers.edit', $s) }}" class="action-btn edit">Ubah</a>
                        <form method="POST" action="{{ route('suppliers.destroy', $s) }}" class="inline-form" onsubmit="return confirm('Hapus supplier ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn del link-btn">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted-empty">Belum ada data supplier.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $suppliers->links() }}
</div>
@endsection
