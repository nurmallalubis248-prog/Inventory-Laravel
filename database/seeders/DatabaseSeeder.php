<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun default (password di-hash otomatis lewat cast "hashed")
        User::create([
            'name' => 'admin', 'username' => 'admin',
            'email' => 'admin@stockify.test',
            'password' => 'password123', 'role' => 'admin',
        ]);
        User::create([
            'name' => 'staff', 'username' => 'staff',
            'email' => 'staff@stockify.test',
            'password' => 'password123', 'role' => 'staff',
        ]);

        // Data contoh dari P4
        $elektronik = Category::create(['name' => 'Elektronik']);
        $pakaian    = Category::create(['name' => 'Pakaian']);
        $atk        = Category::create(['name' => 'Alat Tulis Kantor']);

        $jaya = Supplier::create([
            'name' => 'PT Jaya Elektronik', 'phone' => '081234567890',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
        ]);
        $maju = Supplier::create([
            'name' => 'CV Maju Bersama', 'phone' => '089876543210',
            'address' => 'Jl. Ahmad Yani No. 45, Bandung',
        ]);

        $items = [
            [$elektronik, $jaya, 'ELK-001', 'Laptop ASUS Vivobook',    12, 7500000],
            [$elektronik, $jaya, 'ELK-002', 'Mouse Wireless Logitech', 45, 150000],
            [$pakaian,    $maju, 'PAK-001', 'Kemeja Formal Pria',      30, 120000],
            [$atk,        $maju, 'ATK-001', 'Kertas HVS A4 70gr',      50, 45000],

            // Data tambahan supaya pagination (10 per halaman) terlihat saat demo
            [$elektronik, $jaya, 'ELK-003', 'Keyboard Mechanical',     25, 350000],
            [$elektronik, $jaya, 'ELK-004', 'Headset Gaming',          18, 275000],
            [$elektronik, $jaya, 'ELK-005', 'Flashdisk 64GB',          60, 85000],
            [$elektronik, $jaya, 'ELK-006', 'Monitor LED 24 Inch',      9, 1750000],
            [$elektronik, $jaya, 'ELK-007', 'Webcam Full HD',           3, 320000], // stok menipis (<= minimum 5)
            [$pakaian,    $maju, 'PAK-002', 'Kaos Polos Katun',        40, 65000],
            [$pakaian,    $maju, 'PAK-003', 'Celana Chino Pria',       22, 185000],
            [$pakaian,    $maju, 'PAK-004', 'Jaket Hoodie',            15, 210000],
            [$atk,        $maju, 'ATK-002', 'Pulpen Gel Hitam',       120, 4000],
            [$atk,        $maju, 'ATK-003', 'Buku Tulis A5',           80, 8500],
            [$atk,        $maju, 'ATK-004', 'Stapler Besar',           25, 32000],
            [$atk,        $maju, 'ATK-005', 'Spidol Whiteboard',       55, 12000],
        ];

        foreach ($items as [$category, $supplier, $sku, $name, $stock, $price]) {
            $product = Product::create([
                'category_id' => $category->id,
                'sku' => $sku,
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'minimum_stock' => 5,
            ]);

            // Stok awal dicatat sebagai barang masuk agar rumus stok tetap konsisten
            $product->stockMovements()->create([
                'supplier_id' => $supplier->id,
                'type' => 'IN',
                'qty' => $stock,
                'reference' => 'Stok awal',
            ]);
        }

        // Data uji tambahan (opsional), aktifkan jika perlu:
        // Category::factory(5)->create();
        // Product::factory(50)->create();
    }
}
