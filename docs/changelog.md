# Changelog & Dokumentasi Fitur MMS

> **Maintenance Management System (MMS)**
> Dibuat oleh: Cline AI Assistant
> Tanggal: 26 Mei 2026

---

## Daftar Isi

1. [Fitur Assign Mekanik & SLA](#1-fitur-assign-mekanik--sla)
2. [Fitur Dashboard Role-Based](#2-fitur-dashboard-role-based)
3. [Fitur Sparepart Usage](#3-fitur-sparepart-usage)
4. [Fitur Select2 Search](#4-fitur-select2-search)
5. [Fitur Laporan Tiket (CSV & PDF)](#5-fitur-laporan-tiket-csv--pdf)
6. [Daftar File yang Dimodifikasi](#6-daftar-file-yang-dimodifikasi)
7. [Struktur Database](#7-struktur-database)

---

## 1. Fitur Assign Mekanik & SLA

### Tujuan
Supervisor/Admin dapat menugaskan mekanik (role Operator) ke tiket perbaikan dengan target SLA.

### Alur
```
Tiket Dibuat (OPEN) 
  → Admin/Supervisor klik "Assign Mekanik" 
  → Pilih mekanik + target SLA 
  → Submit → Status auto jadi IN PROGRESS, waktu mulai tercatat
```

### File yang Terlibat

#### Controller: `app/Http/Controllers/TicketController.php`
- **`assignForm()`** — Menampilkan form assign dengan dropdown mekanik aktif
- **`assign()`** — Validasi, update assigned_to, auto set status `in_progress` + `started_at`

#### Routes: `routes/web.php`
```php
Route::get('/tickets/{ticket}/assign', [TicketController::class, 'assignForm'])->name('tickets.assign.form');
Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign'])->name('tickets.assign');
```

#### View: `resources/views/tickets/assign.blade.php`
- Form dengan Select2 dropdown mekanik
- Input target SLA dengan nilai default berdasarkan prioritas
- Informasi tiket (mesin, deskripsi, prioritas, status)

### SLA Target Default per Prioritas
| Prioritas | Target SLA |
|-----------|-----------|
| 🔴 High | 4 jam |
| 🟡 Medium | 8 jam |
| 🟢 Low | 24 jam |

### SLA Status
| Indikator | Kondisi |
|-----------|---------|
| 🟢 **On-Time** | Durasi ≤ 80% target |
| 🟡 **Warning** | Durasi 81% - 100% target |
| 🔴 **Breach** | Durasi > 100% target |

---

## 2. Fitur Dashboard Role-Based

### Tujuan
Dashboard menampilkan data sesuai role user.

### Role Rules
| Role | Data yang Ditampilkan |
|------|----------------------|
| 👨‍🔧 Operator (Mekanik) | Hanya job yang di-assign ke dirinya |
| 👔 Supervisor / Super Admin | Semua job |

### File yang Terlibat

#### Controller: `app/Http/Controllers/DashboardController.php`
- Summary cards: Total Today, Open, In Progress, SLA On-Time
- Query difilter berdasarkan role
- Chart data 7 hari terakhir
- Latest tickets
- Unassigned tickets (khusus admin/supervisor)
- My active tasks (khusus mekanik)

#### Route: `routes/web.php`
```php
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
```

#### View: `resources/views/dashboard.blade.php`
- **Summary Cards** (clickable dengan hover effect):
  - Total Job/Tugas Hari Ini → link ke `tickets.index`
  - Tiket OPEN → link ke `tickets.index?status=open`
  - IN PROGRESS → link ke `tickets.index?status=in_progress`
  - SLA On-Time % → link ke `tickets.index?status=closed`
- **Chart.js** Bar chart stacked (Open, In Progress, Resolved) 7 hari
- **Tugas Aktif Saya** (mekanik) — list tugas yang perlu dikerjakan
- **Tiket Belum di-Assign** (admin/supervisor) — peringatan merah
- **Side Panel**: Tiket Terbaru + Ringkasan Tugas

### Summary Card Hover Effect
```css
.card-hover {
    transition: transform 0.15s ease, box-shadow 0.15s ease;
    cursor: pointer;
}
.card-hover:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}
```

---

## 3. Fitur Sparepart Usage

### Tujuan
Saat menyelesaikan tiket, user dapat memasukkan sparepart yang digunakan. Stok sparepart otomatis berkurang.

### Alur
```
Edit Tiket → Pilih Status Resolved/Closed 
→ Klik "Tambah Sparepart" → Pilih sparepart + qty 
→ Submit → Stok berkurang secara otomatis
```

### File yang Terlibat

#### Migration: `database/migrations/2026_05_25_094829_create_ticket_sparepart_table.php`
```php
Schema::create('ticket_sparepart', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
    $table->foreignId('sparepart_id')->constrained()->onDelete('cascade');
    $table->integer('qty')->unsigned()->default(1);
    $table->timestamps();
});
```

#### Model: `app/Models/Ticket.php`
```php
public function spareparts()
{
    return $this->belongsToMany(Sparepart::class, 'ticket_sparepart')
        ->withPivot('qty')
        ->withTimestamps();
}
```

#### Controller: `app/Http/Controllers/TicketController.php`
- **`edit()`** — Mengirim data `$spareparts` ke view
- **`update()`** — Validasi `spareparts[]` dan `qtys[]`, proses dalam DB transaction:
  1. Cek stok cukup
  2. Kurangi stok (`decrement`)
  3. Simpan pivot (`sync`)

#### View: `resources/views/tickets/edit.blade.php`
- Area sparepart di sebelah kanan form
- Tombol "Tambah Sparepart" untuk menambah row dinamis
- Setiap row: dropdown sparepart (nama + SKU + stok), input qty, badge stok real-time
- Badge stok berubah otomatis saat sparepart dipilih

#### View: `resources/views/tickets/show.blade.php`
- Kartu "Sparepart Digunakan" muncul jika ada sparepart terpakai
- Tabel: Nama Sparepart, SKU, Jumlah

### Contoh Kode: Stock Deduction
```php
DB::transaction(function () use ($request, $ticket, $updateData) {
    $ticket->update($updateData);
    
    if ($request->filled('spareparts')) {
        foreach ($request->spareparts as $index => $sparepartId) {
            $qty = $request->qtys[$index] ?? 1;
            $sparepart = Sparepart::findOrFail($sparepartId);
            
            if ($sparepart->stock < $qty) {
                throw new \Exception("Stok {$sparepart->name} tidak mencukupi.");
            }
            
            $sparepart->decrement('stock', $qty);
            $syncData[$sparepartId] = ['qty' => $qty];
        }
        $ticket->spareparts()->sync($syncData);
    }
});
```

---

## 4. Fitur Select2 Search

### Tujuan
Dropdown pencarian menggunakan Select2 untuk data yang banyak (mesin, mekanik).

### File yang Terlibat
- `resources/views/tickets/create.blade.php` — Dropdown mesin
- `resources/views/tickets/assign.blade.php` — Dropdown mekanik

### Dependencies
```html
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
```

### Konfigurasi
```javascript
$('#machine_id').select2({
    theme: 'bootstrap-5',
    placeholder: '-- Cari & Pilih Mesin --',
    allowClear: true,
    width: '100%',
    language: {
        noResults: function() { return "Data tidak ditemukan"; },
        searching: function() { return "Mencari..."; }
    }
});
```

---

## 5. Fitur Laporan Tiket (CSV & PDF)

### Tujuan
User dapat melihat laporan tiket perbaikan dengan filter tanggal, teknisi, dan status. Hasil laporan bisa di-download dalam format CSV (Excel) atau PDF.

### Alur
```
Buka menu Laporan (sidebar) 
→ Filter: Tanggal Mulai, Tanggal Akhir, Teknisi, Status 
→ Klik Search
→ Lihat preview tabel + summary statistik
→ Klik "Download CSV" → file .csv (bisa buka di Excel)
→ Klik "Download PDF" → file .pdf (landscape A4)
```

### Filter yang Tersedia
| Filter | Tipe | Default |
|--------|------|---------|
| Tanggal Mulai | date (input) | - |
| Tanggal Akhir | date (input) | - |
| Teknisi | dropdown (all / per mekanik) | Semua Teknisi |
| Status | dropdown (all / open / in_progress / resolved / closed) | Semua Status |

### File yang Terlibat

#### Controller: `app/Http/Controllers/ReportController.php`
- **`index()`** — Menampilkan halaman filter + preview tabel + statistik
- **`exportCsv()`** — Stream file CSV dengan BOM UTF-8 (kompatibel Excel)
- **`exportPdf()`** — Generate PDF menggunakan dompdf (landscape A4)
- **`getFilteredTickets()`** — Private method untuk query filter yang digunakan oleh exportCsv dan exportPdf

#### Routes: `routes/web.php`
```php
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/export-csv', [ReportController::class, 'exportCsv'])->name('export.csv');
    Route::get('/export-pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
});
```

#### View: `resources/views/reports/index.blade.php`
- **Filter Card** — Gradient ungu dengan input date, dropdown teknisi & status
- **Statistics Cards** — Total Tiket, Open, In Progress, Resolved, Closed (warna berbeda)
- **Action Buttons** — Download CSV (hijau) + Download PDF (merah)
- **Tabel Preview** — No Tiket (link ke detail), Mesin, Pelapor, Mekanik, Prioritas, Status, SLA, Tanggal Dibuat

#### View: `resources/views/reports/pdf.blade.php`
- Menggunakan font DejaVu Sans (default dompdf)
- **Header** — Judul laporan + periode + teknisi + tanggal cetak
- **Summary Cards** — Total Tiket, Open, In Progress, Selesai (warna-warni)
- **Tabel** — No Tiket, Mesin, Mekanik, Prioritas, Status, Target SLA, Mulai, Selesai

#### Layout: `resources/views/layouts/app.blade.php`
- + Menu "Laporan" di sidebar (icon `bi-file-earmark-bar-graph`)

### Dependencies
```bash
composer require barryvdh/laravel-dompdf
```

### Contoh Output CSV
```
No. Tiket, Mesin, Tag No, Pelapor, Mekanik, Deskripsi, Prioritas, Status, SLA Target (Jam), Mulai, Selesai, Dibuat
TIK-ABC123-20260528, Mesin CNC-01, MC-001, User A, Mekanik B, Bearing rusak, High, In Progress, 4, 28/05/2026 10:00, -, 28/05/2026 09:00
```

### Catatan
- CSV menggunakan **BOM UTF-8** (`EF BB BF`) agar kompatibel dengan Microsoft Excel untuk karakter Indonesia
- PDF menggunakan **A4 landscape** agar tabel cukup lebar menampung semua kolom
- Filter dipertahankan via `request()->query()` saat link download agar export sesuai filter yang dipilih

---

## 6. Daftar File yang Dimodifikasi

### File Baru
| File | Keterangan |
|------|-----------|
| `app/Http/Controllers/DashboardController.php` | Dashboard dengan data real-time & role-based |
| `database/migrations/2026_05_25_094829_create_ticket_sparepart_table.php` | Pivot table ticket_sparepart |
| `app/Http/Controllers/ReportController.php` | Report dengan filter, CSV, dan PDF |
| `resources/views/reports/index.blade.php` | Halaman laporan dengan filter + preview |
| `resources/views/reports/pdf.blade.php` | Template PDF laporan |
| `docs/changelog.md` | Dokumentasi ini |

### File Diubah
| File | Perubahan |
|------|-----------|
| `app/Models/Ticket.php` | + `cast()` untuk datetime, + `assignedMechanic()`, + `spareparts()` |
| `app/Models/User.php` | + `mechanicTickets()` |
| `app/Http/Controllers/TicketController.php` | + `assignForm()`, `assign()`, role-based filtering, sparepart stock deduction |
| `routes/web.php` | Dashboard → DashboardController, + assign routes, + reports routes |
| `resources/views/layouts/app.blade.php` | + Menu "Laporan" di sidebar |
| `resources/views/dashboard.blade.php` | Full rewrite: real-time data, clickable cards, Chart.js, role-based |
| `resources/views/tickets/create.blade.php` | + Select2 untuk dropdown mesin |
| `resources/views/tickets/assign.blade.php` | Full rewrite: + Select2, + info tiket |
| `resources/views/tickets/edit.blade.php` | + sparepart selection dengan dynamic rows |
| `resources/views/tickets/show.blade.php` | + sparepart card, + role-based buttons |
| `resources/views/tickets/index.blade.php` | + mechanic column, + SLA indicator |

### Total File: 16 file (6 baru, 11 diubah)

---

## 7. Struktur Database

### Table: `tickets`
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint | Primary Key |
| ticket_number | string | Format: TIK-XXXXXX-YYYYMMDD |
| machine_id | foreignId | FK → machines |
| user_id | foreignId | FK → users (pelapor) |
| assigned_to | foreignId? | FK → users (mekanik) |
| issue_description | text | Deskripsi kerusakan |
| photo_path | string? | Path foto |
| priority | enum | low, medium, high |
| status | enum | open, in_progress, resolved, closed |
| sla_target_hours | integer? | Target SLA dalam jam |
| started_at | datetime? | Waktu mulai pengerjaan |
| resolved_at | datetime? | Waktu selesai |
| created_at | timestamp | |
| updated_at | timestamp | |

### Table: `ticket_sparepart` (pivot)
| Kolom | Tipe | Keterangan |
|-------|------|-----------|
| id | bigint | Primary Key |
| ticket_id | bigint | FK → tickets (cascade) |
| sparepart_id | bigint | FK → spareparts (cascade) |
| qty | integer | Jumlah sparepart digunakan |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Catatan untuk Developer

### Role yang Ada
- **Super Admin** — Akses penuh
- **Supervisor** — Akses penuh (sama seperti Super Admin untuk fitur saat ini)
- **Operator** — Mekanik, akses terbatas ke tiket sendiri

### Cek Cepat Saat Error
1. Cek syntax: `php -l app/Http/Controllers/NamaController.php`
2. Cek route: `php artisan route:list`
3. Cek migration: `php artisan migrate`
4. Cek model casts: `started_at` dan `resolved_at` harus `'datetime'`

### Aturan Akses Tiket
| Aksi | Super Admin | Supervisor | Operator (Mekanik) |
|------|------------|-----------|-------------------|
| Lihat tiket | Semua | Semua | Hanya tiket sendiri |
| Buat tiket | ✅ | ✅ | ✅ |
| Assign mekanik | ✅ | ✅ | ❌ |
| Update status (semua) | ✅ | ✅ | ❌ |
| Finish (resolved/closed) | ✅ | ✅ | ✅ (tiket sendiri) |
| Hapus tiket | ✅ | ✅ | ❌ |
| Gunakan sparepart | ✅ | ✅ | ✅ (tiket sendiri) |
