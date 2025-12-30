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
use Illuminate\Support\Facades\Http;

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

    // Ghi log nhập SX (AJAX) - Updated để xử lý QC multi-row
    public function postNhapSX(Request $request)
    {
        // Kiểm tra nếu là QC multi-row
        if ($request->has('is_qc_multi') && $request->is_qc_multi == '1') {
            return $this->handleQCMultiRow($request);
        }

        // Xử lý normal flow
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
            'data' => [
                'id' => $log->id
            ]
        ]);
    }

    // Xử lý QC Multi-row
    private function handleQCMultiRow(Request $request)
    {
        try {
            $request->validate([
                'cong_doan' => 'required|string|max:10',
                'nhan_vien_id' => 'required|string|max:20',
                'dien_giai' => 'nullable|string|max:500',
                'qc_rows' => 'required|json',
            ]);

            $qcRows = json_decode($request->qc_rows, true);

            if (empty($qcRows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu QC để lưu!'
                ]);
            }

            $savedIds = [];
            $errors = [];

            DB::beginTransaction();

            try {
                foreach ($qcRows as $index => $row) {
                    // Validate từng row
                    if (empty($row['lenh_sx']) || empty($row['so_luong_dat'])) {
                        $errors[] = "Dòng " . ($index + 1) . ": Thiếu mã lệnh hoặc số lượng đạt";
                        continue;
                    }

                    // Tạo record
                    $log = NhapSXLog::create([
                        'lenh_sx' => $row['lenh_sx'],
                        'cong_doan' => $request->cong_doan,
                        'nhan_vien_id' => $request->nhan_vien_id,
                        'so_luong_dat' => $row['so_luong_dat'],
                        'so_luong_loi' => $row['so_luong_loi'] ?? 0,
                        'dien_giai' => $request->dien_giai . ' (QC Multi-row)',
                    ]);

                    $savedIds[] = $log->id;

                    // Tự động in phiếu cho mỗi lệnh
                    try {
                        $this->autoPrintQC($log->id);
                    } catch (\Exception $e) {
                        // Log lỗi in nhưng không dừng quá trình lưu
                        \Log::warning("Không thể in phiếu QC ID: {$log->id}");
                    }
                }

                DB::commit();

                $message = count($savedIds) . ' lệnh QC đã được lưu thành công!';
                if (!empty($errors)) {
                    $message .= ' Có ' . count($errors) . ' lỗi: ' . implode(', ', $errors);
                }

                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'id' => 'QC_' . implode('_', $savedIds),
                        'saved_count' => count($savedIds),
                        'saved_ids' => $savedIds
                    ]
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu QC: ' . $e->getMessage()
            ]);
        }
    }

    // Tự động in phiếu QC
    private function autoPrintQC($id)
    {
        $log = NhapSXLog::findOrFail($id);

        $log->da_in = true;
        $log->ngay_nhap = now();
        $log->save();

        $pdfUrl = route('bao-cao-sx.pdf', ['id' => $id]);

        // Gọi node in (không chờ response)
        try {
            Http::timeout(1)->withHeaders([
                'X-API-KEY' => 'IN_LBP2900_2025'
            ])->post('http://192.168.1.14:3333/print', [
                'pdf_url' => $pdfUrl,
            ]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi in
        }
    }

    public function printDirect($id)
    {
        $log = NhapSXLog::findOrFail($id);

        // ✅ đánh dấu đã in
        $log->da_in = true;
        $log->ngay_nhap = now();
        $log->save();

        $pdfUrl = route('bao-cao-sx.pdf', ['id' => $id]);

        // ✅ gọi node in
        Http::timeout(1)->withHeaders([
            'X-API-KEY' => 'IN_LBP2900_2025'
        ])->post('http://192.168.1.14:3333/print', [
            'pdf_url' => $pdfUrl,
        ]);

        return response()->json([
            'success' => true
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
            ->take(5)
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
        $data = NhapSXLog::orderBy('created_at', 'desc')->get();
        return response()->json($data);
    }

    // In lệnh SX (check đã in hôm nay)
    public function checkAndPrint($id)
    {
        $log = NhapSXLog::findOrFail($id);
        $today = Carbon::today()->toDateString();

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