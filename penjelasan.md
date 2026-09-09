# Penjelasan Kode — Mini Kasir Laravel

Aplikasi kasir (POS) berbasis Laravel dengan API untuk mengelola produk, kategori, order/checkout, autentikasi (Sanctum), serta background job. Berikut inti dari setiap file PHP dalam project (di luar `vendor/`, cache, dan compiled view).

## Models (`app/Models`)
- **Product.php** — Model produk. Field: `nama`, `harga`, `quantity`. Accessor `nama` otomatis uppercase saat dibaca dan ucwords saat disimpan. Scope `available()` (stok > 0), `priceAbove()`, `quantityAbove()`. Relasi many-to-many ke `Category` dan ke `Order` (dengan pivot quantity & price).
- **Category.php** — Model kategori, relasi many-to-many ke `Product`.
- **Order.php** — Model order/transaksi. Field: `invoice`, `total`. Relasi many-to-many ke `Product` dengan pivot quantity & price.
- **User.php** — Model user (autentikasi), pakai Sanctum untuk token API. Field fillable: name, email, password, role. Relasi one-to-many ke `Post`.
- **Post.php** — Model post sederhana milik user (fitur di luar kasir, kemungkinan latihan relasi).
- **Session.php** — Model tabel sessions, relasi ke `User`.

## Controllers (`app/Http/Controllers`)
- **ProductController.php** — CRUD produk via API: index (list + cache), show, store (validasi & create), update, destroy (cek izin admin via Gate), aboveQuantity (filter stok), decreaseQuantity (kurangi stok, contoh untuk transaksi manual).
- **OrderController.php** — `checkout()` memvalidasi daftar item lalu memanggil `OrderService::checkout()`; `show()` menampilkan detail order beserta produknya.
- **AuthController.php** — `login()` cek kredensial, buat token Sanctum; `logout()` menghapus token aktif.
- **Controller.php** — Base abstract controller kosong (bawaan Laravel).

## Services (`app/Services`) — logika bisnis, dipanggil controller
- **ProductService.php** — Wrapper ke `ProductRepository` + logika tambahan: cache list produk 60 detik, validasi quantity ≥ 0 saat create, `decreaseQuantity()` (transaksi DB, lempar `InsufficientStockException` jika stok kurang), fungsi batch processing produk & stok rendah.
- **OrderService.php** — `checkout()`: dalam satu DB transaction — validasi tiap item (produk ada, stok cukup), hitung total, buat `Order`, attach produk ke order (isi pivot), lalu kurangi stok tiap produk.

## Repositories (`app/Repositories`)
- **ProductRepositoryInterface.php** — Kontrak akses data produk (getAll, findById, create, update, delete, getAboveQuantity, save).
- **ProductRepository.php** — Implementasi konkret ke Eloquent `Product` sesuai interface di atas. Di-bind di `AppServiceProvider` agar bisa di-inject via interface (dependency inversion).

## Middleware & Policy
- **AdminMiddleware.php** — Menolak akses (403) jika `user->role !== 'admin'`.
- **ProductPolicy.php** — Aturan otorisasi model Product; hanya `delete()` yang mengizinkan (khusus role admin), aksi lain default `false`.

## Resources (`app/Http/Resources`) — pembentuk format JSON response
- **ProductResource.php** — Format output produk + kategori (jika di-load) + data pivot (quantity/price saat jadi bagian order).
- **CategoryResource.php** — Format output kategori (id, nama).
- **OrderResource.php** — Format output order (id, invoice, total) + daftar produk terkait via `ProductResource`.

## Helper
- **ApiResponse.php** — Helper statis untuk menyeragamkan response JSON sukses/gagal (`success`, `message`, `data`).

## Observer, Job, Provider
- **ProductObserver.php** — Event model Product: saat `creating`, quantity negatif dipaksa jadi 0; saat `created`, tulis log.
- **ProcessLowStockProducts.php** — Job terjadwal (dijalankan tiap menit via `routes/console.php`) yang mencatat log produk dengan stok ≤ 5.
- **SendWelcomeMessage.php**, **TestFailedJob.php** — Job contoh/latihan queue (yang kedua sengaja melempar exception untuk uji failed job).
- **AppServiceProvider.php** — Bind interface `ProductRepositoryInterface` ke `ProductRepository`, daftarkan observer produk, dan definisikan Gate `delete-product` (khusus admin).

## Exception
- **InsufficientStockException.php** — Exception khusus stok tidak cukup, di-handle global di `bootstrap/app.php` menjadi response JSON 422.

## Routing
- **routes/api.php** — Endpoint API: login/logout, CRUD produk, filter stok, checkout, lihat order (butuh auth), hapus produk (butuh auth + admin).
- **routes/web.php** — Beberapa route contoh non-API (dummy list produk, test error).
- **routes/console.php** — Menjadwalkan job `ProcessLowStockProducts` tiap menit; contoh command `inspire`.

## Bootstrap
- **bootstrap/app.php** — Konfigurasi utama aplikasi: daftar file routing, alias middleware `admin`, dan handler global untuk `InsufficientStockException` (jadi JSON 422).
- **bootstrap/providers.php** — Daftar service provider aktif (`AppServiceProvider`).

## Database — Migrations
Struktur tabel: `users` (+role), `products` (nama, harga, quantity), `categories`, `category_product` (pivot), `orders` (invoice, total), `order_product` (pivot quantity+price), `posts`, serta tabel bawaan Laravel (`sessions`, `cache`, `jobs`, `failed_jobs`, `personal_access_tokens` untuk Sanctum).

## Database — Factories & Seeders
- **UserFactory.php**, **ProductFactory.php** — Data dummy untuk testing (nama, harga, quantity acak / user dengan password default).
- **DatabaseSeeder.php** — Membuat satu user tes.
- **ProductSeeder.php** — Mengisi puluhan data produk kasir riil (mie instan, minuman, kopi, dll).
- **CategorySeeder.php** — Mengisi kategori: Makanan, Minuman, Snack, Sembako, Kebutuhan Rumah.

## Tests
- **ProductTest.php** — Feature test: ambil daftar produk, buat produk (sukses & validasi gagal), hapus produk (khusus admin, ditolak untuk user biasa), serta pengurangan stok yang gagal karena stok tidak cukup.
- **TestCase.php**, **ExampleTest.php** — Base class & test contoh bawaan Laravel.

## public/index.php
Entry point standar aplikasi Laravel yang memuat autoloader dan menjalankan HTTP kernel/application.

---
**Alur inti aplikasi:** Request masuk lewat `routes/api.php` → `Controller` → `Service` (logika bisnis & transaksi) → `Repository`/Model (akses data) → `Resource` (format JSON) → `ApiResponse` (bungkus response seragam). Otorisasi diatur lewat `Middleware`, `Gate`, dan `Policy`; stok produk dijaga lewat `Observer`, `Exception`, dan job terjadwal.
