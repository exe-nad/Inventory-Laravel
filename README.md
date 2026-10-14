# 🥐 SweetBakes: Artisan Inventory Management System (Laravel Application)

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-9.x%20MVC-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-InnoDB-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Eloquent ORM](https://img.shields.io/badge/Eloquent-Relationship%20M%3AN-orange?style=for-the-badge&logo=laravel&logoColor=white)

**Hasil Migrasi & Refactoring dari Inventory Management System P4 (PHP Native) ke Framework Laravel (Pertemuan 5 — Laravel Fundamental).**

</div>

---

| Identitas Mahasiswa | Keterangan |
| :--- | :--- |
| **Nama Mahasiswa** | **Nadya Salsabila Zahra** |
| **NIM** | **250180031** |
| **Mata Kuliah** | Pemrograman Web (Pertemuan 5 — Laravel Fundamental) |
| **Tema Proyek** | **SweetBakes — Artisan Bakery & Pastry Studio Manager** (Migrasi P4 ke Laravel) 🍰 |
| **Arsitektur Utama** | Pola Arsitektur MVC (*Model-View-Controller*), Resource Controller, Route Grouping, Blade Layout & Components, Database Migrations, Eloquent ORM & Relationships, Model Factories & Database Seeders |

---

## 🎯 1. Arsitektur MVC & Alur Kerja (*Request Lifecycle*)

Proyek ini menerapkan prinsip pemisahan tanggung jawab (*separation of concerns*) sesuai standar Laravel:

```text
Request (Browser)
   ↓
Route (routes/web.php)
   ↓
Controller (app/Http/Controllers/ProductController.php)
   ↓
Model & Eloquent (app/Models/Product.php, Category.php)
   ↓
Database (MySQL: inventory_db)
   ↓
Response (Blade View: resources/views/products/index.blade.php)
```

1. **Route (`routes/web.php`):** Menerima HTTP Request dan mengarahkan ke Controller yang bersangkutan menggunakan `Route::resource('products', ProductController::class)`.
2. **Controller (`app/Http/Controllers/`):** Mengatur alur logika bisnis, validasi `$request->validate()`, transaksi basis data atomik `DB::transaction()`, dan memanggil Model.
3. **Model & Eloquent (`app/Models/`):** Merepresentasikan tabel dan relasi bisnis:
   - `Category` $\rightarrow$ `hasMany(Product::class)`
   - `Product` $\rightarrow$ `belongsTo(Category::class)`
   - `Product` $\leftrightarrow$ `Supplier` $\rightarrow$ `belongsToMany(...)` via tabel pivot `product_suppliers` (Many-to-Many)
   - `Product` $\rightarrow$ `hasMany(StockMovement::class)`
4. **Database Migrations (`database/migrations/`):** Version control skema database relasional dengan foreign key constraint `restrictOnDelete()` dan `cascadeOnDelete()`.
5. **View & Blade Template (`resources/views/`):** Menyajikan antarmuka ke pengguna dengan sistem template Blade (`layouts/app.blade.php`, `@yield`, `@extends`, `<x-alert>`, `@error`, `@forelse`, dan `@csrf`).

---

## 🛡️ 2. Aturan Bisnis (*Business Rules*) P4 yang Dipertahankan

Sesuai instruksi slide 35: *"Business rule tidak boleh hilang hanya karena framework berubah."*

| Aturan Bisnis P4 | Implementasi di Laravel P5 | Lokasi File |
| :--- | :--- | :--- |
| **SKU harus unik** | Aturan validasi `'sku' => ['required', 'unique:products,sku']` dan index unik migration. | `app/Http/Controllers/ProductController.php` |
| **Harga dan stok tidak boleh negatif** | Rule validasi `'price' => ['numeric', 'min:0']`, `'stock' => ['integer', 'min:0']`. | `app/Http/Controllers/ProductController.php` |
| **Barang keluar tidak boleh melebihi stok** | Pengecekan saldo sebelum mutasi di `StockController@storeOut` dengan pesan error validasi kustom. | `app/Http/Controllers/StockController.php` |
| **Stok tidak boleh menjadi negatif** | Transaksi atomik `DB::transaction()` dan locking `lockForUpdate()`. | `app/Http/Controllers/StockController.php` |
| **Produk berhistori tidak boleh dihapus** | Pengecekan `$product->stockMovements()->count() > 0` sebelum `destroy()`. | `app/Http/Controllers/ProductController.php` |
| **Sorting hanya kolom yang diizinkan** | Array whitelist `$allowedSorts` pada `ProductController@index`. | `app/Http/Controllers/ProductController.php` |

---

## 🚀 3. Panduan Instalasi & Menjalankan Aplikasi

### Langkah 1: Nyalakan MySQL di XAMPP
Pastikan service **MySQL** pada XAMPP Control Panel dalam keadaan **Start / Running**.

### Langkah 2: Buat Database & Jalankan Migrasi + Seeder
Buka terminal di folder project (`C:\xampp\htdocs\inventory-laravel`) lalu jalankan:

```bash
php artisan migrate:fresh --seed
```
*Perintah ini akan membuat seluruh tabel dari nol dan mengisi data seed menu awal SweetBakes (Users, Kategori, Supplier, Produk, dan Histori Transaksi).*

### Langkah 3: Jalankan Server Aplikasi
Klik ganda file **`jalankan_server.bat`**, atau ketik di terminal:

```bash
php artisan serve
```

### Langkah 4: Buka di Browser
Akses alamat:
👉 **`http://localhost:8000`**

---

## 📋 4. Pemenuhan Acceptance Criteria & Checklist (Slide 41 & 46)

- [x] **Project Laravel dapat dijalankan** (`php artisan serve`).
- [x] **.env dan koneksi database benar** (`DB_DATABASE=inventory_db`).
- [x] **route:list menampilkan route yang diharapkan** (`products`, `categories`, `suppliers`, `stock`).
- [x] **Migration dapat dijalankan dari database kosong** (`migrate:fresh`).
- [x] **Seeder menghasilkan data uji** (`DatabaseSeeder.php` mengisi data SweetBakes).
- [x] **Model memiliki relationship yang benar** (`Category`, `Product`, `Supplier`, `StockMovement`).
- [x] **Resource controller menangani CRUD** (`ProductController` dengan 7 resource methods).
- [x] **Blade memakai layout dan escaping** (`layouts/app.blade.php`, `{{ $variable }}`).
- [x] **Form memiliki `@csrf` dan validasi** (`@error`, `old()`, flash alerts).
- [x] **Search dan pagination mempertahankan query string** (`->withQueryString()`).
- [x] **Inventory Management System P4 berhasil dipindahkan secara utuh ke Laravel.**
