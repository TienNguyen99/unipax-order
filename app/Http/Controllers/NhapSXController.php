<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NhapSXLog;
use App\Models\LenhSanXuat;
use App\Models\PhieuVe;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LenhSXImport;
use App\Imports\PhieuVeImport;
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
        $khuVuc = $request->get('khu_vuc', 'khu_vuc_1');

        // Gọi in tự động với khu_vuc
        try {
            $this->autoPrintQC($log->id, $khuVuc);
        } catch (\Exception $e) {
            \Log::warning("Không thể in: " . $e->getMessage());
        }

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
                // 🔑 Tạo so_phieu duy nhất cho cả phiếu QC
                $soPhieu = 'QC' . date('YmdHis');

                foreach ($qcRows as $index => $row) {
                    // Validate từng row
                    if (empty($row['lenh_sx']) || empty($row['so_luong_dat'])) {
                        $errors[] = "Dòng " . ($index + 1) . ": Thiếu mã lệnh hoặc số lượng đạt";
                        continue;
                    }

                    // Kết hợp ghi chú chung + ghi chú riêng
                    $dienGiai = $request->dien_giai;
                    if (!empty($row['dien_giai'])) {
                        $dienGiai = $dienGiai ? $dienGiai . ' | ' . $row['dien_giai'] : $row['dien_giai'];
                    }

                    // Tạo record với so_phieu chung
                    $log = NhapSXLog::create([
                        'so_phieu' => $soPhieu,
                        'lenh_sx' => $row['lenh_sx'],
                        'cong_doan' => $request->cong_doan,
                        'nhan_vien_id' => $request->nhan_vien_id,
                        'so_luong_dat' => $row['so_luong_dat'],
                        'so_luong_loi' => $row['so_luong_loi'] ?? 0,
                        'dien_giai' => $dienGiai,
                    ]);

                    $savedIds[] = $log->id;
                }

                DB::commit();

                // 🖨️ In phiếu QC 1 lần duy nhất (sau khi tất cả records được lưu)
                if (!empty($savedIds)) {
                    try {
                        $khuVuc = $request->get('khu_vuc', 'khu_vuc_1');
                        $this->printQCPhieu($soPhieu, $khuVuc);
                    } catch (\Exception $e) {
                        \Log::warning("Không thể in phiếu QC: {$soPhieu}");
                    }
                }

                $message = count($savedIds) . ' lệnh QC đã được lưu thành công! (Phiếu: ' . $soPhieu . ')';
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

    // Tự động in phiếu (chỉ cho normal SX, QC dùng printQCPhieu)
    private function autoPrintQC($id, $khuVuc = 'khu_vuc_1')
    {
        $log = NhapSXLog::findOrFail($id);

        // ⏭️ Bỏ qua in tự động cho QC (sẽ in 1 lần duy nhất sau khi tất cả records được lưu)
        if (strtoupper(trim($log->cong_doan ?? '')) === 'QC') {
            return;
        }

        $log->da_in = true;
        $log->ngay_nhap = now();
        $log->save();

        $pdfUrl = route('bao-cao-sx.pdf', ['identifier' => $log->id]);

        // Gọi node in (không chờ response)
        try {
            Http::timeout(1)->withHeaders([
                'X-API-KEY' => 'IN_LBP2900_2025'
            ])->post('http://192.168.1.14:3333/print', [
                'pdf_url' => $pdfUrl,
                'khu_vuc' => $khuVuc,
            ]);
        } catch (\Exception $e) {
            // Bỏ qua lỗi in
        }
    }

    // In phiếu QC multi-row 1 lần duy nhất
    private function printQCPhieu($soPhieu, $khuVuc = 'khu_vuc_1')
    {
        // Lấy 1 record để mark da_in cho tất cả
        $logs = NhapSXLog::where('so_phieu', $soPhieu)->get();
        
        if ($logs->isNotEmpty()) {
            // Mark tất cả records là đã in
            NhapSXLog::where('so_phieu', $soPhieu)->update([
                'da_in' => true,
                'ngay_nhap' => now()
            ]);

            $pdfUrl = route('bao-cao-sx.pdf', ['identifier' => $soPhieu]);

            // Gọi node in (không chờ response)
            try {
                Http::timeout(1)->withHeaders([
                    'X-API-KEY' => 'IN_LBP2900_2025'
                ])->post('http://192.168.1.14:3333/print', [
                    'pdf_url' => $pdfUrl,
                    'khu_vuc' => $khuVuc,
                ]);
            } catch (\Exception $e) {
                // Bỏ qua lỗi in
            }
        }
    }

    public function printDirect(Request $request, $id)
    {
        $log = NhapSXLog::findOrFail($id);
        $khuVuc = $request->get('khu_vuc', 'khu_vuc_1');

        // ✅ đánh dấu đã in
        $log->da_in = true;
        $log->ngay_nhap = now();
        $log->save();

        // Dùng so_phieu cho QC, dùng id cho normal
        $identifier = strtoupper($log->cong_doan) === 'QC' ? $log->so_phieu : $log->id;
        $pdfUrl = route('bao-cao-sx.pdf', ['identifier' => $identifier]);

        // ✅ gọi node in với khu_vuc
        try {
            Http::timeout(1)->withHeaders([
                'X-API-KEY' => 'IN_LBP2900_2025'
            ])->post('http://192.168.1.14:3333/print', [
                'pdf_url' => $pdfUrl,
                'khu_vuc' => $khuVuc,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi in: ' . $e->getMessage()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'In thành công!'
        ]);
    }

    // API tìm kiếm mã lệnh
    public function searchLenhSX(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') return response()->json([]);

        $data = LenhSanXuat::select('ma_lenh', 'description', 'so_luong_dat')
            ->where('ma_lenh', 'like', "%{$q}%")
            ->orWhere('description', 'like', "%{$q}%")
            ->orderBy('ma_lenh')
            ->take(5)
            ->get();

        return response()->json($data);
    }

    // API tìm mã lệnh từ phiếu PS
    public function searchPhieuPs(Request $request)
    {
        $q = trim($request->get('q', ''));
        if ($q === '') return response()->json([]);

        // Tìm phiếu về theo phieu_ps
        $phieuVe = PhieuVe::where('phieu_ps', 'like', "%{$q}%")
            ->first();

        if (!$phieuVe) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phiếu PS: ' . $q
            ]);
        }

        // Lấy model_code (ma_hang) và vi_tri từ phiếu về
        $modelCode = $phieuVe->ma_hang;
        $viTri = $phieuVe->vi_tri;

        // Tìm lenh_sx phù hợp: model_code = ma_hang AND don_gia = vi_tri
        $query = LenhSanXuat::select('ma_lenh', 'description', 'model_code', 'don_gia', 'item_code');

        if ($modelCode) {
            $query->where('model_code', $modelCode);
        }

        if ($viTri) {
            $query->where('don_gia', $viTri);
        }

        $lenhSX = $query->first();

        if (!$lenhSX) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy lệnh SX cho model: ' . ($modelCode ?: 'N/A') . ' và vi trí: ' . ($viTri ?: 'N/A')
            ]);
        }

        // Update ma_lenh vào phiếu về
        $phieuVe->update(['ma_lenh' => $lenhSX->ma_lenh]);

        return response()->json([
            'success' => true,
            'ma_lenh' => $lenhSX->ma_lenh,
            'description' => $lenhSX->description,
            'model_code' => $lenhSX->model_code,
            'don_gia' => $lenhSX->don_gia,
            'phieu_ps' => $phieuVe->phieu_ps,
            'message' => 'Tìm thấy lệnh: ' . $lenhSX->ma_lenh
        ]);
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
    public function checkAndPrint(Request $request, $id)
    {
        $log = NhapSXLog::findOrFail($id);
        $today = Carbon::today()->toDateString();
        $khuVuc = $request->get('khu_vuc', 'khu_vuc_1');

        $alreadyPrinted = NhapSXLog::where('id', $id)
            ->whereDate('created_at', $today)
            ->where('da_in', true)
            ->exists();
        
        $forcePrint = $request->get('force', false);

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

        // Dùng so_phieu cho QC, dùng id cho normal
        $identifier = strtoupper($log->cong_doan) === 'QC' ? $log->so_phieu : $log->id;

        // Gọi node in với khu_vuc
        try {
            Http::timeout(1)->withHeaders([
                'X-API-KEY' => 'IN_LBP2900_2025'
            ])->post('http://192.168.1.14:3333/print', [
                'pdf_url' => route('bao-cao-sx.pdf', ['identifier' => $identifier]),
                'khu_vuc' => $khuVuc,
            ]);
        } catch (\Exception $e) {
            // In tự động ở background, không cần return error
        }

        return response()->json([
            'success' => true,
            'message' => '✅ In thành công!',
            'pdf_url' => route('bao-cao-sx.pdf', ['identifier' => $identifier])
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

    // Import Phiếu Về
    public function importPhieuVe(Request $request)
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => '⚠️ Không có file được tải lên.'
                ]);
            }

            DB::table('phieu_ve')->truncate();
            $importer = new PhieuVeImport();
            Excel::import($importer, $request->file('file'));

            $stats = $importer->getImportStats();
            
            $message = '✅ Import thành công!';
            $message .= "\n📊 Tổng cộng: {$stats['imported_rows']} row được lưu";
            if ($stats['failed_rows'] > 0) {
                $message .= ", {$stats['failed_rows']} row lỗi";
            }
            
            $details = '';
            if (!empty($stats['failed_details'])) {
                $details = "Các row bị lỗi:\n";
                foreach ($stats['failed_details'] as $fail) {
                    $details .= "- Dòng {$fail['row_number']} (PS: {$fail['so_phieu']}, Mã hàng: {$fail['ma_hang']}, Vị trí: {$fail['vi_tri']}): {$fail['error']}\n";
                }
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'stats' => $stats,
                'failed_details' => $details
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Lỗi khi import: ' . $e->getMessage()
            ]);
        }
    }

    // Xuất báo cáo PDF (hỗ trợ cả id và so_phieu)
    public function exportBaoCaoPDF($identifier)
    {
        // Kiểm tra identifier là id hay so_phieu
        $log = null;
        if (is_numeric($identifier)) {
            // Là id
            $log = NhapSXLog::findOrFail($identifier);
        } else {
            // Là so_phieu (QC...)
            $log = NhapSXLog::where('so_phieu', $identifier)->firstOrFail();
        }

        $exporter = new BaoCaoSXExport(null, $log->id);
        $filePath = $exporter->exportToPDF();

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}