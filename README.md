# Absensi RFID — Laravel + Livewire + Tailwind

File di folder ini adalah *overlay*: salin ke proyek Laravel 11/12 yang baru.

```bash
composer create-project laravel/laravel absensi-rfid
cd absensi-rfid
composer require livewire/livewire
php artisan install:api          # membuat routes/api.php (+ Sanctum, boleh diabaikan)

# salin isi folder ini ke dalam proyek (timpa routes/web.php, routes/api.php,
# resources/css/app.css, database/seeders/DatabaseSeeder.php)

# .env  -> atur DB_*, dan APP_TIMEZONE=Asia/Jakarta di config/app.php / .env
php artisan migrate --seed
npm install && npm run dev
php artisan serve
```

Login awal: `admin@example.com` / `password` (segera ganti).

## Endpoint untuk perangkat RFID

```
POST /api/v1/attendance      header: X-Device-Key: <api key perangkat>
body: uid=04A1B2C3           (opsional: scanned_at=2026-10-29 07:55:00)
GET  /api/v1/ping            cek koneksi + sinkron waktu server
```

Contoh respons: `{"ok":true,"code":"recorded","message":"Absen masuk berhasil","name":"Budi","type":"in","status":"on_time","time":"07:55:12","lcd":{"line1":"ABSEN MASUK","line2":"BUDI"}}`
Field `lcd.line1` dan `lcd.line2` masing-masing selalu maksimal 16 karakter untuk LCD I2C 16x2. Nama yang lebih panjang akan dipotong hanya pada field `lcd`; field `name` tetap lengkap.
Kartu tak dikenal -> HTTP 404 + `code: unregistered`, lalu muncul di menu **Kartu RFID** untuk diberi nama.
