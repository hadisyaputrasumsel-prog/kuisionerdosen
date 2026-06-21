<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AdminController;

Route::get('/', [FormController::class, 'index'])->name('form.index');
Route::get('/get-jadwals/{prodi_id}', [FormController::class, 'getJadwals']);
Route::post('/submit', [FormController::class, 'submit'])->name('form.submit');
Route::get('/success', function() {
    return view('success');
})->name('form.success');

Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/sinkron', [AdminController::class, 'sinkron'])->name('admin.sinkron');
    Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('admin.jadwal');
    Route::get('/jadwal/{id}/saran', [AdminController::class, 'saran'])->name('admin.jadwal.saran');
    Route::delete('/jadwal/{id}', [AdminController::class, 'destroyJadwal'])->name('admin.jadwal.destroy');
    Route::post('/scrape/periods', [AdminController::class, 'getPeriods'])->name('admin.scrape.periods');
    Route::post('/scrape', [AdminController::class, 'scrapeData'])->name('admin.scrape');
    Route::post('/periode', [AdminController::class, 'storePeriode'])->name('admin.periode.store');
    Route::post('/periode/{id}/toggle', [AdminController::class, 'togglePeriode'])->name('admin.periode.toggle');
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('admin.laporan');
    Route::get('/laporan/cetak-tabel', [AdminController::class, 'cetakTabel'])->name('admin.laporan.cetak-tabel');
    Route::post('/laporan/config', [AdminController::class, 'saveLaporanConfig'])->name('admin.laporan.config');
    Route::get('/laporan/preview', [AdminController::class, 'previewLaporan'])->name('admin.laporan.preview');
    
    // Questions Management
    Route::resource('questions', App\Http\Controllers\QuestionController::class)->names('admin.questions');
});
