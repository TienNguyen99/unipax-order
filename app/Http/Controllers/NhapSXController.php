<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhapSXLog;
use App\Models\LenhSanXuat;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LenhSXImport;
use Illuminate\Support\Facades\DB;
use App\Exports\BaoCaoSXExport;

class NhapSXController extends Controller
{
    // 🟢 Hiển thị form nhập SX
    public function showForm()
    {
        $lenhSXs = LenhSanXuat::select('ma_lenh', 'description')
            ->orderBy('ma_lenh')
            ->get();

        return view('client.congnhan', compact('lenhSXs'));
    }

    // 🟢 Ghi log nhập SX (AJAX)
    public function postNhapSX(Request $request)
    {
        $validated = $request->validate([
            'lenh_sx' => 'required|string|max:50',
            'cong_doan' => 'required|string|max:10',
            'so_luong_dat' => 'required|integer|min:0',
            'so_luong_loi' => 'nullable|integer|min:0',
            'dien_giai' => 'nullable|string|max:500',
        ]);

        $log = NhapSXLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu dữ liệu thành công!',
            'data' => $log
        ]);
    }
        // 🟢 🔍 API Tìm kiếm mã lệnh (cho gợi ý trong form)
    public function searchLenhSX(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') {
            return response()->json([]);
        }

        $data = LenhSanXuat::select('ma_lenh', 'description')
            ->where('ma_lenh', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->orderBy('ma_lenh')
            ->take(20)
            ->get();

        return response()->json($data);
    }
    // 🟢 Xem danh sách nhập SX
    public function list()
    {
        $data = NhapSXLog::orderBy('id', 'desc')->take(50)->get();
        return view('client.list', compact('data'));
    }

    // 🟢 Import Excel (xóa toàn bộ dữ liệu cũ)
    public function importLenhSX(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Không có file được tải lên.'
                ]);
            }

            DB::table('lenh_sx')->truncate();
            Excel::import(new LenhSXImport, $request->file('file'));

            return response()->json([
                'success' => true,
                'message' => '✅ Đã xóa dữ liệu cũ và import mới thành công!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Lỗi khi import: ' . $e->getMessage()
            ]);
        }
    }

    // 🟢 Xuất báo cáo ra PDF cho bản ghi vừa nhập
    public function exportBaoCaoPDF($id)
    {
        $exporter = new BaoCaoSXExport(null, $id);
        $filePath = $exporter->exportToPDF();

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
