# Atlet Import Feature — Design Spec
**Date:** 2026-05-23  
**Status:** Approved

---

## Overview

An admin-only page to bulk-import club registrations from the official multi-sheet XLSX template. A single upload creates/reuses the club user account, creates/reuses athlete records, and registers each athlete to the correct events with payment marked as complete.

---

## XLSX Format

The official template has three sheets:

| Sheet | Purpose |
|---|---|
| `Info Klub` | Club contact info; row 1 = headers, row 2+ = data (multiple PICs, same email) |
| `Referensi` | Acara list for this competition. Columns: `id, jenis_lomba, nomor_lomba, nama, kategori, grup, min_umur, [max_umur,] label` |
| `Input Atlet` | Athlete registrations. Row 1 = headers, row 2+ = data |

**Info Klub columns:** `Nama Club, PIC, Nomor HP, Email, Alamat`

**Input Atlet columns:** `No, Nama Lengkap, Tanggal Lahir, Tahun Lahir, Jenis Kelamin, Nomor Lomba 1–7, Nama Dokumen, Catatan`

- `Tanggal Lahir` may be a numeric Excel date serial (e.g. `44307`) or a text date
- `Jenis Kelamin` is `Pria` or `Wanita`
- `Nomor Lomba` values match the `label` column in Referensi (e.g. `"2 - KU A - 25M Kaki Bebas Papan - Wanita"`)

---

## Data Flow

```
Admin: selects kompetisi + uploads XLSX
  ↓
AdminController@importAtlet
  ↓
AtletImport (WithMultipleSheets)
  ├─ InfoKlubSheetImport    → find or create User by email
  ├─ ReferensiSheetImport   → build label→acara_id map, validate against kompetisi_id
  └─ InputAtletSheetImport  → for each athlete row:
       1. Parse birth date (Excel serial or text)
       2. Find or create Atlet by (name, user_id)
       3. For each Nomor Lomba 1–7:
           - Look up acara_id from Referensi map
           - Skip if already registered (Peserta exists for atlet+acara)
           - Create Peserta (status_pembayaran = Selesai)
       4. After all rows: create one Pembayaran (status=Berhasil)
          linked to all Peserta created in this import
  ↓
Result summary → flash to session → redirect to result view
```

---

## Business Rules

**User account**
- Match by email (case-insensitive)
- If not found: create with `name=PIC` (first Info Klub data row), `club=Nama Club`, `phone=Nomor HP`, `role=user`, `password=random 12-char`
- Generated password shown **once** in result summary; admin must communicate it to the club
- If found: use existing user, do not update any fields

**Athletes**
- Match by `(name, user_id)` — case-insensitive trim
- If not found: create with `umur=parsed_date`, `jenis_kelamin`, `user_id`, `is_verified=true`, `nik=null` (not available in XLSX)
- If found: reuse, skip creation

**Event registration (Peserta)**
- Skip Nomor Lomba cells that are empty or null
- Look up label in Referensi map → get `acara_id`
- If acara_id not found in map: log error row, skip
- If Peserta already exists for `(atlet_id, acara_id)`: skip silently
- Otherwise: create `Peserta` with `status_pembayaran=Selesai`, `peserta_user_id=user_id`

**Payment (Pembayaran)**
- Create one Pembayaran per import run per user
- `status=Berhasil`, `total_harga=sum of acara.harga for all Peserta created`
- `midtrans_order_id='IMPORT-{timestamp}-{userId}'`
- Update all newly created Peserta with `pembayaran_id`

**Skipped rows**
- Row with empty `Nama Lengkap` → skip silently
- Acara label not in Referensi map → log: `"Row {n}: nomor lomba '{label}' not found in referensi"`
- Acara not in selected kompetisi → log: `"Row {n}: acara {id} does not belong to kompetisi {id}"`

---

## Files

**New:**
- `app/Imports/AtletImport.php` — entry point, `WithMultipleSheets`
- `app/Imports/Sheets/InfoKlubSheetImport.php`
- `app/Imports/Sheets/ReferensiSheetImport.php`
- `app/Imports/Sheets/InputAtletSheetImport.php`
- `resources/views/admin/admin-import-atlet.blade.php`

**Modified:**
- `app/Http/Controllers/AdminController.php` — add `importAtletForm()`, `importAtlet()`
- `routes/web.php` — add two routes in admin middleware group

---

## Routes

```
GET  /admin/dashboard/import-atlet   admin.import.atlet.form
POST /admin/dashboard/import-atlet   admin.import.atlet
```

---

## Result Summary Structure

```php
[
    'user_created'      => bool,
    'user_email'        => string,
    'user_password'     => string|null,  // only shown if newly created
    'athletes_new'      => int,
    'athletes_reused'   => int,
    'registrations'     => int,
    'pembayaran_total'  => int,          // in rupiah
    'errors'            => string[],     // per-row error messages
]
```

---

## UI

Single page at `/admin/dashboard/import-atlet`:
- Dropdown: select kompetisi
- File input: `.xlsx` only
- Submit button
- After POST: same page shows the result summary (user info with password if new, counts, error list)
