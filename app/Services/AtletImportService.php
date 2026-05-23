<?php

namespace App\Services;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Pembayaran;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsxDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AtletImportService
{
    private array $errors = [];

    public function import(string $filePath, int $kompetisiId): array
    {
        $spreadsheet = IOFactory::load($filePath);

        $userInfo  = $this->parseInfoKlub($spreadsheet->getSheetByName('Info Klub'));
        $acaraMap  = $this->parseReferensi($spreadsheet->getSheetByName('Referensi'), $kompetisiId);
        $stats     = $this->parseInputAtlet($spreadsheet->getSheetByName('Input Atlet'), $userInfo['user'], $acaraMap);

        return array_merge($userInfo, $stats, ['errors' => $this->errors]);
    }

    private function parseInfoKlub(Worksheet $sheet): array
    {
        $rows = $sheet->toArray(null, true, false, false);
        $dataRow = null;
        for ($i = 1; $i < count($rows); $i++) {
            if (!empty(trim((string)($rows[$i][0] ?? '')))) {
                $dataRow = $rows[$i];
                break;
            }
        }
        if (!$dataRow) {
            throw new \RuntimeException('Sheet "Info Klub" tidak memiliki data klub.');
        }

        $clubName = trim((string)($dataRow[0] ?? ''));
        $picName  = trim((string)($dataRow[1] ?? ''));
        $phone    = trim((string)($dataRow[2] ?? ''));
        $email    = strtolower(trim((string)($dataRow[3] ?? '')));

        $plainPassword = null;
        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            $plainPassword = Str::random(12);
            $user = User::create([
                'name'     => $picName,
                'email'    => $email,
                'club'     => $clubName,
                'phone'    => $phone,
                'password' => $plainPassword,
            ]);
        }

        return [
            'user'          => $user,
            'user_created'  => $plainPassword !== null,
            'user_email'    => $email,
            'user_password' => $plainPassword,
        ];
    }

    private function parseReferensi(Worksheet $sheet, int $kompetisiId): array
    {
        return [];  // stub — filled in Task 2
    }

    private function parseInputAtlet(Worksheet $sheet, User $user, array $acaraMap): array
    {
        return [    // stub — filled in Task 2
            'athletes_new'     => 0,
            'athletes_reused'  => 0,
            'registrations'    => 0,
            'pembayaran_total' => 0,
        ];
    }
}
