# Memahami Mini Kasir Laravel Melalui Roadmap Backend

Dokumen ini memetakan project **mini-kasir-laravel** (kode + database `kasir_laravel.sql`) ke 6 tahap **Roadmap Backend Development**, supaya terlihat jelas: bagian roadmap mana yang sudah dikuasai lewat project ini, dan apa inti pembelajarannya.

---

## Tahap 1 — Dasar Pemrograman
**Di project ini:** hampir semua file PHP memakai variabel, tipe data (int, string, array), kontrol alur (`if`, `foreach`), fungsi/method, dan error handling (`try` implisit lewat `throw new Exception`, custom exception `InsufficientStockException`).
**Contoh nyata:** `ProductService::decreaseQuantity()` — cek kondisi stok, lempar exception kalau tidak cukup.
**Inti:** dasar ini jadi fondasi seluruh logika di layer Service & Controller.

## Tahap 2 — Database
**Di project ini:** `kasir_laravel.sql` berisi skema nyata hasil migration Laravel.

| Tabel | Fungsi | Relasi |
|---|---|---|
| `users` | akun (ada kolom `role`: user/admin) | 1–N ke `posts` |
| `products` | data barang (nama, harga, quantity) | N–N ke `categories`, N–N ke `orders` |
| `categories` | kategori produk | N–N ke `products` |
| `category_product` | tabel pivot | FK ke `products` & `categories`, unique(product_id, category_id) |
| `orders` | transaksi (invoice unik, total) | N–N ke `products` |
| `order_product` | pivot order↔produk | simpan `quantity` & `price` saat transaksi, FK cascade delete |
| `posts` | contoh relasi 1–N ke user | FK `user_id` cascade delete |
| `personal_access_tokens`, `sessions`, `cache`, `jobs`, `job_batches`, `failed_jobs`, `migrations`, `password_reset_tokens` | tabel bawaan Laravel (auth token, session, cache, antrian job) | — |

**Konsep database yang dipakai:** normalisasi (pivot table `category_product` & `order_product` memisahkan relasi many-to-many), indexing otomatis via primary/unique key, foreign key dengan `ON DELETE CASCADE`, serta constraint `UNIQUE` (mis. `orders.invoice`, `users.email`).
**Inti:** skema ini persis representasi dari relasi Eloquent di `app/Models` — apa yang didefinisikan sebagai `belongsToMany()` di kode, terwujud sebagai tabel pivot di database.

## Tahap 3 — REST API
**Di project ini:** `routes/api.php` mendefinisikan endpoint CRUD standar REST:
- `GET /products`, `GET /products/{id}` → baca data
- `POST /products` → buat data
- `PUT /products/{id}` → update data
- `DELETE /products/{id}` → hapus data
- `POST /checkout` → aksi bisnis khusus (bukan CRUD murni)

Response memakai format JSON konsisten lewat `ApiResponse` helper (`success`, `message`, `data`) dan status code yang sesuai (200, 201, 401, 403, 422). Validasi request (`$request->validate()`) memastikan input sesuai aturan sebelum diproses.
**Inti:** ini implementasi nyata dari "API CRUD dengan framework" yang jadi output roadmap tahap 3.

## Tahap 4 — Autentikasi API
**Di project ini:** menggunakan **Laravel Sanctum** (token-based auth, bukan session), terlihat dari tabel `personal_access_tokens` dan kode:
- `AuthController::login()` — cek kredensial, buat token via `createToken()`.
- `AuthController::logout()` — hapus token aktif (`currentAccessToken()->delete()`).
- Middleware `auth:sanctum` di route yang butuh login (logout, checkout order, lihat order).
- Middleware custom `AdminMiddleware` — otorisasi berbasis role (mirip middleware security di roadmap), dipakai bersama `auth:sanctum` untuk endpoint hapus produk.
- `ProductPolicy` & `Gate::define('delete-product')` — otorisasi level aksi (siapa boleh apa).

**Inti:** ini realisasi dari "session vs token-based auth, middleware security" — project ini memilih pendekatan token (Sanctum) + middleware role-based, bukan JWT/OAuth2, tapi konsepnya sama: melindungi endpoint dari akses tidak sah.

## Tahap 5 — OOP & Backend Architecture
**Di project ini** arsitektur sudah dipecah ke beberapa layer, sesuai pola **Repository–Service–Controller**:

```
Route → Controller → Service → Repository → Model (Eloquent) → Database
                         ↓
                    Resource (format JSON output)
```

- **Controller** (`ProductController`, `OrderController`, `AuthController`) — hanya menerima request & mengembalikan response, tidak berisi logika bisnis berat.
- **Service** (`ProductService`, `OrderService`) — logika bisnis: validasi stok, transaksi checkout, caching.
- **Repository** (`ProductRepository` + `ProductRepositoryInterface`) — abstraksi akses data, di-bind lewat `AppServiceProvider` (contoh Dependency Inversion / dependency injection via interface).
- **Resource** (`ProductResource`, `OrderResource`, `CategoryResource`) — transformasi model jadi JSON terkontrol.
- **Observer** (`ProductObserver`) & **Policy** (`ProductPolicy`) — pemisahan tanggung jawab event & otorisasi dari model/controller.
- OOP dasar: inheritance (`Controller` abstract, `Exception` custom), penggunaan trait (`HasFactory`, `HasApiTokens`, `Notifiable`).

**Inti:** ini contoh nyata "aplikasi modular dengan layer Service/Repo/Handler" — bukan sekadar CRUD di controller, tapi dipisah agar mudah diuji dan dikembangkan.

## Tahap 6 — Advanced Backend
**Di project ini:**
- **Job Scheduling / Cron:** `routes/console.php` menjadwalkan `ProcessLowStockProducts` untuk berjalan `everyMinute()`.
- **Queue / Background Job:** `app/Jobs` (`ProcessLowStockProducts`, `SendWelcomeMessage`, `TestFailedJob`) — job `ShouldQueue` yang diproses async, didukung tabel `jobs`, `job_batches`, `failed_jobs`.
- **Batch Processing:** `Product::chunk(5, ...)` di `ProductService` dan job — memproses data besar per-batch agar hemat memori.
- **Caching:** `ProductService::getAll()` memakai `Cache::remember('products', 60, ...)` untuk mengurangi query berulang (tabel `cache` & `cache_locks` mendukung ini).
- **Monitoring sederhana:** `logger()->info()` dipakai di observer & job untuk mencatat aktivitas (stok rendah, produk dibuat).
- Belum ada integrasi Message Queue eksternal (Redis/RabbitMQ) secara eksplisit — job masih pakai driver queue default/database, jadi ini area lanjutan yang bisa dikembangkan lebih jauh sesuai roadmap (mis. ganti driver queue ke Redis).

**Inti:** project sudah menyentuh semua elemen tahap 6 (scheduled job, queue, batch, caching) meski skalanya masih sederhana — pas untuk latihan sebelum masuk produksi dengan Redis/MQ sungguhan.

---

## Ringkasan Pemetaan

| Roadmap | Terpenuhi di Project? | Bukti Utama |
|---|---|---|
| 1. Dasar Pemrograman | ✅ | Logika di seluruh Controller/Service |
| 2. Database | ✅ | Skema `kasir_laravel.sql`, relasi & pivot table |
| 3. REST API | ✅ | `routes/api.php`, `ApiResponse` |
| 4. Autentikasi API | ✅ (token-based, bukan JWT/OAuth2) | Sanctum, `AdminMiddleware`, `ProductPolicy` |
| 5. OOP & Architecture | ✅ | Repository–Service–Controller, Observer, Resource |
| 6. Advanced Backend | ✅ (skala kecil) | Scheduler, Queue Job, `chunk()`, `Cache::remember` |

**Kesimpulan:** mini-kasir-laravel ini praktis merupakan implementasi ujung-ke-ujung dari seluruh 6 tahap roadmap backend — dari dasar pemrograman sampai advanced backend — dalam satu studi kasus aplikasi kasir sederhana. Yang paling bisa dikembangkan lebih lanjut: autentikasi (tambah JWT/OAuth2 sebagai pembanding Sanctum) dan advanced backend (ganti queue driver ke Redis + integrasi message queue eksternal).
