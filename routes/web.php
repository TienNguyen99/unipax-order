<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderImportController;
use App\Http\Controllers\DeliveryImportController;
use App\Http\Controllers\HomeClientController;
use App\Http\Controllers\ExcelPrintController;
use App\Http\Controllers\PhieuNhapController;
use Illuminate\Http\Request;
use App\Http\Controllers\PhieuUnipaxController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('orders/import', [OrderImportController::class, 'showForm'])->name('orders.import.form');
Route::post('orders/import', [OrderImportController::class, 'import'])->name('orders.import');
//
Route::get('deliveries/import', [DeliveryImportController::class, 'showForm'])->name('deliveries.import.form');
Route::post('deliveries/import', [DeliveryImportController::class, 'import'])->name('deliveries.import');

// Route for Home
Route::get('/client/home', [HomeClientController::class, 'index']);
Route::get('/api/production-orders', [HomeClientController::class, 'getData']);

//Route for Excel Print
Route::get('/excel', [ExcelPrintController::class, 'index'])->name('excel');
Route::post('/print', [ExcelPrintController::class, 'print'])->name('excel.print');

//

Route::prefix('phieu-nhap')->group(function () {
    Route::get('/', [PhieuUnipaxController::class, 'index'])->name('phieuunipax.index');
    Route::post('/', [PhieuUnipaxController::class, 'store'])->name('phieuunipax.store');
    Route::get('/view-all', [PhieuUnipaxController::class, 'viewAll'])->name('phieuunipax.viewAll');
    Route::delete('/delete/{id}', [PhieuUnipaxController::class, 'delete'])->name('phieuunipax.delete');
});