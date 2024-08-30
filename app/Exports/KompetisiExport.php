<?php

namespace App\Exports;

use App\Models\Kompetisi;
use App\Models\Acara;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class KompetisiExport implements FromCollection, WithEvents, WithHeadings, WithTitle
{
    protected $kompetisiId;

    public function __construct($kompetisiId)
    {
        $this->kompetisiId = $kompetisiId;
    }

    public function collection()
    {
        $acaras = Acara::where('kompetisi_id', $this->kompetisiId)->get();
        $exportData = [];

        // Loop through each acara (event)
        foreach ($acaras as $acara) {
            // Add acara header
            $exportData[] = [
                'ACARA' => strtoupper($acara->nomor_lomba . ' - ' . $acara->nama . ' ' . $acara->grup), // Header for the acara
                'RANK' =>'',
                'NAMA' => '',
                'ASAL SEKOLAH / KLUB' => '',
                'QET' => '',
                'HASIL' => ''
            ];

            // Get participants for this acara
            $participants = $acara->pesertaSelesai;
            $rank = 1;
            // Set club for each participant and add participant's data to export
            foreach ($participants as $participant) {
                $exportData[] = [
                    'ACARA' => '', // No need to repeat acara name in participant rows
                    'RANK' => $rank++,
                    'NAMA' => $participant->name,
                    'ASAL SEKOLAH / KLUB' => $participant->user->club ?? '-',
                    'QET' => '', // Assuming you have QET (Qualifying Entry Time)
                    'HASIL' => '' // Assuming you have the result
                ];
            }

            // Add an empty row after each acara for better spacing in the Excel sheet
            $exportData[] = ['', '', '', '', ''];
        }

        // Convert the array into a Laravel Collection
        return collect($exportData);
    }

    public function headings(): array
    {
        return [
            ['ACARA', 'RANK', 'NAMA', 'ASAL SEKOLAH / KLUB', 'QET', 'HASIL'],
        ];
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function(AfterSheet $event) {
            $sheet = $event->sheet->getDelegate();

            // Set the width for specific columns
            $sheet->getColumnDimension('A')->setWidth(15); // Adjust column A
            $sheet->getColumnDimension('B')->setWidth(10); // Adjust column B
            $sheet->getColumnDimension('C')->setWidth(25); // Adjust column C
            $sheet->getColumnDimension('D')->setWidth(30); // Adjust column D
            $sheet->getColumnDimension('E')->setWidth(15); // Adjust column E
            $sheet->getColumnDimension('F')->setWidth(15); // Adjust column F
        }
    ];
}

    public function title(): string
    {
        return 'Kompetisi';
    }
}

