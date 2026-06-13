<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;

Route::get('/', [FormController::class, 'index'])->name('form.index');
Route::get('/get-jadwals/{prodi_id}', [FormController::class, 'getJadwals']);
Route::post('/submit', [FormController::class, 'submit'])->name('form.submit');
Route::get('/success', function() {
    return view('success');
})->name('form.success');
