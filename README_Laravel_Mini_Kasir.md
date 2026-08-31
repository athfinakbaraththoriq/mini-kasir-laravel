# Mini Kasir Laravel — Catatan Belajar

README ini merangkum perjalanan belajar dari migrasi project ke Laravel sampai Eloquent, Relationship, Scope, Model Events, dan Observer.

## 1. Project

```text
D:\laragon\www\mini-kasir-laravel
```

Database:

```text
kasir_laravel
```

Environment:
- Laragon
- PHP
- MySQL
- Laravel
- Artisan
- Tinker

---

## 2. Routing

Melihat route:

```bash
php artisan route:list
```

Contoh:

```php
Route::get('/products', [ProductController::class, 'index']);
Route::post('/products', [ProductController::class, 'store']);
Route::get('/products/{id}', [ProductController::class, 'show']);
```

`GET` mengambil data, `POST` membuat data, dan route `{id}` mengambil data berdasarkan ID.

---

## 3. CSRF

CSRF (Cross-Site Request Forgery) adalah perlindungan Laravel terhadap request web yang tidak memiliki token CSRF yang valid.

Pada Blade form:

```blade
<form method="POST" action="/products">
    @csrf
</form>
```

---

## 4. Migration

Migration digunakan untuk mengatur **struktur database**.

Contoh:

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('nama');
    $table->integer('harga');
    $table->integer('quantity');
    $table->timestamps();
});
```

Jalankan:

```bash
php artisan migrate
```

Cek status:

```bash
php artisan migrate:status
```

### `migrate:fresh`

```bash
php artisan migrate:fresh
```

Menghapus seluruh tabel lalu menjalankan migration dari awal. Semua data akan hilang.

Karena itu setelah `migrate:fresh`, data perlu dibuat ulang.

### Error `Table already exists`

Jika muncul:

```text
SQLSTATE[42S01]: Base table or view already exists
```

berarti Laravel mencoba membuat tabel yang sudah ada. Cek `php artisan migrate:status` dan kondisi database sebelum membuat migration tambahan.

---

# 5. Eloquent

Eloquent adalah ORM Laravel yang memungkinkan kita berinteraksi dengan database menggunakan Model.

```php
use App\Models\Product;

Product::all();
Product::first();
Product::find(1);
```

Membuat data:

```php
Product::create([
    'nama' => 'Laptop',
    'harga' => 10000000,
    'quantity' => 10,
]);
```

### Namespace di Tinker

Jika:

```text
Class "Product" not found
```

gunakan:

```php
App\Models\Product::all();
```

atau:

```php
use App\Models\Product;
```

---

# 6. `$fillable`

`$fillable` menentukan field yang boleh diisi melalui mass assignment.

```php
protected $fillable = [
    'nama',
    'harga',
    'quantity',
];
```

Contoh:

```php
Product::create([
    'nama' => 'Laptop',
    'harga' => 10000000,
    'quantity' => 10,
]);
```

Jika field tidak diizinkan, bisa muncul `MassAssignmentException`.

---

# 7. Product Model

Model yang digunakan selama latihan:

```php
<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;

class Product extends Model
{
    protected $fillable = [
        'nama',
        'harga',
        'quantity',
    ];

    protected function nama(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => ucwords($value),
        );
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeQuantityAbove(
        Builder $query,
        int $quantity
    ): Builder {
        return $query->where('quantity', '>', $quantity);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}
```

---

# 8. Accessor

Accessor bekerja ketika data **dibaca**.

```php
get: fn ($value) => strtoupper($value),
```

Database:

```text
Laptop Gaming
```

Saat dibaca:

```php
$product->nama;
```

menjadi:

```text
LAPTOP GAMING
```

---

# 9. Mutator

Mutator bekerja ketika data **akan disimpan**.

```php
set: fn ($value) => ucwords($value),
```

Input:

```text
laptop gaming
```

disimpan sebagai:

```text
Laptop Gaming
```

Accessor + Mutator modern dapat digabung melalui:

```php
Attribute::make(...)
```

---

# 10. Cast

Cast berhubungan dengan tipe data.

Contoh konsep:

```text
"1"      -> 1
"true"   -> true
tanggal  -> object tanggal
```

Intinya: **Cast mengatur bagaimana nilai diperlakukan sebagai tipe tertentu.**

---

# 11. Eloquent Relationship

Jenis yang dipelajari:

```text
One to One       -> hasOne
One to Many      -> hasMany
Many to One      -> belongsTo
Many to Many     -> belongsToMany
```

### `hasOne`

Satu User memiliki satu Profile.

```php
return $this->hasOne(Profile::class);
```

### `hasMany`

Satu User memiliki banyak Post.

```php
return $this->hasMany(Post::class);
```

### `belongsTo`

Banyak Post dimiliki oleh satu User.

```php
return $this->belongsTo(User::class);
```

### `belongsToMany`

Banyak Product dapat memiliki banyak Category.

```php
return $this->belongsToMany(Category::class);
```

---

# 12. Pivot Table

Relasi many-to-many membutuhkan pivot table.

Contoh:

```text
Product <-> Category
```

Pivot:

```text
category_product
```

Isi:

```text
id | product_id | category_id
1  | 1          | 2
```

### `attach`

```php
$product->categories()->attach($category->id);
```

### `detach`

```php
$product->categories()->detach($category->id);
```

### `sync`

```php
$product->categories()->sync([1, 2, 3]);
```

`sync()` menyamakan relationship dengan daftar ID yang diberikan.

### `withPivot`

Jika pivot punya data tambahan:

```text
order_id
product_id
quantity
```

relationship dapat memakai:

```php
return $this->belongsToMany(Product::class)
    ->withPivot('quantity');
```

Kemudian:

```php
$order->products[0]->pivot->quantity;
```

---

# 13. `products.quantity` vs `order_product.quantity`

Ini penting.

`products.quantity`:

> stok Product yang tersedia.

Contoh:

```text
Laptop -> quantity 15
```

`order_product.quantity`:

> jumlah Product yang dibeli pada Order tertentu.

Contoh:

```text
order_id | product_id | quantity
10       | 1          | 2
```

Artinya Order #10 membeli 2 Laptop.

Jadi kedua kolom dapat sama-sama bernama `quantity`, tetapi maknanya berbeda.

---

# 14. Query Scope

Scope membuat query yang sering digunakan menjadi reusable.

### Local Scope

```php
public function scopeAvailable(Builder $query): Builder
{
    return $query->where('quantity', '>', 0);
}
```

Panggil:

```php
Product::available()->get();
```

SQL kira-kira:

```sql
SELECT * FROM products
WHERE quantity > 0;
```

Untuk melihat SQL:

```php
Product::available()->toSql();
```

Hasil seperti:

```text
select * from `products` where `quantity` > ?
```

`?` adalah placeholder prepared statement dan normal.

### Dynamic Scope

Dynamic Scope menerima parameter:

```php
public function scopePriceAbove(
    Builder $query,
    int $price
): Builder {
    return $query->where('harga', '>', $price);
}
```

Panggil:

```php
Product::priceAbove(5000000)->get();
```

### `quantityAbove`

```php
public function scopeQuantityAbove(
    Builder $query,
    int $quantity
): Builder {
    return $query->where('quantity', '>', $quantity);
}
```

Panggil:

```php
Product::quantityAbove(10)->get();
```

Bisa digabung:

```php
Product::available()
    ->quantityAbove(10)
    ->get();
```

**Ingat:** Laravel mengenali Scope dari prefix `scope`.

Benar:

```php
scopeQuantityAbove()
```

Dipanggil:

```php
quantityAbove()
```

---

# 15. Model Events

Event utama:

```text
creating -> sebelum INSERT
created  -> setelah INSERT

updating -> sebelum UPDATE
updated  -> setelah UPDATE

deleting -> sebelum DELETE
deleted  -> setelah DELETE
```

Alur:

```text
creating
   ↓
INSERT
   ↓
created
```

dan:

```text
updating
   ↓
UPDATE
   ↓
updated
```

---

# 16. `booted()`

Event dapat ditulis langsung di Model:

```php
protected static function booted(): void
{
    static::creating(function (Product $product) {
        if ($product->quantity < 0) {
            $product->quantity = 0;
        }
    });
}
```

Jika dibuat:

```php
Product::create([
    'nama' => 'Test Event',
    'harga' => 100000,
    'quantity' => -5,
]);
```

sebelum INSERT:

```text
-5 -> 0
```

yang disimpan adalah `0`.

Contoh event `created`:

```php
static::created(function (Product $product) {
    logger("Product {$product->nama} berhasil dibuat.");
});
```

Log dapat dilihat di:

```text
storage/logs/laravel.log
```

---

# 17. Observer

Jika event semakin banyak, logic event dapat dipisahkan ke Observer.

Buat:

```bash
php artisan make:observer ProductObserver --model=Product
```

File:

```text
app/Observers/ProductObserver.php
```

Contoh:

```php
<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    public function creating(Product $product): void
    {
        if ($product->quantity < 0) {
            $product->quantity = 0;
        }
    }

    public function created(Product $product): void
    {
        logger("Product {$product->nama} berhasil dibuat.");
    }
}
```

Daftarkan di `AppServiceProvider`:

```php
use App\Models\Product;
use App\Observers\ProductObserver;
```

Kemudian:

```php
public function boot(): void
{
    Product::observe(ProductObserver::class);
}
```

Setelah event dipindahkan ke Observer, hindari menduplikasi event yang sama di `Product::booted()`.

Konsep:

```text
Product.php
    -> model, relationship, scope, attribute

ProductObserver.php
    -> event logic
```

---

# 18. Tinker

Masuk:

```bash
php artisan tinker
```

Contoh:

```php
App\Models\Product::all();

App\Models\Product::find(1);

App\Models\Product::create([
    'nama' => 'Monitor',
    'harga' => 2500000,
    'quantity' => 10,
]);

App\Models\Product::available()->get();

App\Models\Product::quantityAbove(10)->get();
```

---

# 19. Error yang Pernah Ditemui

### `Class "Product" not found`

Gunakan:

```php
App\Models\Product
```

atau import:

```php
use App\Models\Product;
```

### `MassAssignmentException`

Tambahkan field ke:

```php
protected $fillable = [
    'field',
];
```

### `category_product doesn't exist`

Laravel mencari pivot table `category_product`. Pastikan migration pivot sudah dibuat dan dijalankan.

### `Field 'nama' doesn't have a default value`

Field `nama` wajib diisi tetapi tidak diberikan saat INSERT.

### `$category` null

Jika:

```php
$category->id
```

menghasilkan error null, berarti `$category` belum berisi object Category.

Cek:

```php
$category = App\Models\Category::first();
```

### `Table already exists`

Migration mencoba membuat tabel yang sudah ada. Cek:

```bash
php artisan migrate:status
```

---

# 20. Cara Membaca Error

Jangan langsung mengubah banyak kode.

Baca:

```text
1. Jenis error
2. Pesan error
3. File
4. Baris
5. Query SQL jika ada
6. Data yang dikirim
```

Contoh perbedaan:

```text
VS Code warning
```

belum tentu error runtime.

Sedangkan:

```text
BadMethodCallException
QueryException
MassAssignmentException
```

adalah error yang perlu dianalisis.

---

# 21. Checklist Materi

```text
Laravel dasar              [x]
Routing                    [x]
CSRF                       [x]
Migration                  [x]
Eloquent CRUD              [x]
$fillable                  [x]
Relationship               [x]
Pivot                      [x]
attach / detach / sync     [x]
withPivot                  [x]
Accessor                   [x]
Mutator                    [x]
Cast                       [x]
Local Scope                [x]
Dynamic Scope              [x]
Model Events               [x]
Observer                   [x]
```

---

# 22. Alur Belajar

```text
PHP Project
    ↓
Migrasi ke Laravel
    ↓
Routing
    ↓
Controller
    ↓
Migration
    ↓
Model
    ↓
Eloquent CRUD
    ↓
Relationship
    ↓
Pivot
    ↓
attach / detach / sync
    ↓
withPivot
    ↓
Accessor
    ↓
Mutator
    ↓
Cast
    ↓
Local Scope
    ↓
Dynamic Scope
    ↓
Model Events
    ↓
Observer
    ↓
Repository Pattern  <-- materi berikutnya
    ↓
Task 6
    ↓
Live Coding
```

---

# 23. Cheat Sheet

## CRUD

```php
Product::all();

Product::find(1);

Product::create([
    'nama' => 'Laptop',
    'harga' => 10000000,
    'quantity' => 10,
]);

$product->update([
    'harga' => 9000000,
]);

$product->delete();
```

## Relationship

```php
$user->posts;

$post->user;

$product->categories;

$category->products;
```

## Pivot

```php
$product->categories()->attach($category->id);

$product->categories()->detach($category->id);

$product->categories()->sync([1, 2, 3]);
```

## Scope

```php
Product::available()->get();

Product::quantityAbove(10)->get();
```

## Observer

```php
Product::observe(ProductObserver::class);
```

---

# 24. Prinsip Utama untuk Live Coding

Jangan menghafal semua syntax.

Gunakan alur berpikir:

```text
Apa yang sedang dibuat?
        ↓
Model apa yang terlibat?
        ↓
Relationship-nya apa?
        ↓
Query apa yang dibutuhkan?
        ↓
Apakah query berulang?
        ↓
Gunakan Scope?
        ↓
Apakah ada event?
        ↓
Apakah event perlu Observer?
```

Yang paling penting bukan bebas dari error, tetapi mampu:

1. membaca error,
2. menemukan sumber masalah,
3. memahami kenapa error terjadi,
4. memperbaikinya,
5. menjelaskan alasan solusi.

---

# Status

**Eloquent sampai Observer: SELESAI.**

Materi berikutnya:

```text
Repository Pattern
```

Target akhirnya:

```text
Controller
    ↓
Repository
    ↓
Eloquent Model
    ↓
Database
```

Repository Pattern akan membantu memisahkan logic/query database dari Controller sehingga aplikasi lebih mudah dirawat.
