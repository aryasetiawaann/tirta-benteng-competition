<?php

namespace App\Exports;

use App\Models\Acara;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;  


class KompetisiExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
{
    protected $acaras;

    public function __construct($acaras)
    {
        $this->acaras = $acaras;
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
        $rows = [];

        // Tambahkan baris untuk ACARA
        $rows[] = [
            'ACARA ' . $acara->nomor_lomba,
            $acara->nama,
            '', '', '', '', '', '', ''
        ];

        $rows[] = [
            'SERI', 'GRUP', 'LINT', 'NAMA', 'ASAL SEKOLAH / KLUB', 'QET', 'HASIL'
        ];

        foreach ($acara->heats as $serieIndex => $heat) {
            foreach ($heat as $groupIndex => $group) {
                if($group) {
                    foreach ($group as $laneIndex => $participant) {
                        if ($participant) {
                            $trackRecordFormatted = sprintf('%02d:%02d.%02d', 
                            floor($participant['track_record'] / 60),  // Menit
                            floor(fmod($participant['track_record'], 60)),  // Detik
                            round(($participant['track_record'] - floor($participant['track_record'])) * 100) // Milisekon
                            );

                                // Jika kategori fun, tambahkan kolom GRUP
                                $rows[] = [
                                    $serieIndex + 1, // SERI
                                    $groupIndex == 0 ? 'A' : 'B', // GRUP
                                    $laneIndex + 1,  // LINT
                                    $participant['name'],  // NAMA
                                    $participant['club'],  // ASAL SEKOLAH / KLUB
                                    $participant['track_record'] == 999 ? 'NT' : $trackRecordFormatted, // QET
                                    '', // HASIL
                                ];
                        } else {
                            // Jika tidak ada peserta
                            
                            $rows[] = [
                                $serieIndex + 1, // SERI
                                $groupIndex == 0 ? 'A' : 'B', // GRUP
                                $laneIndex + 1,  // LINT
                                '', '', '', '', // Kosong
                            ];
                        }
                    }
                }
            }
        }            

        $rows[] = [];
        
        return $rows;
    }

    /**
     * Memeriksa apakah sebuah acara memiliki peserta.
     *
     * @param \App\Models\Acara $acara
     * @return bool
     */
    private function hasParticipants(Acara $acara): bool
    {
        foreach ($acara->heats as $heat) {
            foreach ($heat as $group) {
                foreach ($group as $participant) {
                    if ($participant) {
                        return true;
                    }
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

                // Atur gaya default
                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Courier New');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(12);

                // Gaya untuk header
                $headerStyle = [
                    'font' => [
                        'color' => ['argb' => 'FFFFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['argb' => '008DDA'],
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];

                // Gaya untuk konten
                $contentStyle = [
                    'font' => [
                        'name' => 'Courier New',
                        'size' => 12,
                    ],
                    'alignment' => [
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ];

                $currentRow = 1;

                foreach ($this->acaras as $acara) {
                    // Merge untuk baris ACARA
                    $sheet->mergeCells("A$currentRow:G$currentRow");
                    $sheet->setCellValue("A$currentRow", 'Acara ' . $acara->nomor_lomba . ' | ' . $acara->nama . ' - ' . $acara->grup);
                    $sheet->getStyle("A$currentRow:G$currentRow")->applyFromArray($headerStyle);

                    $currentRow += 2;

                    // Cek apakah acara memiliki peserta
                    if ($this->hasParticipants($acara)) {
                        
                        // Proses heats dan seri
                        $serieIndex = 0;
                        foreach ($acara->heats as $key => $heat) {
                            $this->mergeSeriColumns($sheet, $currentRow, $serieIndex);

                            // Lakukan penggabungan kolom Grup
                            $this->mergeGrupColumns($sheet, $currentRow);
                            
                            if ($key == count($acara->heats) - 1) {
                                $currentRow += 9;
                            } else {
                                $currentRow += 8;
                            }
                            $serieIndex++;
                        }
                    } else {
                        // Jika tidak ada peserta, tambahkan baris kosong atau keterangan jika diperlukan
                        // Misalnya, tambahkan baris kosong
                        $currentRow += 1;
                    }

                    // Terapkan style konten
                    $sheet->getStyle("A$currentRow:G$currentRow")->applyFromArray($contentStyle);
                }

                // Sesuaikan lebar kolom
                $this->adjustColumnWidths($sheet);
            },
        ];
    }

    // Merge Grup A dan B
    private function mergeGrupColumns($sheet, $currentRow)
    {
        // Periksa apakah ada peserta di Grup A
        // $groupAExists = !empty($heat[0]);
        // $groupBExists = !empty($heat[1]);
        
        // foreach ($acara->heats as $heat) {
        //     if (!empty($heat[0])) {  // Heat pertama untuk Grup A
        //         $groupAExists = true;
        //     }
        //     if (!empty($heat[1])) {  // Heat kedua untuk Grup B
        //         $groupBExists = true;
        //     }
        // }

        // Grup A
        
            $startGroupARow = $currentRow;
            $endGroupARow = $startGroupARow + 3; // 4 baris untuk Grup A
            $sheet->mergeCells("B$startGroupARow:B$endGroupARow");
            $sheet->setCellValue("B$startGroupARow", 'A');
            $sheet->getStyle("B$startGroupARow")->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, // Vertikal di atas
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, // Horizontal di kanan
                ],
            ]);
        

        // Grup B
        
            $startGroupBRow = isset($endGroupARow) ? $endGroupARow + 1 : $currentRow;
            $endGroupBRow = $startGroupBRow + 3; // 4 baris untuk Grup B
            $sheet->mergeCells("B$startGroupBRow:B$endGroupBRow");
            $sheet->setCellValue("B$startGroupBRow", 'B');
            $sheet->getStyle("B$startGroupBRow")->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, // Vertikal di atas
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, // Horizontal di kanan
                ],
            ]);
        
    }


    // Merge untuk SERI
    private function mergeSeriColumns($sheet, $currentRow, $serieIndex)
    {
        $startSeriRow = $currentRow; // Setelah header
        $endSeriRow = $startSeriRow + 7; // 8 baris per SERI
        $sheet->mergeCells("A$startSeriRow:A$endSeriRow");
        $sheet->setCellValue("A$startSeriRow", $serieIndex + 1);
        $sheet->getStyle("A$startSeriRow")->applyFromArray([
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP, // Vertikal di atas
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT, // Horizontal di kanan
            ],
        ]);
    }

    // Custom lebar kolom
    private function adjustColumnWidths($sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(3); // SERI
        $sheet->getColumnDimension('B')->setWidth(5);  // GRUP
        $sheet->getColumnDimension('C')->setWidth(3);  // LINT
        $sheet->getColumnDimension('D')->setWidth(30); // NAMA
        $sheet->getColumnDimension('E')->setWidth(25); // ASAL SEKOLAH / KLUB
        $sheet->getColumnDimension('F')->setWidth(10); // QET
        $sheet->getColumnDimension('G')->setWidth(10); // HASIL
    }

}
