@extends('layouts.app')

@section('content')
<div class="card">
    <h3>{{ $editing ? 'Edit Pengguna' : 'Tambah Pengguna Sistem Baru' }}</h3>
    <form method="POST" action="{{ $editing ? route('users.update', $editing) : route('users.store') }}">
        @csrf
        @if ($editing) @method('PUT') @endif

        <div class="form-grid">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" value="{{ old('username', $editing->username ?? '') }}" required autocomplete="off">
                @error('username') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label>Password {{ $editing ? '(Kosongkan jika tidak diubah)' : '' }}</label>
                <input type="password" name="password" placeholder="Masukkan password" @if (! $editing) required @endif autocomplete="new-password">
                @error('password') <div class="field-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Hak Akses (Role)</label>
                <select name="role">
                    <option value="staff" @selected(old('role', $editing->role ?? 'staff') === 'staff')>Staff</option>
                    <option value="admin" @selected(old('role', $editing->role ?? 'staff') === 'admin')>Admin</option>
                </select>
                @error('role') <div class="field-error">{{ $message }}</div> @enderror
            </div>
        </div>
        <button type="submit" class="btn-pink">{{ $editing ? 'Perbarui Pengguna' : 'Simpan Pengguna' }}</button>
        @if ($editing)
            <a href="{{ route('users.index') }}" class="cancel-link">Batal</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="search-bar">
        <h3>Daftar Pengguna Terdaftar</h3>
        <form method="GET" action="{{ route('users.index') }}">
            <input type="text" name="search" placeholder="Cari username..." value="{{ request('search') }}">
        </form>
    </div>
    <table>
        <thead><tr><th>NO</th><th>USERNAME</th><th>ROLE</th><th>AKSI</th></tr></thead>
        <tbody>
            @foreach ($users as $u)
                <tr>
                    <td>{{ $users->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $u->username }}</strong></td>
                    <td>
                        <span style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; {{ $u->role === 'admin' ? 'background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8;' : 'background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0;' }}">
                            {{ strtoupper($u->role) }}
                        </span>
                    </td>
                    <td>
                        @if ($u->id !== auth()->id())
                            <a href="{{ route('users.edit', $u) }}" class="action-btn edit">Ubah</a>
                            <form method="POST" action="{{ route('users.destroy', $u) }}" class="inline-form" onsubmit="return confirm('Hapus pengguna ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="action-btn del link-btn">Hapus</button>
                            </form>
                        @else
                            <span style="color: #94a3b8; font-size: 13px;">(Akun Anda)</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $users->links() }}
</div>
@endsection
