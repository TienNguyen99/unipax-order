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
use App\Http\Controllers\MaterialCalculatorController;
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
Route::get('/print/approval', [ExcelPrintController::class, 'approvalList'])->name('print.approval');
Route::post('/print/approve/{id}', [ExcelPrintController::class, 'approve'])->name('print.approve');
Route::delete('/print/delete/{id}', [ExcelPrintController::class, 'deleteLog'])->name('print.delete');
// Debug route
Route::get('/print/test/{sheet?}', [ExcelPrintController::class, 'test'])->name('print.test');

//

Route::get('/nhap-sx', [NhapSXController::class, 'showForm'])->name('nhap-sx.form');
Route::post('/nhap-sx', [NhapSXController::class, 'postNhapSX'])->name('nhap-sx.submit');
Route::get('/nhap-sx/list', [NhapSXController::class, 'list'])->name('nhap-sx.list');
Route::get('/lenh-sx/search', [NhapSXController::class, 'searchLenhSX'])->name('lenh-sx.search');
Route::get('/phieu-ps/search', [NhapSXController::class, 'searchPhieuPs'])->name('phieu-ps.search');
Route::get('/nhap-sx/{ma_lenh}', [NhapSXController::class, 'showForm'])->name('nhap-sx.ma-lenh');

// Route import Excel (AJAX)
Route::post('/lenh-sx/import', [NhapSXController::class, 'importLenhSX'])->name('lenh-sx.import');
Route::post('/import-phieu-ve', [NhapSXController::class, 'importPhieuVe'])->name('phieu-ve.import');
// Route xuất báo cáo PDF (support cả id và so_phieu)
Route::get('/bao-cao-sx/pdf/{identifier}', [NhapSXController::class, 'exportBaoCaoPDF'])->name('bao-cao-sx.pdf');
Route::post('/nhap-sx/{id}/print', [NhapSXController::class, 'checkAndPrint'])->name('nhap-sx.print');
// in trực tiếp sau khi công nhân nhập
Route::post('/nhap-sx/{id}/print-direct', [NhapSXController::class, 'printDirect'])
    ->name('nhap-sx.print');

// Phiếu Về Entry Routes - Nhập dữ liệu công nhân
use App\Http\Controllers\PhieuVeEntryController;
Route::get('/phieu-ve-entry', [PhieuVeEntryController::class, 'show'])->name('phieu-ve-entry.show');
Route::post('/phieu-ve-entry/search', [PhieuVeEntryController::class, 'search'])->name('phieu-ve-entry.search');
Route::post('/phieu-ve-entry/save', [PhieuVeEntryController::class, 'save'])->name('phieu-ve-entry.save');
Route::post('/phieu-ve-entry/save-multiple', [PhieuVeEntryController::class, 'saveMultiple'])->name('phieu-ve-entry.save-multiple');
Route::post('/phieu-ve-entry/add-to-cart', [PhieuVeEntryController::class, 'addToCart'])->name('phieu-ve-entry.add-to-cart');
Route::post('/phieu-ve-entry/cart-count', [PhieuVeEntryController::class, 'getCartCount'])->name('phieu-ve-entry.cart-count');
Route::post('/phieu-ve-entry/get-cart', [PhieuVeEntryController::class, 'getCart'])->name('phieu-ve-entry.get-cart');
Route::post('/phieu-ve-entry/remove-from-cart', [PhieuVeEntryController::class, 'removeFromCart'])->name('phieu-ve-entry.remove-from-cart');
Route::post('/phieu-ve-entry/update-cart-item', [PhieuVeEntryController::class, 'updateCartItem'])->name('phieu-ve-entry.update-cart-item');
Route::post('/phieu-ve-entry/save-cart', [PhieuVeEntryController::class, 'saveCart'])->name('phieu-ve-entry.save-cart');
Route::get('/phieu-ve-entry/export-cart', [PhieuVeEntryController::class, 'exportCart'])->name('phieu-ve-entry.export-cart');
Route::get('/phieu-ve-entry/available-phieu', [PhieuVeEntryController::class, 'getAvailablePhieuXuatKho'])->name('phieu-ve-entry.available-phieu');

// Phiếu Xuất Kho Routes - Lịch sử và quản lý
Route::get('/phieu-xuat-kho', [PhieuVeEntryController::class, 'listPhieuXuatKho'])->name('phieu-xuat-kho.list');
Route::get('/phieu-xuat-kho/{id}', [PhieuVeEntryController::class, 'viewPhieuXuatKho'])->name('phieu-xuat-kho.view');
Route::get('/phieu-xuat-kho/{id}/print', [PhieuVeEntryController::class, 'printPhieuXuatKho'])->name('phieu-xuat-kho.print');
Route::post('/phieu-xuat-kho/{id}/update-status', [PhieuVeEntryController::class, 'updateStatusPhieuXuatKho'])->name('phieu-xuat-kho.update-status');
Route::post('/phieu-xuat-kho/{id}/add-items', [PhieuVeEntryController::class, 'addItemsToPhieuXuatKho'])->name('phieu-xuat-kho.add-items');
Route::post('/phieu-xuat-kho/item/{itemId}/update', [PhieuVeEntryController::class, 'updatePhieuXuatKhoItem'])->name('phieu-xuat-kho.item.update');
Route::delete('/phieu-xuat-kho/item/{itemId}/delete', [PhieuVeEntryController::class, 'deletePhieuXuatKhoItem'])->name('phieu-xuat-kho.item.delete');

// Material Calculator Routes
Route::get('/material-calculator', [MaterialCalculatorController::class, 'show'])
    ->name('material-calculator.show');
