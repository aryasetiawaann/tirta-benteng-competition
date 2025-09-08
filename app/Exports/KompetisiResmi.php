<?php

namespace App\Exports;

use App\Models\Acara;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Style\Border; 
use Carbon\Carbon;

class KompetisiResmi implements FromCollection, WithMapping, WithEvents
{
    protected $acaras;
    protected $maxLanes;
    protected $kompetisi;

    public function __construct($acaras, $maxLanes, $kompetisi)
    {
        $this->acaras = $acaras;
        $this->maxLanes = $maxLanes;
        $this->kompetisi = $kompetisi;
    }

    /**
     * Mengambil data yang akan diekspor.
     */
    public function collection()
    {
        return $this->acaras;
    }

    /**
     * Mapping data dari database ke kolom-kolom Excel.
     */
    public function map($acara): array
    {
        return [];
    }

    private function hasParticipants(Acara $acara): bool
    {
        foreach ($acara->heats as $heat) {
            foreach ($heat as $participant) {
                if ($participant) {
                    return true;
                }
            }
        }
        return false;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                 $sheet = $event->sheet->getDelegate();

                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);

                // Set orientasi Portrait (bisa diganti Landscape)
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_PORTRAIT);

                // Set Footer
                $sheet->getHeaderFooter()->setOddFooter('&LDicetak pada &D &T');

                $waktu = Carbon::parse($this->kompetisi->waktu_kompetisi)->translatedFormat('j F Y');

                $sheet->getHeaderFooter()->setOddHeader(
                    "&C {$this->kompetisi->nama}\n{$this->kompetisi->lokasi}\n{$waktu}"
                );

                // Atur gaya default
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(10);
                $sheet->getParent()->getDefaultStyle()->applyFromArray([
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Gaya untuk header
                $headerStyle = [
                    'font' => [
                        'size' => 9,
                        'bold' => true,
                        'color' => ['argb' => '000000'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '7BDFF2'],
                    ],
                    'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ]
                ];

                $seriStyle = [
                    'font' => [
                        'size' => 9,
                        'underline' => true,
                        'bold' => true,
                        'color' => ['argb' => '000000'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'B2F7EF']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $subHeaderStyle = [
                    'font' => [
                        'size' => 9,
                        'bold' => true,
                        'color' => ['argb' => '000000'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'F2F2F2']
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ];

                $currentRow = 1;

                foreach ($this->acaras as $acara) {

                    $kategori = strtoupper($acara->kategori); // Default uppercase kategori
                    if ($acara->kategori == 'Wanita') {
                        $kategori = 'PUTRI';
                    } elseif ($acara->kategori == 'Pria') {
                        $kategori = 'PUTRA';
                    } elseif ($acara->kategori == 'Campuran') {
                        $kategori = 'CAMPURAN';
                    }

                    // Header
                    $sheet->setCellValue("A$currentRow", 'ACARA ' . $acara->nomor_lomba);
                    $sheet->mergeCells("B$currentRow:C$currentRow");
                    $sheet->setCellValue("B$currentRow", ' (KU ' . strtoupper($acara->grup) 
                    . ') ');
                    $sheet->mergeCells("D$currentRow:E$currentRow");
                    $sheet->setCellValue("D$currentRow", $acara->nama);
                    $sheet->setCellValue("F$currentRow", $kategori);
                    $sheet->getStyle("A$currentRow:F$currentRow")->applyFromArray($headerStyle);

                    $currentRow++;

                    if ($this->hasParticipants($acara))
                    {
                        $lastSerieIndex = array_key_last($acara->heats);

                        foreach ($acara->heats as $serieIndex => $heat) 
                        {
                            // Seri
                            $sheet->setCellValue("A$currentRow", "Seri " . ($serieIndex + 1));
                            $sheet->getStyle("A$currentRow")->applyFromArray($seriStyle);
                            $currentRow++;

                            // Subheader
                            $sheet->setCellValue("A$currentRow", "Ln.");
                            $sheet->setCellValue("B$currentRow", "Nama");
                            $sheet->setCellValue("C$currentRow", "KU");
                            $sheet->setCellValue("D$currentRow", "Asal Sekolah/Klub");
                            $sheet->setCellValue("E$currentRow", "QET");
                            $sheet->setCellValue("F$currentRow", "Hasil");
                            $sheet->getStyle("A$currentRow:F$currentRow")->applyFromArray($subHeaderStyle);
                            $currentRow++;

                            $this->applySeriStyle($sheet, $currentRow);

                            foreach($heat as $laneIndex => $participant)
                            {
                                if($participant)
                                {
                                    $trackRecordFormatted = sprintf('%02d:%02d.%02d',
                                            floor($participant['track_record'] / 60),
                                            floor(fmod($participant['track_record'], 60)),
                                            round(($participant['track_record'] - floor($participant['track_record'])) * 100)
                                        );

                                        $sheet->fromArray([[
                                            $laneIndex + 1,
                                            $participant['name'],
                                            'KU ' . $acara->grup,
                                            $participant['club'],
                                            $participant['track_record'] == 999 ? 'NT' : $trackRecordFormatted,
                                            '',
                                        ]], null, "A$currentRow");
                                }
                                else
                                {
                                    $sheet->fromArray([[
                                            $laneIndex + 1,
                                            '', '', '', '', ''
                                        ]], null, "A$currentRow");
                                }

                                $currentRow++;
                            }
                        }

                        if($serieIndex == $lastSerieIndex){
                            $currentRow++;
                        }
                    }
                    else
                    {
                        $currentRow += 1;
                    }
                }

                // Custom width
                $this->adjustColumnWidths($sheet);
                $sheet->getDefaultRowDimension()->setRowHeight(20);
            },
        ];
    }

    // Merge untuk SERI
    private function applySeriStyle($sheet, $currentRow)
    {
        $startSeriRow = $currentRow; // Setelah header
        $endSeriRow = $startSeriRow + ($this->maxLanes - 1); // baris per SERI

        // Set LINTASAN jadi di tengah
        $sheet->getStyle("A$startSeriRow:A$endSeriRow")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Set KU ke tengah
        $sheet->getStyle("C$startSeriRow:C$endSeriRow")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Set QET dan HASIL ke tengah
        $sheet->getStyle("E$startSeriRow:F$endSeriRow")->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }

    // Custom lebar kolom
    private function adjustColumnWidths($sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(10); // LINT
        $sheet->getColumnDimension('B')->setWidth(25); // NAMA
        $sheet->getColumnDimension('C')->setWidth(10); // KU
        $sheet->getColumnDimension('D')->setWidth(25); // ASALH SEKOLAH
        $sheet->getColumnDimension('E')->setWidth(15); // QET
        $sheet->getColumnDimension('F')->setWidth(15); // HASIL
    }

    
}
