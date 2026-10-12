<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Stockify - Inventory System</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #fff1f2; display: flex; height: 100vh; overflow: hidden; color: #334155; }

        /* Sidebar Warna Pink Elegan */
        .sidebar { width: 260px; background-color: #831843; color: #ffffff; display: flex; flex-direction: column; }
        .sidebar-brand { padding: 24px 20px; font-size: 24px; font-weight: bold; background-color: #500724; border-bottom: 1px solid #9f1239; }
        .sidebar-brand span { display: block; font-size: 13px; color: #fbcfe8; font-weight: normal; margin-top: 4px; text-transform: capitalize; }
        .sidebar-menu { list-style: none; padding: 15px 0; flex-grow: 1; overflow-y: auto; }
        .sidebar-menu li a { display: block; padding: 12px 24px; color: #fbcfe8; text-decoration: none; font-size: 15px; transition: 0.2s; border-left: 4px solid transparent; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: #9f1239; color: #ffffff; border-left-color: #f472b6; }
        .sidebar-menu li.logout { margin-top: 10px; }
        .sidebar-menu li.logout button { display: block; width: 100%; text-align: left; padding: 12px 24px; background: none; border: none; border-left: 4px solid transparent; color: #fecdd3; font-family: inherit; font-size: 15px; cursor: pointer; transition: 0.2s; }
        .sidebar-menu li.logout button:hover { background-color: #4c0519; color: #ffffff; border-left-color: #fb7185; }

        /* Main Content */
        .main-content { flex-grow: 1; display: flex; flex-direction: column; overflow-y: auto; background: #fffdfd; }
        .topbar { background: #ffffff; padding: 20px 30px; border-bottom: 1px solid #fce7f3; display: flex; justify-content: space-between; align-items: center; }
        .topbar h1 { font-size: 22px; color: #831843; }
        .user-badge { background: #fdf2f8; color: #be185d; border: 1px solid #fbcfe8; padding: 6px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; text-transform: uppercase; }

        .content-body { padding: 30px; }

        /* Komponen Umum CRUD & Tabel */
        .card { background: #ffffff; border: 1px solid #fce7f3; padding: 24px; border-radius: 10px; box-shadow: 0 4px 6px -1px rgba(131, 24, 67, 0.05); margin-bottom: 24px; }
        .card h3 { font-size: 18px; color: #831843; margin-bottom: 16px; }

        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-bottom: 16px; }
        .form-group label { display: block; font-size: 13px; color: #881337; font-weight: 600; margin-bottom: 6px; }
        .form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 1px solid #fbcfe8; border-radius: 6px; font-size: 14px; outline: none; }
        .form-group input:focus, .form-group select:focus { border-color: #db2777; box-shadow: 0 0 0 3px rgba(219, 39, 119, 0.1); }
        .form-group input:disabled { background: #f8fafc; color: #94a3b8; }

        .btn-pink { background-color: #db2777; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; font-family: inherit; }
        .btn-pink:hover { background-color: #be185d; }

        .search-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; gap: 12px; flex-wrap: wrap; }
        .search-bar input { width: 300px; padding: 10px 14px; border: 1px solid #fbcfe8; border-radius: 6px; outline: none; }
        .filters { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .filters input { width: 200px; }
        .filters input, .filters select { padding: 10px 14px; border: 1px solid #fbcfe8; border-radius: 6px; outline: none; font-size: 14px; font-family: inherit; }

        table { width: 100%; border-collapse: collapse; text-align: left; }
        th { background: #fff1f2; padding: 12px 16px; font-size: 13px; color: #881337; border-bottom: 2px solid #fce7f3; font-weight: 700; }
        td { padding: 12px 16px; font-size: 14px; color: #334155; border-bottom: 1px solid #f1f5f9; }
        tr:hover { background: #fff5f7; }

        .action-btn { color: #db2777; text-decoration: none; font-weight: 600; margin-right: 10px; }
        .action-btn.del { color: #e11d48; }
        .action-btn.edit { color: #0284c7; }
        .link-btn { background: none; border: none; cursor: pointer; font-size: 14px; font-family: inherit; padding: 0; }
        .inline-form { display: inline; }
        .cancel-link { margin-left: 10px; color: #64748b; text-decoration: none; font-size: 14px; }

        /* Alert & error */
        .alert { padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; font-size: 14px; }
        .alert-success { background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; }
        .alert-error { background: #fee2e2; border: 1px solid #fecaca; color: #991b1b; }
        .field-error { color: #be123c; font-size: 12px; margin-top: 4px; }
        .muted-empty { text-align: center; color: #94a3b8; }

        /* Pagination */
        .pager { display: flex; gap: 6px; margin-top: 20px; flex-wrap: wrap; }
        .pager a, .pager span { padding: 6px 12px; border: 1px solid #fbcfe8; border-radius: 6px; font-size: 13px; text-decoration: none; color: #be185d; }
        .pager a:hover { background: #fdf2f8; }
        .pager .current { background: #db2777; color: #fff; border-color: #db2777; }
        .pager .disabled { color: #cbd5e1; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            Stockify
            <span>{{ auth()->user()->role }}</span>
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a></li>
            <li><a href="{{ route('products.index') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Produk</a></li>
            <li><a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'active' : '' }}">Kategori</a></li>
            <li><a href="{{ route('suppliers.index') }}" class="{{ request()->routeIs('suppliers.*') ? 'active' : '' }}">Supplier</a></li>
            <li><a href="{{ route('stock-in.index') }}" class="{{ request()->routeIs('stock-in.*') ? 'active' : '' }}">Barang Masuk</a></li>
            <li><a href="{{ route('stock-out.index') }}" class="{{ request()->routeIs('stock-out.*') ? 'active' : '' }}">Barang Keluar</a></li>
            <li><a href="{{ route('history') }}" class="{{ request()->routeIs('history') ? 'active' : '' }}">Riwayat Transaksi</a></li>
            <li><a href="{{ route('reports') }}" class="{{ request()->routeIs('reports') ? 'active' : '' }}">Laporan</a></li>

            @if (auth()->user()->role === 'admin')
                <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'active' : '' }}">Pengguna</a></li>
            @endif

            <li class="logout">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="topbar">
            <h1>Sistem Inventaris Barang</h1>
            <div class="user-badge">Role: {{ auth()->user()->role }}</div>
        </div>
        <div class="content-body">
            @if (session('success'))
                <x-alert type="success">{{ session('success') }}</x-alert>
            @endif
            @if (session('error'))
                <x-alert type="error">{{ session('error') }}</x-alert>
            @endif

            @yield('content')
        </div>
    </div>

</body>
</html>
