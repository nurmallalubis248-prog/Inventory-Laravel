@extends('layouts.app')

@section('content')
<div class="card">
    <div class="search-bar">
        <h3>Semua Riwayat Transaksi Stok</h3>
        <form method="GET" action="{{ route('history') }}" class="filters">
            <input type="text" name="search" placeholder="Cari produk..." value="{{ request('search') }}">
            <select name="type" onchange="this.form.submit()">
                <option value="">Semua Tipe</option>
                <option value="IN" @selected(request('type') === 'IN')>Masuk</option>
                <option value="OUT" @selected(request('type') === 'OUT')>Keluar</option>
            </select>
        </form>
    </div>
    <table>
        <thead><tr><th>NO</th><th>PRODUK</th><th>TIPE</th><th>JUMLAH</th><th>KETERANGAN</th><th>TANGGAL</th></tr></thead>
        <tbody>
            @forelse ($movements as $m)
                <tr>
                    <td>{{ $movements->firstItem() + $loop->index }}</td>
                    <td><strong>{{ $m->product->name }}</strong></td>
                    <td>
                        <span style="padding:4px 8px; border-radius:4px; font-weight:bold; font-size:12px; {{ $m->type === 'IN' ? 'background:#dcfce7;color:#166534;' : 'background:#fee2e2;color:#991b1b;' }}">
                            {{ $m->type === 'IN' ? 'MASUK' : 'KELUAR' }}
                        </span>
                    </td>
                    <td><strong>{{ $m->qty }}</strong></td>
                    <td>{{ $m->reference }}</td>
                    <td>{{ $m->created_at->format('Y-m-d') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="muted-empty">Belum ada transaksi tercatat.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $movements->links() }}
</div>
@endsection
