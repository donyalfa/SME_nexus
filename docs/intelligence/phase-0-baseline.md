# Intelligence — Phase 0 Baseline

Tanggal: 2026-11-03
Branch: `feature/intelligence`

## Database

- Working database: `db_sme`
- Test database: `db_sme_test`
- Sumber schema legacy: `dump-db_sme-202609241157.sql` (20 tabel)
- Backup schema working DB: `storage/backups/db_sme_baseline_*.sql`
- Backup kondisi `db_sme2` setelah insiden test: `storage/backups/db_sme2_current_*.sql`

`db_sme` dibuat dari dump legacy, lalu dilengkapi migrasi aditif:

- `2026_11_02_000000_create_business_data_table`
  - FK `id_profile` → `sme_profiles.id_profile`
  - Unique `(id_profile, period_date)`
- `2026_11_03_000000_create_legacy_auth_support_tables`
  - `password_reset_tokens`
  - `sessions`

Migrasi `0001_01_01_000000_create_users_table` ditandai sudah berjalan pada `db_sme`
karena tabel `users` legacy sudah ada. Tabel `cache`, `jobs`, dan turunannya dibuat
oleh migrasi Laravel bawaan.

Seeder lokal membuat:

- `admin@example.com` / `password` — role `admin`
- `test@example.com` / `password` — role `sme_owner`

## Test isolation

Tes tidak lagi memakai `RefreshDatabase` pada database kerja. Konfigurasi berubah:

- `.env.testing` → `DB_DATABASE=db_sme_test`
- `tests/Pest.php` → `DatabaseTransactions`

Schema `db_sme_test` berasal dari dump legacy yang sama dan sudah dilengkapi migrasi
aditif. Jalankan:

```bash
php artisan test --compact
```

Hasil baseline: **51 passed (95 assertions)**.

## Auth compatibility

- `App\Shared\Models\User`
  - primary key `id_user`
  - `full_name`
  - `is_verified`
  - role `sme_owner` / `admin`
- Verifikasi email memakai `is_verified` dan tetap mengimplementasikan `MustVerifyEmail`.
- Factory, seeder, registrasi, profil, dan reset password disesuaikan.
- Kolom `remember_token` tidak ditambahkan ke tabel legacy; fitur remember-me tidak
  diaktifkan. Reset password tidak lagi menulis `remember_token`.
- Middleware `role` ditambahkan untuk membatasi route owner/admin.

## Routing dan UI foundation

- `App\Modules\Intelligence\Providers\IntelligenceServiceProvider`
  - `/intelligence` → dashboard owner
  - `/admin/intelligence` → dashboard admin
- Layout Bootstrap 5 terpisah:
  - `resources/views/layouts/intelligence.blade.php`
  - `resources/views/layouts/intelligence-admin.blade.php`
- Asset Vite:
  - `resources/css/intelligence.css`
  - `resources/js/intelligence.js`

## Catatan insiden

Sebelum isolasi tes diterapkan, test suite lama memakai `RefreshDatabase` dan mengarah
ke `db_sme2`, sehingga database tersebut ter-reset. Berdasarkan audit sebelumnya,
`db_sme2` hanya berisi satu user uji dan nol data bisnis. Working database sekarang
dibangun ulang dari dump legacy di `db_sme`.

## Berikutnya

Phase 1: admin user management, profil UMKM, master data, dan rekomendasi.
