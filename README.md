# Stockify – Inventory Management System (Laravel)

Migrasi **Inventory Management System P4 (PHP native)** ke **Laravel 12** untuk Pertemuan 5 – Laravel Fundamental.
Business rule dari P4 tetap dipertahankan: stok tidak boleh negatif, dan riwayat transaksi selalu sinkron dengan stok.

## Fitur

- Login / logout dengan dua role: **admin** dan **staff** (manajemen pengguna hanya untuk admin)
- CRUD **Produk** (resource controller lengkap), **Kategori**, **Supplier**
- Search, filter kategori, sorting, dan pagination pada daftar produk
- **Barang Masuk / Barang Keluar** dengan validasi stok dan DB transaction
- Riwayat transaksi, laporan, dan peringatan stok menipis

## Kebutuhan

- PHP 8.2 atau lebih baru
- Composer
- MySQL / MariaDB (XAMPP sudah cukup)

## Instruksi Instalasi

```bash
# 1. Masuk ke folder project
cd inventory-laravel

# 2. Pasang dependency
composer install

# 3. Salin konfigurasi lingkungan dan buat application key
cp .env.example .env          # PowerShell: Copy-Item .env.example .env
php artisan key:generate

# 4. Buat database kosong bernama inventory_laravel (lewat phpMyAdmin atau MySQL)
#    Nyalakan Apache dan MySQL di XAMPP terlebih dahulu.

# 5. Jalankan migration dan seeder
php artisan migrate:fresh --seed

# 6. Jalankan aplikasi
php artisan serve
```

Buka **http://127.0.0.1:8000** di browser.

Konfigurasi database default di `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_laravel
DB_USERNAME=root
DB_PASSWORD=
```

> `migrate:fresh` menghapus seluruh tabel lalu membuatnya ulang. Gunakan hanya pada database development.

## Akun Login (dari seeder)

| Username | Password      | Role  |
|----------|---------------|-------|
| admin    | `password123` | admin |
| staff    | `password123` | staff |

## Arsitektur MVC

Alur request sampai response:

```
Request -> Route -> Controller -> Model (Eloquent) -> Database -> Response (View / Redirect)
```

| Komponen   | Tanggung jawab                                   | Lokasi                                  |
|------------|--------------------------------------------------|-----------------------------------------|
| Route      | Menghubungkan URL + HTTP method ke controller    | `routes/web.php`                        |
| Controller | Menerima request, validasi, memilih response     | `app/Http/Controllers/`                 |
| Model      | Representasi tabel, relationship, akses database | `app/Models/`                           |
| View       | Tampilan Blade (layout, component, form)         | `resources/views/`                      |
| Service    | Business rule stok (transaction)                 | `app/Services/StockService.php`         |
| Migration  | Struktur database (version control skema)        | `database/migrations/`                  |
| Seeder     | Data awal / data uji                             | `database/seeders/DatabaseSeeder.php`   |

### Contoh alur: tambah produk

1. Pengguna membuka `GET /products/create` -> `ProductController@create` menampilkan `products/create.blade.php` (form dengan `@csrf`).
2. Form dikirim ke `POST /products` -> `ProductController@store` menjalankan `$request->validate(...)`.
3. Jika validasi gagal, pengguna dikembalikan ke form dengan pesan error (`@error`) dan input lama (`old()`).
4. Jika berhasil, `Product::create($data)` menyimpan ke tabel `products` di dalam DB transaction (stok awal dicatat sebagai barang masuk).
5. Controller melakukan `redirect()->route('products.index')` dengan pesan sukses.
6. `GET /products` menampilkan daftar produk terbaru lewat Eloquent `Product::with('category')->paginate(10)`.

### Relationship Eloquent

- `Category` **hasMany** `Product`; `Product` **belongsTo** `Category`
- `Product` **hasMany** `StockMovement`; `StockMovement` **belongsTo** `Product` dan `Supplier`
- `Supplier` **hasMany** `StockMovement`

### Route produk (resource controller)

| Method    | URL                    | Action    | Nama route        |
|-----------|------------------------|-----------|-------------------|
| GET       | `/products`            | index     | products.index    |
| GET       | `/products/create`     | create    | products.create   |
| POST      | `/products`            | store     | products.store    |
| GET       | `/products/{product}`  | show      | products.show     |
| GET       | `/products/{id}/edit`  | edit      | products.edit     |
| PUT/PATCH | `/products/{product}`  | update    | products.update   |
| DELETE    | `/products/{product}`  | destroy   | products.destroy  |

Daftar lengkap: `php artisan route:list`.

## Business Rule Stok

- Stok produk hanya berubah lewat **Barang Masuk / Keluar** (`StockService`), bukan lewat form edit produk.
- Barang keluar yang melebihi stok ditolak dengan pesan error.
- Semua perubahan stok dibungkus `DB::transaction` dengan `lockForUpdate()` agar stok dan riwayat tetap konsisten.
