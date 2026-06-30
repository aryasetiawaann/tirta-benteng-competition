# Laporan Kompetisi Aktif — Extra Stats — Design

Date: 2026-07-01
Status: Approved design, pending implementation plan

## 1. Goal

Enrich each competition card on the **Laporan Kompetisi Aktif** page
(`admin.admin-laporan`) with additional statistics covering **finance**, **operational
readiness**, and **participation demographics**, surfaced through a per-card expandable
detail panel. The on-screen card is the only thing that changes — the Excel/zip export
pipeline is untouched.

## 2. Background

The page (`LaporanController::index` → `LaporanReportService::summaries`) currently renders
five core tiles per active competition: **Peserta, Nomor, Club, Selesai, Menunggu**. Active
competition = `buka_pendaftaran <= now <= waktu_kompetisi`.

`summaries()` is built on `baseRows()`, which loads every `acara_atlet` row for the active
competitions into a PHP collection and groups in memory. The new demographic stats reuse
those already-loaded rows; finance and quota stats are added as lightweight DB-side
aggregates.

### Key insight — revenue source of truth

Revenue must **not** be derived from `acara.harga`. Pricing is tiered
(`pricings.event_amount` → `harga`) plus a per-competition `kompetisi.additional_price`, so
per-event prices do not sum to the amount actually charged. The source of truth is the
`pembayaran` table: `total_harga` (integer, actual amount) with `status` ∈
{`Berhasil`, `Menunggu`, `Gagal`, `Kedaluarsa`}. Each `acara_atlet` row links to a
`pembayaran_id`. One payment covers multiple entries, so revenue is the sum of **distinct**
`pembayaran.total_harga` (grouped per payment), never per entry.

Status vocabulary differs by level: entry-level `acara_atlet.status_pembayaran` uses
`Selesai`/`Menunggu`; payment-level `pembayaran.status` uses `Berhasil`/`Menunggu`/… A
"Selesai" entry corresponds to a "Berhasil" payment.

## 3. Stats to add

Displayed in a per-card expandable detail panel, grouped into three labelled sections.

### 💰 Keuangan (Finance)
- **Pendapatan terkumpul** — Σ distinct `pembayaran.total_harga` where `status = Berhasil`,
  for payments linked to this competition's entries.
- **Pendapatan tertunda** — Σ distinct `pembayaran.total_harga` where `status = Menunggu`.
- **Tingkat pelunasan** — `Selesai` entries ÷ total entries, as a percentage.

### 🏊 Operasional (Operational readiness)
- **Sisa hari pendaftaran** — whole days from now to `kompetisi.tutup_pendaftaran`; shows
  "Ditutup" when the deadline has passed.
- **Keterisian kuota** — total entries ÷ Σ `acara.kuota` for the competition, as a
  percentage. Omitted (or shown as "—") when the competition has no quotas set.
- **Jumlah nomor lomba** — count of distinct events (`acara`) that have at least one entry.

### 👥 Partisipasi (Participation)
- **Komposisi gender** — count of male / female athletes (`atlet.jenis_kelamin`),
  counting each athlete once per competition.
- **Rata-rata umur** — average `atlet.umur` across participating athletes.
- **Rata-rata nomor/atlet** — total entries ÷ unique participants.
- **Club terbanyak** — club (`users.club`) with the most participating athletes.

## 4. Data layer changes

All changes live in `LaporanReportService`; the controller and view consume the enriched
summary array.

- **Extend `baseRows()` select** with `at.jenis_kelamin` and `at.umur` (rows are already
  loaded; no extra query). Used for gender, average age, and any per-athlete demographic
  rollups.
- **`summaries()`** gains the new keys per competition: `pendapatan_terkumpul`,
  `pendapatan_tertunda`, `tingkat_pelunasan`, `keterisian_kuota`, `nomor_lomba_count`,
  `gender_l`, `gender_p`, `umur_rata`, `nomor_per_atlet`, `club_terbanyak`. Existing keys
  (`peserta`, `nomor`, `club`, `selesai`, `menunggu`) are unchanged.
- **Revenue aggregate (+1 query)** — a DB-side query joining `pembayaran` through
  `acara_atlet`/`acara`, scoped to the active competition ids, grouped by competition and
  distinct payment, summing `total_harga` split by payment status. Returns one row per
  competition per status bucket — no raw-row loading in PHP.
- **Quota aggregate (+1 query)** — `SUM(acara.kuota)` grouped by `kompetisi_id` over the
  active competition ids. Separate from `baseRows` because it must include events with zero
  entries. Tiny aggregate.
- **Days left** — computed from `tutup_pendaftaran`, already present on the
  `$competitions` collection in the controller; no extra query. Done via a small helper or
  inline in the view.

Net query count for the page: **1 → 3**, the two new ones being grouped aggregates that
return ~one row per active competition. The pre-existing in-memory grouping in `baseRows` is
unchanged and remains the dominant cost; pushing all summary aggregation into SQL is a
possible future refactor but is **out of scope** here.

## 5. View / UX changes

In `resources/views/admin/admin-laporan.blade.php`:

- Keep the existing five `.laporan-stats` tiles visible and unchanged.
- Below them add a native `<details><summary>Detail</summary>…</details>` block (no JS) per
  card containing the three labelled sub-sections. Native `<details>` keeps it dependency-free
  and accessible.
- Style the detail panel to match the existing card aesthetic (the `.laporan-card` /
  `.laporan-stats` look). Money values formatted as Rupiah (e.g. `Rp 1.500.000`); percentages
  to a sensible precision (whole or one decimal).
- The Export button and existing export JS are untouched.

## 6. Edge cases

- **Zero participants** in a competition → no division by zero: completion rate, quota fill,
  average age, and avg entries/athlete all guard against empty denominators and render `0`,
  `—`, or an equivalent placeholder.
- **Null/zero `kuota`** across all events → "Keterisian kuota" shows "—".
- **Multi-entry / tiered payment** → counted once via distinct `pembayaran_id`.
- **Mixed payment statuses** (Berhasil / Menunggu / Gagal / Kedaluarsa) → only Berhasil
  counts as collected, only Menunggu as pending; Gagal/Kedaluarsa excluded.
- **Registration deadline passed** → "Sisa hari pendaftaran" shows "Ditutup".
- **Tie for top club** → deterministic tiebreak (e.g. alphabetical) so output is stable.

## 7. Testing

Extend `tests/Unit/LaporanReportServiceTest.php`:
- Tiered / multi-entry payment counted once toward revenue.
- Mixed Berhasil/Menunggu/Gagal/Kedaluarsa revenue buckets split correctly.
- Null `kuota` yields no quota-fill value; partial quotas compute the right percentage.
- Zero-participant competition produces no division-by-zero and sane placeholders.
- Gender split and average age aggregate correctly, counting each athlete once.
- Top club selection with a tie is deterministic.

Existing export tests (`LaporanExportTest`, `LaporanSheetExportTest`) must remain green,
confirming the export pipeline is unaffected.

## 8. Out of scope

- Any change to the Excel/zip export contents or format.
- Refactoring `baseRows` summary aggregation into pure SQL.
- Demographic breakdowns beyond the listed stats (e.g. province/age-bucket charts).
