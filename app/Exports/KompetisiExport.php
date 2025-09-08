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


class KompetisiExport implements FromCollection, WithMapping, WithEvents
{
    protected $acaras;
    protected $funGroupCount;
    protected $funMaxLanes;
    protected $funGroups;
    protected $kompetisi;

    public function __construct($acaras, $funGroupCount, $funMaxLanes, $funGroups, $kompetisi)
    {
        $this->acaras = $acaras;
        $this->funGroupCount = $funGroupCount;
        $this->funMaxLanes = $funMaxLanes;
        $this->funGroups = $funGroups;
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
        // Ini untuk inisialisasi row nya
        $rows = [];

        // Ini untuk baris header
        $rows[] = [];

        if($this->hasParticipants($acara)){
            // Untuk baris seri
            $rows[] = [];
    
            // Untuk baris subheader
            $rows[] = [];
        }


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
                                    $laneIndex + 1,  // LINT
                                    $groupIndex == 0 ? 'A' : 'B', // GRUP
                                    $participant['name'],  // NAMA
                                    'KU ' . $acara->grup,  // KU
                                    $participant['club'],  // ASAL SEKOLAH / KLUB
                                    $participant['track_record'] == 999 ? 'NT' : $trackRecordFormatted, // QET
                                    '(..............)', // HASIL
                                ];
                        } else {
                            // Jika tidak ada peserta
                            
                            $rows[] = [
                                $laneIndex + 1,  // LINT
                                $groupIndex == 0 ? 'A' : 'B', // GRUP
                                '', '', '', '', // Kosong
                            ];
                        }

                    }
                }

                $lastHeatIndex = array_key_last($acara->heats);

                $keys = array_keys($heat);
                $lastGroupIndex = end($keys);

                // Check group index
                if($groupIndex == $lastGroupIndex && $serieIndex != $lastHeatIndex){
                    // Menambahkan baris baru untuk seri
                    $rows[] = [];

                    // Menambahkan baris baru untuk subheader
                    $rows[] = [];
                }
            }
        }            

        // Space kosong untuk pemisah
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


                    // Merge untuk baris ACARA
                    $sheet->setCellValue("A$currentRow", 'ACARA ' . $acara->nomor_lomba);
                    $sheet->mergeCells("B$currentRow:C$currentRow");
                    $sheet->setCellValue("B$currentRow", ' (KU ' . strtoupper($acara->grup) 
                    . ') ');
                    $sheet->mergeCells("D$currentRow:E$currentRow");
                    $sheet->setCellValue("D$currentRow", $acara->nama);
                    $sheet->mergeCells("F$currentRow:G$currentRow");
                    $sheet->setCellValue("F$currentRow", $kategori);
                    $sheet->getStyle("A$currentRow:G$currentRow")->applyFromArray($headerStyle);

                    $currentRow++;

                    // Cek apakah acara memiliki peserta
                    if ($this->hasParticipants($acara)) {
                        
                        // Proses heats dan seri
                        $serieIndex = 0;
                        foreach ($acara->heats as $key => $heat) {
                            $sheet->setCellValue("A$currentRow", "Seri " . ($serieIndex + 1));
                            $sheet->getStyle("A$currentRow")->applyFromArray($seriStyle);
                            
                            $currentRow++;

                            $sheet->setCellValue("A$currentRow", "Ln.");
                            $sheet->setCellValue("B$currentRow", "Grup");
                            $sheet->setCellValue("C$currentRow", "Nama");
                            $sheet->setCellValue("D$currentRow", "KU");
                            $sheet->setCellValue("E$currentRow", "Asal Selokah/Klub");
                            $sheet->setCellValue("F$currentRow", "QET");
                            $sheet->setCellValue("G$currentRow", "Hasil");
                            $sheet->getStyle("A$currentRow:G$currentRow")->applyFromArray($subHeaderStyle);
                            
                            $currentRow++;

                            // $this->mergeSeriColumns($sheet, $currentRow, $serieIndex);

                            // Lakukan penggabungan kolom Grup
                            $this->mergeGrupColumns($sheet, $currentRow);
                            
                            if ($key == count($acara->heats) - 1) {
                                $currentRow += (($this->funGroupCount * $this->funMaxLanes) + 1);
                            } else {
                                $currentRow += ($this->funGroupCount * $this->funMaxLanes);
                            }
                            $serieIndex++;
                        }

                    } else {
                        // Jika tidak ada peserta, tambahkan baris kosong atau keterangan jika diperlukan
                        // Misalnya, tambahkan baris kosong
                        $currentRow += 1;
                    }
                }

                // Sesuaikan lebar kolom
                $this->adjustColumnWidths($sheet);
                $sheet->getDefaultRowDimension()->setRowHeight(20);
            },
        ];
    }

    // Merge Grup A, B, C, dan D
    private function mergeGrupColumns($sheet, $currentRow)
    {
        $groupNames = $this->funGroups; // Nama grup
        $groupRowCount = $this->funMaxLanes; // Jumlah baris per grup

        foreach ($groupNames as $index => $groupName) {
            $startGroupRow = $currentRow + ($index * $groupRowCount); // Baris awal grup
            $endGroupRow = $startGroupRow + $groupRowCount - 1; // Baris akhir grup

            // Merge kolom untuk grup
            $sheet->mergeCells("B$startGroupRow:B$endGroupRow");
            $sheet->setCellValue("B$startGroupRow", $groupName);

            // Set Text di kotak jadi ada di atas tengah
            $sheet->getStyle("B$startGroupRow")->applyFromArray([
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Set border grup kiri kanan
            $sheet->getStyle("B$startGroupRow:B$endGroupRow")->applyFromArray([
                'borders' => [
                    'left' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '808080'], // Warna hitam
                    ],
                    'right' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => '808080'], // Warna hitam
                    ],
                ],
            ]);

            // Set LINTASAN jadi di tengah
            $sheet->getStyle("A$startGroupRow:A$endGroupRow")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Set KU ke tengah
            $sheet->getStyle("D$startGroupRow:D$endGroupRow")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);

            // Set QET dan HASIL ke tengah
            $sheet->getStyle("F$startGroupRow:G$endGroupRow")->applyFromArray([
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ]);


            // Set border bottom setelah setiap grup
            $sheet->getStyle("A$endGroupRow:G$endGroupRow")->applyFromArray([
                'borders' => [
                    'bottom' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['argb' => 'D0D0D0'],
                    ],
                ],
            ]);
        }
    }
  


    // Merge untuk SERI
    private function mergeSeriColumns($sheet, $currentRow, $serieIndex)
    {
        $startSeriRow = $currentRow; // Baris awal seri
        $endSeriRow = $startSeriRow + (($this->funGroupCount * $this->funMaxLanes) - 1); // Total 16 baris (4 grup × 4 baris)
        $sheet->mergeCells("A$startSeriRow:A$endSeriRow");
        $sheet->setCellValue("A$startSeriRow", $serieIndex + 1);
        $sheet->getStyle("A$startSeriRow")->applyFromArray([
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ]);
    }


    // Custom lebar kolom
    private function adjustColumnWidths($sheet)
    {
        $sheet->getColumnDimension('A')->setWidth(10); // LINT
        $sheet->getColumnDimension('B')->setWidth(7);  // GRUP
        $sheet->getColumnDimension('C')->setWidth(25); // NAMA
        $sheet->getColumnDimension('D')->setWidth(10); // ASALH SEKOLAH
        $sheet->getColumnDimension('E')->setWidth(25); // ASALH SEKOLAH
        $sheet->getColumnDimension('F')->setWidth(10); // QET
        $sheet->getColumnDimension('G')->setWidth(10); // HASIL
    }

}
