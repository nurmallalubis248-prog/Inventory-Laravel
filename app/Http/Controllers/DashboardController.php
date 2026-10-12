<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('dashboard', [
            'totalProduk'   => Product::count(),
            'totalKategori' => Category::count(),
            'totalSupplier' => Supplier::count(),
            'totalNilai'    => Product::selectRaw('COALESCE(SUM(stock * price), 0) as total')->value('total'),
            'stokRendah'    => Product::whereColumn('stock', '<=', 'minimum_stock')->count(),
        ]);
    }
}
