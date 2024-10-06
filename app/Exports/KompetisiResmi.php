<?php

namespace App\Exports;

use App\Models\Acara;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;  

class KompetisiResmi implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
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

        $rows[] = [
            'ACARA ' . $acara->nomor_lomba,
            $acara->nama,
            '', '', '', '', ''
        ];

        $rows[] = [
            'SERI', 'LINT', 'NAMA', 'ASAL SEKOLAH / KLUB', 'QET', 'HASIL'
        ];

        foreach ($acara->heats as $serieIndex => $heat)
        {
            foreach($heat as $laneIndex => $participant)
            {
                if($participant)
                {
                    $trackRecordFormatted = sprintf('%02d:%02d.%02d', 
                        floor($participant['track_record'] / 60),  // Menit
                        floor(fmod($participant['track_record'], 60)),  // Detik
                        ceil(($participant['track_record'] - floor($participant['track_record'])) * 100) // Milisekon
                        );

                    $rows[] = [
                        $serieIndex + 1, // SERI
                        $laneIndex + 1,  // LINT
                        $participant['name'],  // NAMA
                        $participant['club'],  // ASAL SEKOLAH / KLUB
                        $participant['track_record'] == 999 ? 'NT' : $trackRecordFormatted, // QET
                        '', // HASIL
                    ];
                }else {
                    $rows[] = [
                        $serieIndex + 1, // SERI
                        $laneIndex + 1,  // LINT
                        '', '', '', '', // Kosong
                    ];
                }
            }
        }

        $rows[] = [];
        
        return $rows;
    }

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

                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Courier New');
                $sheet->getParent()->getDefaultStyle()->getFont()->setSize(12);
                // Style untuk header biru
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
                            'borderStyle' => Border::BORDER_THIN, // Border tipis
                        ],
                    ],
                ];

                // Style untuk konten
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
                            'borderStyle' => Border::BORDER_THIN, // Border tipis
                        ],
                    ],
                ];

                $currentRow = 1;

                foreach ($this->acaras as $acara) {

                    // Merge untuk nama acara
                    $sheet->mergeCells("A$currentRow:F$currentRow");
                    $sheet->setCellValue("A$currentRow", 'Acara ' . $acara->nomor_lomba . ' | '. $acara->nama . ' - ' . $acara->grup);
                    $sheet->getStyle("A$currentRow:F$currentRow")->applyFromArray($headerStyle);

                    $currentRow+=2;

                    if ($this->hasParticipants($acara))
                    {
                        $serieIndex = 0;
                        foreach ($acara->heats as $key => $heat) 
                        {
                            $this->mergeSeriColumns($sheet, $currentRow, $serieIndex);
                            
                            if($key == count($acara->heats) - 1)
                            {
                                $currentRow += 9;
                            }else{
                                $currentRow += 8;
                            }
                            $serieIndex++;
                        }
                    }
                    else
                    {
                        $currentRow += 1;
                    }
                    // Proses heats dan seri

                    // Terapkan style konten
                    $sheet->getStyle("A$currentRow:F$currentRow")->applyFromArray($contentStyle);
                }

                // Custom width
                $this->adjustColumnWidths($sheet);
            },
        ];
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
        $sheet->getColumnDimension('B')->setWidth(3);  // LINT
        $sheet->getColumnDimension('C')->setWidth(30); // NAMA
        $sheet->getColumnDimension('D')->setWidth(25); // ASAL SEKOLAH / KLUB
        $sheet->getColumnDimension('E')->setWidth(15); // QET
        $sheet->getColumnDimension('F')->setWidth(20); // HASIL
    }

    
}
