<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ModelAPIController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataEdukasiController;
use App\Http\Controllers\DataWilayahController;
use App\Http\Controllers\DataPenggunaController;
use App\Http\Controllers\DataPernikahanController;
use App\Http\Controllers\DataKlasifikasiController;
use App\Http\Controllers\HasilKlasifikasiController;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', [LoginController::class, 'index'])->name('login.index');
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login/authenticate', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| DASHBOARD (admin, kepala kua, penyuluh)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,kepala kua,penyuluh'])->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

/*
|--------------------------------------------------------------------------
| DATA PERNIKAHAN (admin, kepala kua)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,kepala kua'])->prefix('data_pernikahan')->name('data_pernikahan.')->group(function () {
    Route::get('/', [DataPernikahanController::class, 'index'])->name('index');
    Route::get('/tambahData', [DataPernikahanController::class, 'tambahData'])->name('tambahData');
    Route::post('/tambahDataPost', [DataPernikahanController::class, 'tambahDataPost'])->name('tambahDataPost');
    Route::get('/{id}/editData', [DataPernikahanController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [DataPernikahanController::class, 'update'])->name('update');
    Route::delete('/{id}/deleteData', [DataPernikahanController::class, 'delete'])->name('delete');
    Route::get('/detailPasangan/{id}', [DataPernikahanController::class, 'detail'])->name('detail');
    Route::post('/import', [DataPernikahanController::class, 'import'])->name('upload');
    Route::get('/exportExcel', [DataPernikahanController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/exportCsv', [DataPernikahanController::class, 'exportCsv'])->name('exportCsv');
    Route::get('/exportPdf', [DataPernikahanController::class, 'exportPdf'])->name('exportPdf');
});

/*
|--------------------------------------------------------------------------
| DATA PENGGUNA (admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('data_pengguna')->name('data_pengguna.')->group(function () {
    Route::get('/', [DataPenggunaController::class, 'index'])->name('index');
    Route::get('/tambahData', [DataPenggunaController::class, 'tambahData'])->name('tambahData');
    Route::post('/tambahDataPost', [DataPenggunaController::class, 'tambahDataPost'])->name('tambahDataPost');
    Route::get('/{id}/editData', [DataPenggunaController::class, 'edit'])->name('edit');
    Route::post('/update/{id}', [DataPenggunaController::class, 'editDataPost'])->name('editDataPost');
    Route::delete('/{id}/deleteData', [DataPenggunaController::class, 'delete'])->name('delete');
    Route::get('/{id}/detailData', [DataPenggunaController::class, 'detail'])->name('detail');
});

/*
|--------------------------------------------------------------------------
| DATA WILAYAH (admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('data_wilayah')->name('data_wilayah.')->group(function () {
    Route::get('/', [DataWilayahController::class, 'index'])->name('index');
    Route::get('/tambahData', [DataWilayahController::class, 'tambahData'])->name('tambahData');
    Route::post('/tambahDataPost', [DataWilayahController::class, 'tambahDataPost'])->name('tambahDataPost');
    Route::get('/{id}/editData', [DataWilayahController::class, 'editData'])->name('editData');
    Route::post('/update/{id}', [DataWilayahController::class, 'updateData'])->name('updateData');
    Route::delete('/{id}/deleteData', [DataWilayahController::class, 'deleteData'])->name('deleteData');
    Route::get('/{id}/detailData', [DataWilayahController::class, 'detailData'])->name('detailData');
});

/*
|--------------------------------------------------------------------------
| DATA EDUKASI (admin, kepala kua, penyuluh)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,kepala kua,penyuluh'])->prefix('data_edukasi')->name('data_edukasi.')->group(function () {
    Route::get('/', [DataEdukasiController::class, 'index'])->name('index');
    Route::get('/{id}/detailData', [DataEdukasiController::class, 'detailData'])->name('detailData');
    Route::get('/{id}/editData', [DataEdukasiController::class, 'editData'])->name('editData');
    Route::put('/update/{id}', [DataEdukasiController::class, 'updateData'])->name('updateData');
    Route::delete('/{id}/deleteData', [DataEdukasiController::class, 'deleteData'])->name('deleteData');
    Route::get('/tambahData', [DataEdukasiController::class, 'tambahData'])->name('tambahData');
    Route::post('/tambahDataPost', [DataEdukasiController::class, 'tambahDataPost'])->name('store');
});


/*
|--------------------------------------------------------------------------
| DATA EDUKASI (penyuluh)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:penyuluh'])->prefix('data_edukasi')->name('data_edukasi.')->group(function () {

});

/*
|--------------------------------------------------------------------------
| DATA KLASIFIKASI (admin only)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('data_klasifikasi')->name('data_klasifikasi.')->group(function () {
    Route::get('/', [DataKlasifikasiController::class, 'index'])->name('index');
    Route::get('/{id}/detailKlasifikasi', [DataKlasifikasiController::class, 'detail'])->name('detailKlasifikasi');
    Route::get('/exportExcel', [DataKlasifikasiController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/exportCsv', [DataKlasifikasiController::class, 'exportCsv'])->name('exportCsv');
    Route::get('/exportPdf', [DataKlasifikasiController::class, 'exportPdf'])->name('exportPdf');
});

/*
|--------------------------------------------------------------------------
| HASIL KLASIFIKASI (semua role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin,kepala kua,penyuluh'])->group(function () {
    Route::get('/hasil_klasifikasi', [HasilKlasifikasiController::class, 'index'])->name('hasil_klasifikasi.index');
    Route::get('/peta_sebaran', [HasilKlasifikasiController::class, 'map'])->name('peta_sebaran');
    Route::get('/hasil_klasifikasi/chart', [HasilKlasifikasiController::class, 'chart'])->name('hasil_klasifikasi.chart');
    Route::get('/hasil_klasifikasi/graphView', [HasilKlasifikasiController::class, 'graphView'])->name('hasil_klasifikasi.graphView');
    Route::get('/exportExcel', [HasilKlasifikasiController::class, 'exportExcel'])->name('hasil_klasifikasi.exportExcel');
    Route::get('/exportPdf', [HasilKlasifikasiController::class, 'exportPdf'])->name('hasil_klasifikasi.exportPdf');
    Route::get('/exportCsv', [HasilKlasifikasiController::class, 'exportCsv'])->name('hasil_klasifikasi.exportCsv');
});


/*
|--------------------------------------------------------------------------
| Laporan
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin, kepala kua'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/statistik', [LaporanController::class, 'statistik'])->name('statistik');
    Route::get('/klasifikasi', [LaporanController::class, 'klasifikasi'])->name('klasifikasi');
    Route::get('/resiko_wilayah', [LaporanController::class, 'resiko_wilayah'])->name('wilayah');
});

/*
|--------------------------------------------------------------------------
| MODEL API
|--------------------------------------------------------------------------
*/
Route::post('/predict', [ModelAPIController::class, 'predict'])->name('predict');





