<?php

use App\Http\Controllers\DocumentImportController;
use Illuminate\Support\Facades\Route;


Route::get('/import', [DocumentImportController::class, 'showImportForm'])->name('import.form');
Route::post('/import', [DocumentImportController::class, 'import'])->name('import.process');
Route::post('/process-queue', [DocumentImportController::class, 'processQueue'])->name('process.queue');
