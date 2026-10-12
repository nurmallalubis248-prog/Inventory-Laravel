<?php

namespace App\Http\Controllers;

use App\Models\Product;

class ReportController extends Controller
{
    public function __invoke()
    {
        $products = Product::with('category')->orderByDesc('id')->get();

        $totalVal = $products->sum(fn ($p) => $p->stock * $p->price);

        return view('reports.index', compact('products', 'totalVal'));
    }
}
