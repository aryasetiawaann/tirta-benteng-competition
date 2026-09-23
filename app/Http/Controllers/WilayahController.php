<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class WilayahController extends Controller
{
    public function provinces()
    {
        return Cache::remember('wilayah_provinces', now()->addDays(30), function () {
            return Http::get('https://wilayah.id/api/provinces.json')->json();
        });
    }

    public function regencies($id)
    {
        return Cache::remember("wilayah_regencies_{$id}", now()->addDays(30), function () use ($id) {
            return Http::get("https://wilayah.id/api/regencies/{$id}.json")->json();
        });
    }

    public function districts($id)
    {
        return Cache::remember("wilayah_districts_{$id}", now()->addDays(30), function () use ($id) {
            return Http::get("https://wilayah.id/api/districts/{$id}.json")->json();
        });
    }
}
