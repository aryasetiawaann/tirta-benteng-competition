<?php

use App\Http\Controllers\MainPageController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
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
    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    Route::get('/dashboard/atlet', function () {
        return view('pages.dashboard-atlet');
    })->name('dashboard.atlet');

    Route::get('/dashboard/daftar', function () {
        return view('pages.dashboard-daftar');
    })->name('dashboard.daftar');

    Route::get('/dashboard/kompetisi', function () {
        return view('pages.dashboard-kompetisi');
    })->name('dashboard.kompetisi');

    #uid = unique id
    Route::get('/dashboard/kompetisi/uid', function () {
        return view('pages.kompetisi-daftar');
    })->name('kompetisi.daftar');

    Route::get('/dashboard/kompetisi/uid/uid', function () {
        return view('pages.kompetisi-daftar2');
    })->name('kompetisi.daftar2');
    
    Route::get('/dashboard/tagihan', function () {
        return view('pages.dashboard-tagihan');
    })->name('dashboard.tagihan');

    Route::get('/dashboard/lunas', function () {
        return view('pages.dashboard-lunas');
    })->name('dashboard.lunas');

    Route::get('/dashboard/bukuacara', function () {
        return view('pages.dashboard-bukuacara');
    })->name('dashboard.bukuacara');

    Route::get('/dashboard/bukuhasil', function () {
        return view('pages.dashboard-bukuhasil');
    })->name('dashboard.bukuhasil');

});

Route::middleware(['auth','role:admin'])->group(function () {
    Route::get('/admin/dashboard',[AdminController::class,'dashboard'])->name('admin.dashboard');
});


Route::middleware('auth')->group(function () {  
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
