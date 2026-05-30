# 📘 Product Requirements Document (PRD)
## Maintenance Management System (MMS)

**Versi:** MVP v1.0  
**Disusun oleh:** Senior Product Manager – Manufacturing Division  
**Tanggal:** 2024-05-20  
**Status:** Final Draft for Development  
**Lingkungan:** Intranet Pabrik (Offline/No Internet)  

---

## 📋 Daftar Isi
1. [Executive Summary](#1-executive-summary)
2. [User Personas & Role Mapping](#2-user-personas--role-mapping)
3. [Functional Requirements (User Stories)](#3-functional-requirements-user-stories)
4. [Non-Functional Requirements](#4-non-functional-requirements)
5. [Diagram Alir & State Machine](#5-diagram-alir--state-machine)
6. [Milestones & Roadmap](#6-milestones--roadmap)
7. [Lampiran Teknis](#7-lampiran-teknis)

---

## 1. Executive Summary

### 1.1 Latar Belakang Masalah
Pabrik garment saat ini mengelola perawatan ribuan mesin sewing dan peralatan pendukung secara manual melalui pesan teks dan catatan fisik. Dampak yang terjadi:
- ❌ Status perbaikan tidak terlacak secara real-time
- ❌ Tidak ada perhitungan SLA objektif untuk mengukur efisiensi
- ❌ Pencatatan sparepart terpisah dari tiket perbaikan
- ❌ Risiko kehilangan data riwayat & sulit audit

### 1.2 Solusi & Lingkup MVP
**Maintenance Management System (MMS)** adalah aplikasi web berbasis intranet yang mendigitalisasi alur permintaan perbaikan, penugasan mekanik, tracking waktu kerja, kalkulasi SLA otomatis, serta pencatatan sparepart terintegrasi.

| Fitur MVP | Status |
|-----------|--------|
| Form input permintaan perbaikan (scan QR/manual) | ✅ In Scope |
| Assign pekerjaan ke mekanik | ✅ In Scope |
| Update status (OPEN → IN PROGRESS → CLOSED) | ✅ In Scope |
| Input durasi & kalkulasi SLA otomatis | ✅ In Scope |
| Dashboard chart 7 hari terakhir | ✅ In Scope |
| Riwayat pekerjaan filter tanggal & laporan per mekanik | ✅ In Scope |
| Input/output sparepart gudang & link ke job | ✅ In Scope |
| Role-based access (3 level) | ✅ In Scope |

### 1.3 Nilai Bisnis Utama
- ⏱️ Transparansi status perbaikan real-time
- 📊 SLA otomatis → baseline efisiensi tim maintenance
- 📦 Kontrol inventori sparepart akurat per job
- 🌐 100% intranet → cepat, aman, tanpa ketergantungan internet

---

## 2. User Personas & Role Mapping

### 2.1 Persona Pengguna
| Persona | Peran di Pabrik | Tujuan Utama | Role Login |
|---------|----------------|-------------|------------|
| Operator Mesin | Pengguna mesin shop floor | Laporkan kerusakan cepat & akurat | `Operator` |
| Operator Gudang | Pengelola stok sparepart | Catat keluar/masuk part & link ke job | `Operator` |
| Mekanik | Teknisi perbaikan | Terima tugas, update progres, catat waktu & part | `Operator` |
| Supervisor Produksi | Monitoring lini produksi | Pantau status mesin & estimasi selesai | `Supervisor` |
| Supervisor Mekanik | Koordinator tim maintenance | Assign tugas, track SLA, evaluasi performa | `Supervisor` |
| Manager Pabrik | Decision maker | Lihat laporan efisiensi & tren kerusakan | `Supervisor` |
| Super Admin | IT/System Admin | Kelola user, master data, konfigurasi SLA | `Super Admin` |

### 2.2 Matriks Hak Akses (RBAC)
| Fitur | Operator | Supervisor | Super Admin |
|-------|----------|------------|-------------|
| Input Request Perbaikan | ✅ | ❌ | ✅ |
| View Dashboard & Chart | ❌ | ✅ | ✅ |
| Assign Job ke Mekanik | ❌ | ✅ | ✅ |
| Update Status Job | ✅ (hanya own assigned) | ✅ (override) | ✅ |
| Input/Output Sparepart | ✅ | ❌ | ✅ |
| Link Sparepart ke Job | ✅ | ❌ | ✅ |
| Export Laporan (CSV/PDF) | ❌ | ✅ | ✅ |
| Manajemen User & Master Data | ❌ | ❌ | ✅ |
| Konfigurasi SLA Rules | ❌ | ❌ | ✅ |

---

## 3. Functional Requirements (User Stories)

### 🟦 US-01: Input Permintaan Perbaikan

As an Operator Mesin,
I want to submit a repair request by scanning a machine QR code or selecting it manually,
So that the maintenance team receives a real-time ticket with accurate machine location & damage type.
Acceptance Criteria:
Form menampilkan: Nama/ID Mesin (dropdown + QR scan), Lantai (dropdown), Jenis Kerusakan (dropdown wajib)
QR scan menggunakan kamera perangkat via Web API dengan fallback manual input
Timestamp request tercatat otomatis (server time)
Data tersimpan dengan status default OPEN
Notifikasi visual "Request berhasil dikirim" muncul setelah submit
Validasi: field wajib tidak boleh kosong


### 🟦 US-02: Kelola Status Pekerjaan (Mekanik)
As a Mekanik,
I want to view assigned tasks and update their status to IN PROGRESS or CLOSED,
So that supervisors know exactly which job is being handled and when it's finished.
Acceptance Criteria:
Halaman "Tugas Saya" hanya menampilkan job OPEN/IN PROGRESS yang di-assign ke mekanik login
Tombol aksi: Mulai Perbaikan (ubah ke IN PROGRESS) dan Selesai (ubah ke CLOSED)
Perubahan status tercatat di audit log (user_id, old_status, new_status, timestamp)
Job CLOSED tidak dapat diubah statusnya kecuali oleh Supervisor/Admin

### 🟦 US-03: Input Durasi & Kalkulasi SLAs a Mekanik,
I want to input the actual repair duration and mark completion time,
So that the system can automatically calculate SLA compliance.
Acceptance Criteria:
Saat status jadi IN PROGRESS, sistem mencatat start_time otomatis
Saat status jadi CLOSED, sistem mencatat end_time dan menghitung duration_hours
Sistem membandingkan duration_hours dengan target_sla dari master data
Indikator visual SLA: 🟢 On-Time, 🟡 Warning (>80%), 🔴 Breach (>100%)
Field catatan opsional tersedia untuk mekanik

### 🟦 US-04: Kelola Stok Sparepart & Link ke Job
As an Operator Gudang,
I want to record sparepart stock in/out and link them to specific repair jobs,
So that inventory is always updated per maintenance activity.
Acceptance Criteria:
Form input: Kode/Nama Part (searchable), Qty, Tanggal, Referensi Job ID
Saat part di-link ke job, stok berkurang otomatis & tercatat di tabel relasi
Validasi: Qty output tidak boleh melebihi stok tersedia → tampilkan error
Riwayat transaksi sparepart dapat difilter per tanggal & part

### 🟦 US-05: Dashboard Monitoring 7 Hari
As a Supervisor,
I want to view a dashboard with a chart showing maintenance activities from the last 7 days,
So that I can quickly spot bottlenecks and workload distribution.
Acceptance Criteria:
Chart utama: Bar chart jumlah job per status (OPEN, IN PROGRESS, CLOSED) per hari (7 hari terakhir)
Card summary: Total job hari ini, % SLA on-time, rata-rata durasi
Data update real-time via polling setiap 30 detik
Responsive: chart menyesuaikan lebar layar tablet/desktop

### 🟦 US-06: Riwayat & Laporan Mekanik
As a Supervisor,
I want to browse maintenance history filtered by date and view individual mechanic reports,
So that I can audit performance and track recurring issues.
Acceptance Criteria:
Tabel riwayat: Tanggal, ID Job, Mesin, Lantai, Kerusakan, Mekanik, Status, Durasi, SLA Status
Filter: Tanggal (range), Status, Mekanik, Lantai
Export CSV & PDF
Klik ID Job → modal detail: sparepart terpakai, timeline status, catatan mekanik

### 🟦 US-07: Manajemen Sistem (Super Admin)
As a Super Admin,
I want to manage users, roles, machine master data, sparepart catalog, and SLA rules,
So that the system remains secure, accurate, and adaptable to factory changes.
Acceptance Criteria:
CRUD User: Nama, Email, Password, Role, Status Aktif
Master Mesin: ID, Nama, Model, Lantai, Generate & Print QR Code (PNG)
Master Sparepart: SKU, Nama, Kategori, Stok, Min/Max Alert
Konfigurasi SLA: Rule per [Kategori Mesin + Jenis Kerusakan] → Target SLA (jam)
Backup database manual + jadwal otomatis harian


---

## 4. Non-Functional Requirements

### 4.1 Teknologi & Arsitektur
| Komponen | Spesifikasi |
|----------|-------------|
| Backend | PHP 8.2+, Laravel 10+, RESTful API ready, Sanctum untuk future mobile |
| Frontend | Bootstrap 5.3, Blade templating, Alpine.js (lightweight) |
| Database | MySQL 8.0, InnoDB, UTF8MB4 |
| QR Library | `html5-qrcode` (scan), `endroid/qr-code` (generate) |
| Server | Linux/Windows, Apache/Nginx, PHP-FPM |

### 4.2 Infrastruktur & Data
- ✅ 100% on-premise di server intranet pabrik
- ✅ Tidak ada koneksi eksternal/cloud untuk data transaksi
- ✅ Backup harian otomatis ke storage lokal terpisah
- ✅ Retensi backup: 30 hari rolling

### 4.3 Keamanan & Akses
| Aspek | Implementasi |
|-------|--------------|
| Auth | Login form + CSRF, bcrypt password, session timeout 30 menit |
| Authorization | RBAC via Laravel middleware (Operator, Supervisor, Super Admin) |
| Data Protection | Input sanitization, output escaping, prepared statements (anti-SQLi/XSS) |
| Audit Log | Tabel `activity_logs` mencatat: user, action, old/new values, IP, timestamp |

### 4.4 Desain & UX
- 📱 Responsive mobile-first, optimized untuk tablet 7–10 inch
- 🖱️ Touch-friendly buttons (min 44px), navigasi maksimal 3 klik untuk input job
- 🎨 Warna status konsisten: OPEN=🔴, IN PROGRESS=🟡, CLOSED=🟢, SLA Breach=🔴
- ♿ Kontras tinggi (WCAG AA), label form terhubung, keyboard navigation support

### 4.5 Performa & Skalabilitas
| Metric | Target |
|--------|--------|
| Page Load (LAN 100Mbps) | < 2 detik |
| API Response Time (p95) | < 500ms |
| Concurrent Users | 50+ aktif simultan |
| Optimasi DB | Indexing pada `status`, `created_at`, `machine_id`, `assigned_to` |
| Future Ready | Struktur Resource Controller + DTO, endpoint `/api/v1/*` siap diaktifkan |

---

## 5. Diagram Alir & State Machine

### 5.1 Workflow Utama

mermaid 
flowchart TD
    subgraph Operator Area
        A[Scan QR Mesin / Input Manual] --> B{Validasi QR & Master Data}
        B -->|Valid| C[Buat Tiket → Status: OPEN]
        B -->|Invalid| D[Tampilkan Error → Scan Ulang]
    end

    subgraph Supervisor Area
        C --> E[Review & Assign ke Mekanik]
        E --> F{Mekanik Tersedia?}
        F -->|Ya| G[Ubah Status: IN_PROGRESS]
        F -->|Tidak| H[Tunda / Reassign]
    end

    subgraph Mekanik & Gudang Area
        G --> I[Mulai Perbaikan → Catat start_time]
        I --> J{Butuh Sparepart?}
        J -->|Ya| K[Operator Gudang: Input Output Part]
        K --> L{Stok Cukup?}
        L -->|Ya| M[Kurangi Stok → Link ke Job]
        L -->|Tidak| N[Hold Job → Request Pembelian]
        J -->|Tidak| O[Selesai Perbaikan]
        M --> O
    end

    subgraph System & Monitoring
        O --> P[Input end_time → Status: CLOSED]
        P --> Q{Hitung SLA Otomatis}
        Q -->|≤ Target| R[SLA: ✅ On-Time]
        Q -->|> Target| S[SLA: ⚠️ Breach]
        R & S --> T[Dashboard Real-Time Update]
        T --> U[Export Laporan / Audit Log]
    end

### 5.2 State Machine Status Job (Mermaid)
    stateDiagram-v2
    [*] --> OPEN : Operator Submit Request
    OPEN --> IN_PROGRESS : Supervisor Assign + Mekanik Start
    OPEN --> CANCELLED : Supervisor Cancel
    
    IN_PROGRESS --> CLOSED : Mekanik Finish + Input end_time
    IN_PROGRESS --> CANCELLED : Supervisor Cancel
    
    CLOSED --> [*] : Terminal State
    CANCELLED --> [*] : Terminal State

    note right of OPEN
      Menunggu assignment
      Dapat diedit creator
    end note
    
    note right of IN_PROGRESS
      start_time tercatat
      Sparepart dapat di-link
    end note
    
    note right of CLOSED
      end_time tercatat
      SLA dihitung otomatis
      Immutable (tidak bisa diubah)
    end note

## 6. Milestones & Roadmap
| Fase | Minggu | Deliverable Utama | Kriteria Selesai (DoD) |
|------|--------|-------------------|------------------------|
| Discovery & Design | 1–2 | PRD Final, Wireframe, ERD, Template Master Data | ✅ Sign-off stakeholder & validasi UX |
| Backend Core | 3–4 | Laravel Auth, CRUD API, SLA Engine, Migration | ✅ Unit test >80%, API docs ready |
| Frontend MVP | 5–7 | UI Responsif, QR Scanner, Dashboard, Role Logic | ✅ 7 User Stories pass, 0 critical bug |
| QA & Go-Live | 8 | UAT Report, Backup Config, Training, QR Deployment | ✅ Zero P0/P1, 100% RBAC verified |
| Post-Launch | 9+ | Monitoring, API Docs, Feedback Session, V2 Backlog | ✅ SLA akurat >95%, adopsi >80% |

### 6.2 Roadmap Fitur V2 (Post-MVP)
🔜 Q2 2026: WebSocket notifikasi real-time, Predictive maintenance (jam operasi), Mobile app native via Laravel API
🔜 Q3 2026: Integrasi ERP existing, Advanced analytics (MTTR, MTBF, cost/repair), Multi-plant support
🔜 Q4 2026: AI sparepart recommendation, Voice input hands-free

## 7. Lampiran Teknis
### 7.1 Skema Database (Inti)
| Nama Tabel | Tujuan Utama | Kolom Kunci | Relasi Utama |
|------------|--------------|-------------|--------------|
| `users` | Akun login & role | `id`, `email`, `role`, `password` | 1:N ke `repair_jobs` |
| `machines` | Master data mesin | `id`, `code`, `name`, `floor`, `qr_code` | 1:N ke `repair_jobs` |
| `spareparts` | Katalog & stok part | `id`, `sku`, `name`, `current_stock`, `min_stock` | 1:N ke `job_spareparts` |
| `repair_jobs` | Tiket maintenance | `id`, `ticket_number`, `status`, `start_time`, `end_time`, `sla_status` | N:1 ke `machines`, `users` |
| `job_spareparts` | Pemakaian part per job | `id`, `job_id`, `sparepart_id`, `qty_used`, `unit_price` | N:1 ke `repair_jobs`, `spareparts` |
| `sla_rules` | Konfigurasi target waktu | `id`, `machine_category`, `damage_type`, `target_hours` | Referensi saat job dibuat |
| `activity_logs` | Audit trail sistem | `id`, `user_id`, `action`, `entity_type`, `old_values`, `new_values` | N:1 ke `users` |
### 7.2 Contoh Konfigurasi SLA Rules
| Kategori Mesin | Jenis Kerusakan | Target SLA (Jam) | Indikator |
|----------------|-----------------|------------------|-----------|
| Sewing Juki/Typical | Needle Break | 2 | 🟢 On-Time |
| Sewing Juki/Typical | Motor/Drive Failure | 6 | 🟡 Warning |
| Cutting Machine | Blade Dull | 3 | 🟢 On-Time |
| Overlock/Edging | Thread Tension | 1.5 | 🟢 On-Time |
| All Machines | Major Electrical | 12 | 🔴 Breach |
### 7.3 Contoh Payload API (Future Mobile)
// POST /api/v1/jobs
{
  "machine_id": "MCH-SEW-00123",
  "damage_type": "needle_break",
  "description": "Jarum patah saat operasi speed tinggi",
  "floor": "2",
  "reported_by": 45
}

// Response 201 Created
{
  "data": {
    "id": 1024,
    "ticket_number": "MMS-20260520-1024",
    "status": "OPEN",
    "created_at": "2026-05-20T08:30:00+07:00"
  }
}

### 7.4 Checklist Pra-Go-Live
Server: OS update, PHP/MySQL version verified
Database: Migration run, seeder master data executed
Security: CSRF, XSS, SQLi test passed; password policy enforced
Backup: Cron job backup harian terkonfirmasi berjalan
Network: Akses intranet dari semua titik shop floor teruji
Hardware: Tablet/kiosk di shop floor sudah install browser compatible
Training: SOP booklet dicetak, session training dijadwalkan
QR: Sticker QR semua mesin sudah ditempel & discan test
Rollback: Script rollback deploy disiapkan (jika emergency)
📝 Catatan Revisi & Versioning
Dokumen ini adalah living document. Setiap perubahan prioritas MVP atau penambahan fitur harus melalui proses change request dan update versi dokumen.
Versi Saat Ini: MMS-PRD-v1.0
Next Review Date: 2024-06-05
✅ Dokumen siap untuk handed over ke Engineering Lead, QA Team, dan Stakeholder Factory.
