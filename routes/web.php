<?php

use App\Http\Controllers\AcaraController;
use App\Http\Controllers\AtletController;
use App\Http\Controllers\KompetisiController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnduhanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [MainPageController::class, 'mainpage'])->name('main');

Route::middleware(['auth', 'verified'])->group(function () {
    
    Route::get('/dashboard', [MainPageController::class, 'userDashboard'])->name('dashboard');


    Route::resource('/dashboard/atlet-saya', AtletController::class)->names('dashboard.atlet');
    

    
    Route::get('/dashboard/daftar-kompetisi', [KompetisiController::class, 'index'])->name('dashboard.kompetisi');
    Route::get('/dashboard/daftar-kompetisi/{id}', [AcaraController::class,'index'])->name('dashboard.acara');
    Route::get('/dashboard/daftar-kompetisi/acara/{id}', [AcaraController::class,'showPesertaUser'])->name('dashboard.acara.detail');
    Route::post('/dashboard/daftar-kompetisi/acara/daftar/', [PesertaController::class,'create'])->name('dashboard.acara.daftar');
    Route::get('/dashboard/kompetisi-saya', [KompetisiController::class,'kompetisiSaya'])->name('dashboard.kompe-saya');
    Route::get('/dashboard/kompetisi-saya/{id}', [AcaraController::class,'kompetisiSaya'])->name('dashboard.kompe-saya.acara');
    Route::get('/dashboard/kompetisi-saya/acara/{id}', [AcaraController::class,'kompetisiSayaDetail'])->name('dashboard.kompe-saya.acara.detail');
    

    Route::get('/dashboard/tagihan', [PesertaController::class,'tagihan'])->name('dashboard.tagihan');
    Route::get('/dashboard/tagihan/bayar-semua', [PesertaController::class,'tagihanBayarSemua'])->name('dashboard.tagihan.bayar-semua');
    Route::get('/dashboard/tagihan/{id}', [PesertaController::class,'pembayaranSukses'])->name('dashboard.tagihan.sukses');
    Route::get('/dashboard/riwayat-pembayaran', [PesertaController::class, 'tagihanRiwayat'])->name('dashboard.tagihan.riwayat');
    Route::delete('/dashboard/tagihan/delete/{id}', [PesertaController::class,'destroy'])->name('dashboard.tagihan.destroy');

    Route::get('/dashboard/bukuacara', [UnduhanController::class, 'userBukuAcara'])->name('dashboard.bukuacara');
    Route::get('/dashboard/bukuacara/view/{id}/pdf', [UnduhanController::class, 'showBukuAcara'])->name('dashboard.bukuacara.view');

    Route::get('/dashboard/bukuacara/test', function() {
        return view('layouts.print-layout-bukuacara');
    });

    Route::get('/dashboard/bukuhasil', function () {
        return view('pages.dashboard-bukuhasil');
    })->name('dashboard.bukuhasil');

    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/dashboard/profile/', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard/profile/delete-foto', [ProfileController::class, 'deletePhoto']);
    Route::delete('/dashboard/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
});


require __DIR__.'/auth.php';
