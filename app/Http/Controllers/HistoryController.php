<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __invoke(Request $request)
    {
        $movements = StockMovement::with(['product', 'supplier'])
            ->when($request->search, function ($q, $s) {
                $q->whereHas('product', fn ($p) => $p->where('name', 'like', "%{$s}%"));
            })
            ->when(in_array($request->type, ['IN', 'OUT'], true), fn ($q) => $q->where('type', $request->type))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('history.index', compact('movements'));
    }
}
