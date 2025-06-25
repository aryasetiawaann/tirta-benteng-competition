<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class DaftarPesertaController extends Controller
{
    public function show($kompetisiId)
    {
        $path = "list_daftar_{$kompetisiId}.pdf";

        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'PDF not found.');
        }

        return response()->file(storage_path("app/public/{$path}"), [
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
    }
}
