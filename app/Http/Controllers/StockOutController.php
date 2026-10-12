<?php

namespace App\Http\Controllers;

class StockOutController extends StockMovementController
{
    protected string $type = 'OUT';
    protected string $prefix = 'stock-out';
    protected string $defaultReference = 'Barang Keluar';
}
