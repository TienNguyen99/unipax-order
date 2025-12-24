<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderImportController;
use App\Http\Controllers\DeliveryImportController;
use App\Http\Controllers\HomeClientController;
use App\Http\Controllers\ExcelPrintController;
use App\Http\Controllers\PhieuNhapController;
use Illuminate\Http\Request;
use App\Http\Controllers\PhieuUnipaxController;
use App\Http\Controllers\NhapSXController;
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


//Route for Excel Print
Route::get('/excel', [ExcelPrintController::class, 'index'])->name('excel');
Route::post('/print', [ExcelPrintController::class, 'print'])->name('excel.print');

//

Route::get('/nhap-sx', [NhapSXController::class, 'showForm'])->name('nhap-sx.form');
Route::post('/nhap-sx', [NhapSXController::class, 'postNhapSX'])->name('nhap-sx.submit');
Route::get('/nhap-sx/list', [NhapSXController::class, 'list'])->name('nhap-sx.list');
Route::get('/lenh-sx/search', [NhapSXController::class, 'searchLenhSX'])->name('lenh-sx.search');
Route::get('/nhap-sx/{ma_lenh}', [NhapSXController::class, 'showForm'])->name('nhap-sx.ma-lenh');

// Route import Excel (AJAX)
Route::post('/lenh-sx/import', [NhapSXController::class, 'importLenhSX'])->name('lenh-sx.import');
// Route xuất báo cáo PDF
Route::get('/bao-cao-sx/pdf/{id}', [NhapSXController::class, 'exportBaoCaoPDF'])->name('bao-cao-sx.pdf');
Route::post('/nhap-sx/{id}/print', [NhapSXController::class, 'checkAndPrint'])->name('nhap-sx.print');
// in trực tiếp sau khi công nhân nhập
Route::post('/nhap-sx/{id}/print-direct', [NhapSXController::class, 'printDirect'])
    ->name('nhap-sx.print');
