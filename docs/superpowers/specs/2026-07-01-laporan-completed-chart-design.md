# Laporan — Completed-Competitions Trend Chart — Design

Date: 2026-07-01
Status: Approved design, pending implementation plan

## 1. Goal

Add a second section to the **Laporan** page (`admin.admin-laporan`), below the existing
active-competitions section, showing a line chart of **completed competitions** with three
series — **Peserta**, **Nomor**, and **Revenue (collected)** — so an admin can see trends
across past events at a glance.

## 2. Background

The page currently renders active competitions only (`buka_pendaftaran <= now <=
waktu_kompetisi`). The report data comes from `LaporanReportService`, which already exposes
`baseRows()` (per-entry rows joined across acara_atlet/atlet/user/acara/kompetisi) and
`revenueByComp()` (distinct-payment revenue split by status). The chart reuses both — no new
query patterns.

No charting library exists in the project. The app loads other libraries via CDN
(`admin-dashboard-layout.blade.php` includes trix, jQuery, phosphor) and already embeds
inline `<script>` in the Laporan page (the export button JS). The chart follows the same
pattern.

## 3. Definitions

- **Completed competition**: `waktu_kompetisi < now` (mirror of the active rule).
- **Ordering**: oldest → newest by `waktu_kompetisi` (natural left-to-right time axis).
- **Revenue**: collected only — sum of distinct `pembayaran.total_harga` where
  `status = Berhasil`, same source as the card stats.

## 4. Data layer

New method `LaporanReportService::completedTrend(): array`.

- Load completed competitions (`waktu_kompetisi < now`, ordered by `waktu_kompetisi` asc).
- Reuse `baseRows($ids)` and `revenueByComp($ids)` over those competition ids.
- Return one entry per competition, in date order:
  ```
  [
    'nama'    => string,   // competition name (X-axis label)
    'tanggal' => 'Y-m-d',  // waktu_kompetisi date
    'peserta' => int,      // unique athletes (by atlet_name, matching the cards)
    'nomor'   => int,      // total entries
    'revenue' => int,      // collected (Berhasil) rupiah
  ]
  ```
- Competitions with zero entries still appear, as zeros (so the timeline has no gaps).
- Returns `[]` when there are no completed competitions.

The controller (`LaporanController::index`) passes this array to the view.

## 5. View / chart

New `<section>` below the active-competitions section in `admin-laporan.blade.php`.

- **Empty state**: when the array is empty, render "Belum ada kompetisi selesai." and no
  chart.
- **Toggle**: two buttons **Last 10** (default) / **Semua**. All data is embedded once as
  JSON; the toggle slices client-side (last 10 vs all) and re-renders — no extra requests.
- **Chart**: Chart.js (CDN `<script>` scoped to this page) drawn into a `<canvas>`.
  - Type: line, 3 datasets.
  - **Left Y-axis (`y`)**: Peserta and Nomor (counts), begins at zero.
  - **Right Y-axis (`y1`)**: Revenue (rupiah); grid lines off (`grid.drawOnChartArea =
    false`); tick labels compact (`Rp …jt`). Full rupiah shown in the tooltip.
  - X-axis: competition name; date available in the tooltip.
  - Distinct colors per series; legend on.
- **Placement**: its own `all-card` section so it visually matches the page.

## 6. Error handling & edge cases

- No completed competitions → empty-state text, chart code not executed.
- Fewer than 10 completed → "Last 10" simply shows all of them; toggle still works.
- Zero-entry competition → plotted at 0 for all three series.
- Revenue with no Berhasil payments → 0 on the revenue line.
- Chart JS guards on the canvas element existing before initializing.

## 7. Testing

- **Unit** (`LaporanReportServiceTest`): seed one completed competition (past
  `waktu_kompetisi`) with entries and a `Berhasil` payment, plus an active and a future
  competition. Assert `completedTrend()` returns only the completed one, with correct
  `peserta`, `nomor`, `revenue`, and that multiple completed competitions come back in
  ascending date order.
- **Feature** (`LaporanPageTest`): the page renders the new section (heading + `<canvas>`)
  when a completed competition exists, and the empty-state text when none.
- The chart rendering itself (JS) is not unit-tested; the data method is the tested unit.

## 8. Out of scope

- Exporting this chart or its data (on-screen only).
- Drill-down, per-series toggling beyond the legend, and date-range pickers other than the
  Last 10 / Semua toggle.
- Any change to the active-competitions section or the export pipeline.
