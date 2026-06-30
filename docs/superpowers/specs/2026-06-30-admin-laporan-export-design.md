# Admin Laporan (Report) Page & Export — Design

Date: 2026-06-30
Status: Approved design, pending implementation plan

## 1. Goal

Add an admin page that acts as a live report for **active competitions** and lets the
admin export report files that mirror the output of the existing Python tool at
`D:\Project\report-automation` (the `laporan/laporan_dir/laporan {date}/` folder), with the
folder name extended to include **time** as well as date.

The admin must be able to export:
- a **single** competition, and
- **all active** competitions at once.

## 2. Background & Key Insight

The Python automation reads two MySQL views, `list_club_payment` and `list_daftar`. Both
depend on `view_acara_atlet`, which is **hardcoded** to `WHERE ac.kompetisi_id = 29`. As a
result the automation only ever reports the one competition someone has manually edited the
view to point at.

The web version does **not** use those views. Instead it replicates their SQL in Laravel,
parameterized by competition id(s), so it works for any competition without editing the
database.

"Active competition" is defined as `buka_pendaftaran <= now <= waktu_kompetisi` — the same
rule the public homepage (`MainPageController`) already uses for ongoing competitions.

### View column layouts to reproduce

`list_club_payment` (per club, grouped by competition):
`No, Club, Email, Nomor Telepon, Total Peserta per Club, Total Nomor per Club,
Total Selesai per Club, Total Menunggu per Club, Nama Kompetisi`
plus a `Total Semua` row per competition (aggregate over the whole competition).

`list_daftar` (per athlete-event row, grouped by competition):
`No, Nama Atlet, Nomor Lomba, Club, Nomor Telepon, Status Pembayaran, Nama Kompetisi`
plus a `Total Atlet` row and a `Total Club` row per competition.

Source-of-truth SQL lives in `views_clean.sql` / `views_only.sql` in the repo root. The
replicated query reproduces the same columns, ordering, phone leading-quote, and total rows,
but filters on the chosen `kompetisi_id`(s) instead of the hardcoded one.

## 3. Components

### a) `app/Services/LaporanReportService.php`

Single source of truth for report data. Follows the existing `app/Services/` convention
(`AtletImportService` already lives there). Given an array of `kompetisi_id`s it returns:

- `clubPaymentRows(array $ids): array` — per-club rows + `Total Semua` row per competition,
  in the exact column order above.
- `daftarRows(array $ids): array` — per athlete-event rows + `Total Atlet` + `Total Club`
  rows per competition.
- `summaries(array $ids): array` — small per-competition metrics for the on-screen preview:
  competition name, total peserta, total nomor, total club, total selesai, total menunggu.
  These are the same numbers `summary.py` extracts.
- `activeCompetitions(): Collection` — `Kompetisi` where `buka_pendaftaran <= now <= waktu_kompetisi`,
  ordered by `waktu_kompetisi`.

The row-building methods also expose, or internally compute, the **max content width per
column** so exports can set column widths without PhpSpreadsheet auto-size (see performance).

The page, both export modes, and the summary preview all call this service. No SQL logic
lives in controllers or export classes.

### b) `app/Exports/Laporan/ListClubPaymentExport.php` and `ListDaftarExport.php`

Each takes pre-computed rows + precomputed column widths and reproduces `excel.py`'s
formatting via `maatwebsite/excel` (already a dependency):
- `WithHeadings` — the column headers above.
- `WithEvents` / `AfterSheet` — add a PhpSpreadsheet `Table` styled `TableStyleMedium2`
  with row stripes; center-align headers; left-align text columns (Club, Email, Nama Atlet,
  Nomor Lomba, Nama Kompetisi), center the rest; set page setup fit-to-width and repeating
  header row (`$1:$1`) for printing; apply the precomputed column widths.
- **No `ShouldAutoSize`** (see performance section).
- Phone columns retain the leading `'` so Excel stores them as text, matching the views.

### c) `app/Http/Controllers/LaporanController.php`

Three actions, all behind the existing `auth` + `role:admin` middleware group:
- `index()` — renders the page with `activeCompetitions()` and their `summaries()`.
- `exportOne($id)` — validates the competition exists (404 otherwise), builds a ZIP for that
  one competition, streams it as a download.
- `exportAllActive()` — builds a ZIP covering all active competitions (combined + per-comp
  split), streams it.

### d) View `resources/views/admin/admin-laporan.blade.php` + sidebar link

Uses the existing `admin-dashboard-layout`. A "Laporan" item is added to
`admin-dashboard-sidebar.blade.php` under the "Main" menu (icon from the Phosphor set already
in use, e.g. `ph-file-text`).

## 4. The Page (live preview + export buttons)

For each active competition, a card showing:
- competition name and event date (`waktu_kompetisi`),
- summary metrics: total peserta, total nomor, total club, selesai vs menunggu,
- an **Export** button (calls `exportOne`).

A page-header **"Export Semua Aktif"** button calls `exportAllActive`.

The full multi-thousand-row lists are **not** rendered on screen — only the summary metrics.
The full rows live exclusively in the exported files.

Empty state: if there are no active competitions, show a message and hide/disable
"Export Semua Aktif".

## 5. Export / ZIP Structure

Folder name includes time, colon-free for filename safety:
`laporan {dd-mm-YYYY HH-MM-SS}` (e.g. `laporan 30-06-2026 14-22-05`).

**Export one competition** → ZIP:
```
laporan 30-06-2026 14-22-05/
  list_club_payment 30-06-2026 14-22-05 {Nama Kompetisi}.xlsx
  list_daftar 30-06-2026 14-22-05 {Nama Kompetisi}.xlsx
```

**Export all active** → ZIP (mirrors the Python `process_files`: combined + per-comp split):
```
laporan 30-06-2026 14-22-05/
  list_club_payment 30-06-2026 14-22-05.xlsx        (all active combined)
  list_daftar 30-06-2026 14-22-05.xlsx              (all active combined)
  list_club_payment 30-06-2026 14-22-05 {Comp A}.xlsx
  list_daftar 30-06-2026 14-22-05 {Comp A}.xlsx
  list_club_payment 30-06-2026 14-22-05 {Comp B}.xlsx
  list_daftar 30-06-2026 14-22-05 {Comp B}.xlsx
  ...
```

Competition names are sanitized for filenames (`/` and `\` → `-`), matching `excel.py`.

Assembly: write each `.xlsx` to a per-request temp directory, add it to a `ZipArchive` under
the dated folder, stream the zip as a download response, then delete the temp directory. No
files are left on the server.

## 6. Performance (shared-hosting safe — synchronous + optimized)

Target environment is shared hosting (`memory_limit` ~128–256 MB, `max_execution_time`
~30–60 s). Realistic worst case per competition is low-thousands of `list_daftar` rows and a
few hundred `list_club_payment` rows (gauged from the largest historical report files:
`list_daftar` ~91 KB, `list_club_payment` ~21 KB).

Single-competition export is light and safe. "Export all active" is the higher-risk path
because it builds combined + per-competition files in one request. Two measures keep peak
cost to roughly a single file rather than the sum of all files:

1. **One file at a time, free memory between files.** Generate a file, write it to temp disk,
   then release the PhpSpreadsheet object (`disconnectWorksheets()` + `unset`) before building
   the next. Peak memory ≈ the single largest sheet (~100 MB worst case), not the cumulative
   total.
2. **No `ShouldAutoSize`; precompute column widths.** Auto-size forces PhpSpreadsheet to scan
   every cell with its width calculator — the dominant CPU cost. Instead, compute max content
   length per column cheaply from the data array already held in `LaporanReportService`
   (same outcome as `excel.py`'s manual width loop) and set widths directly.

These keep synchronous export within shared-hosting limits for current and near-term data.

Explicitly out of scope (deferred, not built): queued background jobs, row-count caps, and
raising `memory_limit`/`set_time_limit` inside the action. If data grows substantially or
multiple admins export concurrently, a queued background job is the documented upgrade path.

## 7. Routes (admin middleware group in `routes/web.php`)

- `GET /admin/dashboard/laporan` → `LaporanController@index`, name `admin.laporan`
- `GET /admin/dashboard/laporan/{id}/export` → `LaporanController@exportOne`, name `admin.laporan.export`
- `GET /admin/dashboard/laporan/export-all` → `LaporanController@exportAllActive`, name `admin.laporan.export-all`

## 8. Error Handling & Edge Cases

- No active competitions → empty-state page; "Export Semua Aktif" hidden/disabled.
- Competition with zero registrations → still produces files (headers + zeroed total rows),
  matching automation behavior.
- `exportOne` 404s on a nonexistent competition id.
- Competition names sanitized before use in filenames.
- Temp directory cleaned up even if zip streaming fails (try/finally).

## 9. Testing

- **Unit — `LaporanReportService`:** seed a competition with known athletes/events/payment
  statuses; assert `clubPaymentRows` / `daftarRows` produce the correct counts and the
  `Total Semua` / `Total Atlet` / `Total Club` rows, cross-checked against the view
  definitions. Assert `activeCompetitions()` filters by the date window correctly.
- **Feature — `LaporanController`:** page renders active competitions and summary metrics;
  `exportOne` returns a ZIP whose entries match the expected folder/filenames; `exportAllActive`
  returns a ZIP with combined + per-competition files; empty-state when no active competitions;
  404 on bad id; admin-only access enforced.

## 10. Conventions

Stays within existing patterns: service class in `app/Services/`, export classes in
`app/Exports/`, controller streaming downloads (as `UnduhanController` already does), admin
routes in the `auth`+`role:admin` group, blade view in `resources/views/admin/`, sidebar link
in `admin-dashboard-sidebar.blade.php`.
