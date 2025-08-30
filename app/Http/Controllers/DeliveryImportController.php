<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\DeliveriesImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Delivery;
use Illuminate\Support\Facades\Schema;

class DeliveryImportController extends Controller
{
    public function showForm()
    {
        return view('deliveries.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);
    // Tắt kiểm tra khóa ngoại
        // Tắt kiểm tra khóa ngoại
    Schema::disableForeignKeyConstraints();
    Delivery::truncate();
    Schema::enableForeignKeyConstraints();
        Excel::import(new DeliveriesImport, $request->file('file'));

        return back()->with('success', 'Import dữ liệu hàng về thành công!');
    }
}
