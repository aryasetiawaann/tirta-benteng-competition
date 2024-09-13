<?php

use App\Http\Controllers\AcaraController;
use App\Http\Controllers\AtletController;
use App\Http\Controllers\KompetisiController;
use App\Http\Controllers\LogoKompetisiController;
use App\Http\Controllers\HargaKompetisiController;
use App\Http\Controllers\MainPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UnduhanController;
use App\Http\Controllers\TrackRecordController;
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
    Route::get('/dashboard/atlet-saya/{id}/dokumen/download', [AtletController::class, 'downloadDocument'])->name('dashboard.atlet.dokumen.download');
    Route::delete('/dashboard/edit/atlet-saya/{id}/dokumen/delete', [AtletController::class, 'deleteDocument'])->name('dashboard.atlet.dokumen.delete');
    
    Route::get('/dashboard/atlet-saya/{id}/track-record', [TrackRecordController::class, 'index'])->name('dashboard.track-record.index');
    Route::get('/dashboard/atlet-saya/track-record/{id}/edit', [TrackRecordController::class, 'edit'])->name('dashboard.track-record.edit');
    Route::post('/dashboard/atlet-saya/track-record/store', [TrackRecordController::class, 'create'])->name('dashboard.track-record.create');
    Route::put('/dashboard/atlet-saya/track-record/{id}/update', [TrackRecordController::class, 'update'])->name('dashboard.track-record.update');
    Route::delete('/dashboard/atlet-saya/track-record/{id}/delete', [TrackRecordController::class, 'destroy'])->name('dashboard.track-record.destroy');
    

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

    Route::get('/dashboard/bukuhasil', [UnduhanController::class, 'showBukuHasil'])->name('dashboard.bukuhasil');
    Route::get('/admin/bukuhasil/{id}/download', [UnduhanController::class, 'downloadBukuHasil'])->name('dashboard.bukuhasil.download');


    Route::get('/dashboard/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/dashboard/profile/', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/dashboard/profile/delete-foto', [ProfileController::class, 'deletePhoto']);
    Route::delete('/dashboard/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard/profile', [ProfileController::class, 'adminEdit'])->name('profile.admin.edit');
    Route::post('/admin/dashboard/profile/', [ProfileController::class, 'adminUpdate'])->name('profile.admin.update');
    Route::get('/admin/dashboard/profile/delete-foto', [ProfileController::class, 'adminDeletePhoto']);
    Route::delete('/admin/dashboard/profile/delete', [ProfileController::class, 'adminDestroy'])->name('profile.admin.destroy');
    
    Route::get('/admin/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');

    Route::get('/admin/dashboard/tambah-kompetisi', [KompetisiController::class, 'adminIndex'])->name('dashboard.admin.kompetisi');
    Route::post('/admin/dashboard/tambah-kompetisi', [KompetisiController::class, 'tambahKompetisi'])->name('dashboard.admin.tambahkompetisi');
    Route::post('/admin/dashboard/kompetisi/logo', [LogoKompetisiController::class, 'create'])->name('dashboard.admin.kompetisi.logo.create');
    Route::delete('/admin/dashboard/kompetisi/{id}/logo/delete', [LogoKompetisiController::class, 'destroy'])->name('dashboard.admin.kompetisi.logo.delete');
    Route::post('/admin/dashboard/kompetisi/detail-harga', [HargaKompetisiController::class, 'create'])->name('dashboard.admin.kompetisi.detail-harga.create');
    Route::delete('/admin/dashboard/kompetisi/{id}/detail-harga/delete', [HargaKompetisiController::class, 'destroy'])->name('dashboard.admin.kompetisi.detail-harga.delete');
    Route::put('/admin/dashboard/edit-kompetisi', [KompetisiController::class, 'update'])->name('dashboard.admin.updatekompetisi');
    Route::delete('/admin/dashboard/kompetisi/{id}/delete', [KompetisiController::class, 'destroy'])->name('dashboard.admin.kompetisi.destroy');
    Route::get('/admin/dashboard/{id}/edit-kompetisi', [KompetisiController::class, 'editKompetisi'])->name('dashboard.admin.editkompetisi');


    Route::get('/admin/dashboard/tambah-acara', [KompetisiController::class, 'showKompetisiAdmin'])->name('dashboard.admin.acara');
    Route::post('/admin/dashboard/tambah-acara',  [AcaraController::class, 'create'])->name('dashboard.admin.tambahacara');
    Route::put('/admin/dashboard/edit-acara',  [AcaraController::class, 'update'])->name('dashboard.admin.updateacara');
    Route::get('/admin/dashboard/{id}/tambah-acara', [AcaraController::class, 'indexAdmin'])->name('dashboard.admin.listacara');
    Route::delete('/admin/dashboard/acara/{id}/delete', [AcaraController::class, 'destroy'])->name('dashboard.admin.acara.destroy');
    Route::get('/admin/dashboard/{id}/edit-acara', [AcaraController::class, 'editAcara'])->name('dashboard.admin.editacara');

    Route::post('/admin/dashboard/upload-hasil', [KompetisiController::class, 'uploadHasilKompetisi'])->name('dashboard.admin.file.add');
    Route::get('/admin/dashboard/file/{id}/edit', [KompetisiController::class, 'editHasilKompetisi'])->name('dashboard.admin.file.edit');
    Route::get('/admin/dashboard/file/{id}/file/download', [KompetisiController::class, 'downloadHasilKompetisi'])->name('dashboard.admin.file.download');
    Route::put('/admin/dashboard/file/update', [KompetisiController::class, 'updateHasilKompetisi'])->name('dashboard.admin.file.update');
    Route::delete('/admin/dashboard/file/{id}/delete', [KompetisiController::class, 'deleteHasilKompetisi'])->name('dashboard.admin.file.delete');

    Route::get('/admin/dashboard/file/{id}/download', [UnduhanController::class, 'downloadExcel'])->name('dashboard.admin.excel.download');
    
    Route::get('/admin/dashboard/dokumen-peserta/{id}/download', [KompetisiController::class, 'downloadDokumen'])->name('dashboard.admin.dokumen.download');

});


require __DIR__.'/auth.php';
