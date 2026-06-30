<?php // app/Exports/LaporanSheetExport.php
namespace App\Exports;

use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class LaporanSheetExport implements FromArray, WithHeadings, WithEvents
{
    public function __construct(
        private array $headings,
        private array $rows,
        private array $leftAlignHeadings = [],
    ) {
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function array(): array
    {
        return array_map(
            fn ($row) => array_map(fn ($h) => array_key_exists($h, $row) ? $row[$h] : '', $this->headings),
            $this->rows,
        );
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $data = $this->array();
                $colCount = count($this->headings);
                $lastRow = count($data) + 1; // + header
                $lastCol = Coordinate::stringFromColumnIndex($colCount);

                // Precomputed column widths (no ShouldAutoSize): maxlen + 2.
                foreach ($this->headings as $i => $heading) {
                    $maxLen = strlen((string) $heading);
                    foreach ($data as $r) {
                        $maxLen = max($maxLen, strlen((string) ($r[$i] ?? '')));
                    }
                    $letter = Coordinate::stringFromColumnIndex($i + 1);
                    $sheet->getColumnDimension($letter)->setWidth($maxLen + 2);
                }

                // Header centered.
                $sheet->getStyle("A1:{$lastCol}1")->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Data: center all, then left-align specified columns.
                if ($lastRow > 1) {
                    $sheet->getStyle("A2:{$lastCol}{$lastRow}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    foreach ($this->leftAlignHeadings as $h) {
                        $idx = array_search($h, $this->headings, true);
                        if ($idx !== false) {
                            $letter = Coordinate::stringFromColumnIndex($idx + 1);
                            $sheet->getStyle("{$letter}2:{$letter}{$lastRow}")->getAlignment()
                                ->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        }
                    }
                }

                // Excel table style with row stripes.
                $table = new Table("A1:{$lastCol}{$lastRow}", 'Table_' . Str::random(8));
                $table->setStyle(
                    (new TableStyle())
                        ->setTheme(TableStyle::TABLE_STYLE_MEDIUM2)
                        ->setShowRowStripes(true)
                );
                $sheet->addTable($table);

                // Print: fit to width, repeat header row.
                $sheet->getPageSetup()->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);
            },
        ];
    }
}
