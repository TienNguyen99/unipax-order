<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\OrdersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Order; // thêm model
use Illuminate\Support\Facades\Schema;

class OrderImportController extends Controller
{
    public function showForm()
    {
        return view('orders.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
    // Tắt kiểm tra khóa ngoại
    // Schema::disableForeignKeyConstraints();
    // Order::truncate();
    // Schema::enableForeignKeyConstraints();

    Excel::import(new OrdersImport, $request->file('file'));

    return back()->with('success', 'Import dữ liệu thành công!');
    }
}
