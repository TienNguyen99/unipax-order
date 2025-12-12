<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhapSXLog;
use App\Models\LenhSanXuat;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LenhSXImport;
use Illuminate\Support\Facades\DB;
use App\Exports\BaoCaoSXExport;
use Carbon\Carbon;

class NhapSXController extends Controller
{
    // Hiển thị form nhập SX
    public function showForm(Request $request, $ma_lenh = null)
    {
        $lenhSXs = LenhSanXuat::select('ma_lenh', 'description')
            ->orderBy('ma_lenh')
            ->get();

        return view('client.congnhan', [
            'lenhSXs' => $lenhSXs,
            'ma_lenh_url' => $ma_lenh,
        ]);
    }

    // Ghi log nhập SX (AJAX)
    public function postNhapSX(Request $request)
    {
        $validated = $request->validate([
            'lenh_sx' => 'required|string|max:50',
            'cong_doan' => 'required|string|max:10',
            'nhan_vien_id' => 'nullable|string|max:20',
            'so_luong_dat' => 'nullable|string|max:20',
            'so_luong_loi' => 'nullable|string|max:20',
            'dien_giai' => 'nullable|string|max:500',
            'may_sx' => 'nullable|string|max:100',
            'so_pick' => 'nullable|string|max:50',
            'so_cuon' => 'nullable|string|max:50',
            'so_dong' => 'nullable|string|max:50',
            'so_ban' => 'nullable|string|max:50',
            'so_dau' => 'nullable|string|max:50',
            'so_khuon' => 'nullable|string|max:50',
            'khuon_sx' => 'nullable|string|max:100',
        ]);

        $log = NhapSXLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu. Vui lòng gặp anh Thái để in phiếu!',
            'data' => $log
        ]);
    }

    // API tìm kiếm mã lệnh
    public function searchLenhSX(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') return response()->json([]);

        $data = LenhSanXuat::select('ma_lenh', 'description')
            ->where('ma_lenh', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->orderBy('ma_lenh')
            ->take(20)
            ->get();

        return response()->json($data);
    }

    // Hiển thị view danh sách (không cần $data)
    public function list()
    {
        return view('client.list');
    }

    // API trả dữ liệu JSON
    public function apiLatest()
    {
        $data = NhapSXLog::orderBy('da_in', 'asc')->take(50)->get();
        return response()->json($data);
    }

    // In lệnh SX (check đã in hôm nay)
    public function checkAndPrint($id)
{
    $log = NhapSXLog::findOrFail($id);
    $today = Carbon::today()->toDateString();

    // $alreadyPrinted = NhapSXLog::where('lenh_sx', $log->lenh_sx)
    //     ->whereDate('created_at', $today)
    //     ->where('da_in', true)
    //     ->exists();
$alreadyPrinted = NhapSXLog::where('id', $id)
    ->whereDate('created_at', $today)
    ->where('da_in', true)
    ->exists();
    $forcePrint = request()->get('force', false);

    if ($alreadyPrinted && !$forcePrint) {
        return response()->json([
            'success' => false,
            'confirm' => true,
            'message' => 'Phiếu đã in. Có muốn in lại ?'
        ]);
    }

    // ✅ Cập nhật ngay_nhap và da_in **trước khi export**
    $log->da_in = true;
    $log->ngay_nhap = now();
    $log->save();

    return response()->json([
        'success' => true,
        'message' => '✅ In thành công!',
        'pdf_url' => route('bao-cao-sx.pdf', ['id' => $id])
    ]);
}


    // Import Excel
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

    // Xuất báo cáo PDF
    public function exportBaoCaoPDF($id)
    {
        $exporter = new BaoCaoSXExport(null, $id);
        $filePath = $exporter->exportToPDF();

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
