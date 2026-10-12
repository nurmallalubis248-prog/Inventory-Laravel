<?php

namespace App\Http\Controllers;

class StockInController extends StockMovementController
{
    protected string $type = 'IN';
    protected string $prefix = 'stock-in';
    protected string $defaultReference = 'Barang Masuk';
}
