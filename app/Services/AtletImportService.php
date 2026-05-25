<?php

namespace App\Services;

use App\Models\Acara;
use App\Models\Atlet;
use App\Models\Pembayaran;
use App\Models\Peserta;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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

        $required = ['Info Klub', 'Referensi', 'Input Atlet'];
        $missing  = array_filter($required, fn($name) => $spreadsheet->getSheetByName($name) === null);
        if (!empty($missing)) {
            $found = implode(', ', array_map(fn($s) => '"' . $s->getTitle() . '"', $spreadsheet->getAllSheets()));
            throw new \RuntimeException(
                'File tidak sesuai format. Sheet yang dibutuhkan: ' . implode(', ', $required) . '. '
                . 'Sheet yang ditemukan: ' . $found . '.'
            );
        }

        // Single transaction covers User creation + all registrations — no orphan records on failure.
        return DB::transaction(function () use ($spreadsheet, $kompetisiId) {
            $userInfo = $this->parseInfoKlub($spreadsheet->getSheetByName('Info Klub'));
            $acaraMap = $this->parseReferensi($spreadsheet->getSheetByName('Referensi'), $kompetisiId);
            $stats    = $this->parseInputAtlet($spreadsheet->getSheetByName('Input Atlet'), $userInfo['user'], $acaraMap);

            return array_merge($userInfo, $stats, ['errors' => $this->errors]);
        });
    }

    private function parseInfoKlub(Worksheet $sheet): array
    {
        $rows    = $sheet->toArray(null, true, false, false);
        $dataRow = null;
        for ($i = 1; $i < count($rows); $i++) {
            if (!empty(trim((string) ($rows[$i][0] ?? '')))) {
                $dataRow = $rows[$i];
                break;
            }
        }
        if (!$dataRow) {
            throw new \RuntimeException('Sheet "Info Klub" tidak memiliki data klub.');
        }

        $clubName = trim((string) ($dataRow[0] ?? ''));
        $picName  = trim((string) ($dataRow[1] ?? ''));
        $phone    = trim((string) ($dataRow[2] ?? ''));
        $email    = strtolower(trim((string) ($dataRow[3] ?? '')));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \RuntimeException('Sheet "Info Klub" memiliki email tidak valid: "' . $email . '".');
        }

        $plainPassword = null;
        $userByEmail = User::whereRaw('LOWER(email) = ?', [$email])->first();
        $userByClub  = User::whereRaw('LOWER(club) = ?', [strtolower($clubName)])->first();

        if ($userByEmail && strtolower($userByEmail->club) !== strtolower($clubName)) {
            throw new \RuntimeException(
                'Email "' . $email . '" sudah terdaftar untuk klub "' . $userByEmail->club . '", bukan "' . $clubName . '".'
            );
        }

        if ($userByClub && strtolower($userByClub->email) !== $email) {
            throw new \RuntimeException(
                'Klub "' . $clubName . '" sudah terdaftar dengan email "' . $userByClub->email . '", bukan "' . $email . '".'
            );
        }

        $user = $userByEmail ?? $userByClub;
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
        // Header: id, jenis_lomba, nomor_lomba, nama, kategori, grup, min_umur, [max_umur,] label
        // 'label' column is always the last column in the header row
        $rows = $sheet->toArray(null, true, false, false);
        if (empty($rows)) {
            $this->errors[] = 'Sheet "Referensi" kosong.';
            return [];
        }

        $headers  = array_map(fn($h) => strtolower(trim((string) $h)), $rows[0]);
        $labelIdx = array_search('label', $headers);
        if ($labelIdx === false) {
            $this->errors[] = 'Sheet "Referensi" tidak memiliki kolom "label".';
            return [];
        }

        // Collect all candidate acara IDs from the sheet first
        $candidateIds = [];
        for ($i = 1; $i < count($rows); $i++) {
            $rawId = $rows[$i][0] ?? null;
            if (!empty($rawId)) {
                $candidateIds[] = (int) $rawId;
            }
        }

        // Single query: which of those IDs actually belong to this kompetisi?
        $validAcaraIds = Acara::where('kompetisi_id', $kompetisiId)
            ->whereIn('id', array_unique($candidateIds))
            ->pluck('id')
            ->flip()
            ->all();

        $map = [];
        for ($i = 1; $i < count($rows); $i++) {
            $row   = $rows[$i];
            $rawId = $row[0] ?? null;
            if (empty($rawId)) continue;

            $acaraId = (int) $rawId;
            $label   = trim((string) ($row[$labelIdx] ?? ''));
            if (empty($label)) continue;

            if (!isset($validAcaraIds[$acaraId])) {
                $this->errors[] = "Referensi baris " . ($i + 1) . ": acara ID {$acaraId} tidak ada di kompetisi {$kompetisiId}.";
                continue;
            }

            if (isset($map[$label])) {
                $this->errors[] = "Referensi baris " . ($i + 1) . ": label '{$label}' duplikat, menimpa entri sebelumnya.";
            }
            $map[$label] = $acaraId;
        }

        return $map;
    }

    private function parseInputAtlet(Worksheet $sheet, User $user, array $acaraMap): array
    {
        // Columns (0-indexed): No(0), Nama Lengkap(1), Tanggal Lahir(2), Tahun Lahir(3),
        //   Jenis Kelamin(4), Nomor Lomba 1-7 (5-11), Nama Dokumen(12), Catatan(13)
        $rows = $sheet->toArray(null, true, false, false);

        // Eagerly load all acara referenced by the map — one query instead of one per registration
        $acaraCache = Acara::whereIn('id', array_values($acaraMap))
            ->get()
            ->keyBy('id');

        $athletesNew        = 0;
        $athletesReused     = 0;
        $registrations      = 0;
        $totalHarga         = 0;
        $pesertaIds         = [];
        $registeredAthletes = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row  = $rows[$i];
            $name = trim((string) ($row[1] ?? ''));
            if (empty($name)) continue;

            $rawDate = $row[2] ?? null;
            // Guard: null, empty string, or numeric zero all mean "missing date"
            if ($rawDate === null || $rawDate === '' || (is_numeric($rawDate) && (float) $rawDate <= 0)) {
                $this->errors[] = "Baris " . ($i + 1) . ": tanggal lahir kosong, dilewati.";
                continue;
            }
            if (is_numeric($rawDate) && $rawDate > 0) {
                $birthDate = Carbon::instance(XlsxDate::excelToDateTimeObject((float) $rawDate));
            } else {
                try {
                    $birthDate = Carbon::parse($rawDate);
                } catch (\Exception) {
                    $this->errors[] = "Baris " . ($i + 1) . ": tanggal lahir '{$rawDate}' tidak valid, dilewati.";
                    continue;
                }
            }

            $jenisKelamin = trim((string) ($row[4] ?? ''));
            if (!in_array($jenisKelamin, ['Pria', 'Wanita'])) {
                $this->errors[] = "Baris " . ($i + 1) . ": jenis kelamin '{$jenisKelamin}' tidak valid, dilewati.";
                continue;
            }

            // Find or create Atlet by (user_id, name)
            $atlet = Atlet::where('user_id', $user->id)
                ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                ->first();

            if (!$atlet) {
                $atlet = Atlet::create([
                    'name'          => $name,
                    'umur'          => $birthDate->format('Y-m-d'),
                    'jenis_kelamin' => $jenisKelamin,
                    'user_id'       => $user->id,
                    'is_verified'   => 'verified',
                ]);
                $athletesNew++;
            } else {
                $athletesReused++;
            }

            // Register Nomor Lomba 1–7 (columns 5–11)
            $athleteNewLabels = [];
            for ($col = 5; $col <= 11; $col++) {
                $label = trim((string) ($row[$col] ?? ''));
                if (empty($label)) continue;

                $acaraId = $acaraMap[$label] ?? null;
                if ($acaraId === null) {
                    $this->errors[] = "Baris " . ($i + 1) . " ({$name}): label '{$label}' tidak ditemukan di referensi.";
                    continue;
                }

                if (Peserta::where('atlet_id', $atlet->id)->where('acara_id', $acaraId)->exists()) {
                    continue; // already registered
                }

                $acara   = $acaraCache->get($acaraId);
                $peserta = Peserta::create([
                    'acara_id'          => $acaraId,
                    'atlet_id'          => $atlet->id,
                    'peserta_user_id'   => $user->id,
                    'status_pembayaran' => 'Selesai',
                    'waktu_pembayaran'  => now()->toDateString(),
                ]);
                $pesertaIds[]       = $peserta->id;
                $totalHarga        += $acara->harga ?? 0;
                $registrations++;
                $athleteNewLabels[] = $label;
            }

            if (!empty($athleteNewLabels)) {
                $registeredAthletes[] = ['name' => $name, 'events' => $athleteNewLabels];
            }
        }

        if (!empty($pesertaIds)) {
            $pembayaran = Pembayaran::create([
                'user_id'           => $user->id,
                'midtrans_order_id' => 'IMPORT-' . uniqid('', true) . '-' . $user->id,
                'metode_pembayaran' => 'IMPORT',
                'total_harga'       => $totalHarga,
                'status'            => 'Berhasil',
            ]);
            Peserta::whereIn('id', $pesertaIds)->update(['pembayaran_id' => $pembayaran->id]);
        }

        return [
            'athletes_new'        => $athletesNew,
            'athletes_reused'     => $athletesReused,
            'registrations'       => $registrations,
            'pembayaran_total'    => $totalHarga,
            'registered_athletes' => $registeredAthletes,
        ];
    }
}
