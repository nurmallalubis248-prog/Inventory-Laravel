@extends('layouts.app')

@section('content')
<div class="card">
    <h3>{{ $editing ? 'Edit Kategori' : 'Tambah Kategori Baru' }}</h3>
    <form method="POST" action="{{ $editing ? route('categories.update', $editing) : route('categories.store') }}">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="form-group" style="max-width: 400px; margin-bottom: 12px;">
            <label>Nama Kategori</label>
            <input type="text" name="name" value="{{ old('name', $editing->name ?? '') }}" placeholder="Contoh: Elektronik" required autocomplete="off">
            @error('name') <div class="field-error">{{ $message }}</div> @enderror
        </div>
        <button type="submit" class="btn-pink">{{ $editing ? 'Perbarui Kategori' : 'Simpan Kategori' }}</button>
        @if ($editing)
            <a href="{{ route('categories.index') }}" class="cancel-link">Batal</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="search-bar">
        <h3>Daftar Kategori</h3>
        <form method="GET" action="{{ route('categories.index') }}">
            <input type="text" name="search" placeholder="Cari kategori..." value="{{ request('search') }}">
        </form>
    </div>
    <table>
        <thead><tr><th>NO</th><th>NAMA KATEGORI</th><th>AKSI</th></tr></thead>
        <tbody>
            @forelse ($categories as $c)
                <tr>
                    <td>{{ $categories->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td>
                        <a href="{{ route('categories.edit', $c) }}" class="action-btn edit">Ubah</a>
                        <form method="POST" action="{{ route('categories.destroy', $c) }}" class="inline-form" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="action-btn del link-btn">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="muted-empty">Belum ada data kategori.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $categories->links() }}
</div>
@endsection
