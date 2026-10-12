<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockMovementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'type' => 'IN',
            'qty' => fake()->numberBetween(1, 20),
            'reference' => 'Barang Masuk',
        ];
    }
}
