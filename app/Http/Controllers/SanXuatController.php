<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhieuVe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SanXuatController extends Controller
{
    // ─────────────────────────────────────────────
    // DASHBOARD
    // ─────────────────────────────────────────────
    public function dashboard()
    {
        $stats = [
            'tong_ps'        => PhieuVe::count(),
            'chua_sx'        => PhieuVe::where('trang_thai_sx', 'chua_sx')->count(),
            'dang_sx'        => PhieuVe::where('trang_thai_sx', 'dang_sx')->count(),
            'hoan_thanh'     => PhieuVe::where('trang_thai_sx', 'hoan_thanh')->count(),
            'cho_xuat_kho'   => PhieuVe::where('trang_thai_sx', 'hoan_thanh')
                                        ->whereNull('ngay_xuat_kho')->count(),
            'da_xuat_kho'    => PhieuVe::whereNotNull('ngay_xuat_kho')->count(),
        ];

        // QC tổng hợp
        $qcTong = PhieuVe::selectRaw('
            SUM(CAST(NULLIF(makhac_dat, "") AS UNSIGNED))   as tong_makhac_dat,
            SUM(CAST(NULLIF(makhac_loi, "") AS UNSIGNED))   as tong_makhac_loi,
            SUM(CAST(NULLIF(front_dat,  "") AS UNSIGNED))   as tong_front_dat,
            SUM(CAST(NULLIF(front_loi,  "") AS UNSIGNED))   as tong_front_loi,
            SUM(CAST(NULLIF(back_dat,   "") AS UNSIGNED))   as tong_back_dat,
            SUM(CAST(NULLIF(back_loi,   "") AS UNSIGNED))   as tong_back_loi
        ')->first();

        // PS mới nhập gần đây
        $recentPs = PhieuVe::orderBy('created_at', 'desc')->limit(10)->get();

        // Chart: phân bổ trạng thái SX theo thang_chot
        $byThang = PhieuVe::select('thang_chot', 'trang_thai_sx', DB::raw('count(*) as total'))
            ->whereNotNull('thang_chot')
            ->groupBy('thang_chot', 'trang_thai_sx')
            ->orderBy('thang_chot', 'asc')
            ->get();

        // 🚛 Upcoming Exports: Sắp đến ngày xuất kho (chưa xuất)
        $upcomingExports = PhieuVe::whereNull('ngay_xuat_kho')
            ->whereNotNull('export_date')
            ->where('export_date', '!=', '')
            ->orderBy('export_date', 'asc')
            ->limit(10)
            ->get();

        return view('san-xuat.dashboard', compact('stats', 'qcTong', 'recentPs', 'byThang', 'upcomingExports'));
    }

    // ─────────────────────────────────────────────
    // QUẢN LÝ PHIẾU PS
    // ─────────────────────────────────────────────
    public function psIndex(Request $request)
    {
        $query = PhieuVe::query();

        if ($request->filled('trang_thai_sx')) {
            $query->where('trang_thai_sx', $request->trang_thai_sx);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('phieu_ps',  'like', "%$s%")
                  ->orWhere('ma_hang', 'like', "%$s%")
                  ->orWhere('ma_lenh', 'like', "%$s%")
                  ->orWhere('vi_tri',  'like', "%$s%");
            });
        }
        if ($request->filled('thang_chot')) {
            $query->where('thang_chot', $request->thang_chot);
        }

        $psList = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        $thangChotList = PhieuVe::select('thang_chot')
            ->whereNotNull('thang_chot')
            ->distinct()
            ->orderBy('thang_chot', 'desc')
            ->pluck('thang_chot');

        return view('san-xuat.ps.index', compact('psList', 'thangChotList'));
    }

    public function psExport(Request $request)
    {
        $query = PhieuVe::query();

        if ($request->filled('trang_thai_sx')) {
            $query->where('trang_thai_sx', $request->trang_thai_sx);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('phieu_ps',  'like', "%$s%")
                  ->orWhere('ma_hang', 'like', "%$s%")
                  ->orWhere('ma_lenh', 'like', "%$s%")
                  ->orWhere('vi_tri',  'like', "%$s%");
            });
        }
        if ($request->filled('thang_chot')) {
            $query->where('thang_chot', $request->thang_chot);
        }

        $ids = $query->pluck('id')->toArray();

        if (empty($ids)) {
            return back()->with('error', 'Không có dữ liệu để xuất');
        }

        // Tự động điền ngày xuất kho là hôm nay và đánh dấu hoàn thành cho các phiếu được xuất
        PhieuVe::whereIn('id', $ids)->update([
            'ngay_xuat_kho' => now()->format('d/m/Y'),
            'trang_thai_sx' => 'hoan_thanh'
        ]);

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\PhieuVeExport($ids),
            'danh_sach_ps_' . date('Ymd_His') . '.xlsx'
        );
    }

    public function psImport(Request $request)
    {
        // Import đã được xử lý bởi route/controller riêng
        return back()->with('info', 'Import được xử lý riêng.');
    }

    public function psUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'trang_thai_sx' => 'required|in:chua_sx,dang_sx,hoan_thanh',
        ]);

        try {
            $ps = PhieuVe::findOrFail($id);
            $ps->update(['trang_thai_sx' => $request->trang_thai_sx]);

            return response()->json([
                'success'       => true,
                'trang_thai_sx' => $ps->trang_thai_sx,
                'message'       => 'Đã cập nhật trạng thái',
            ]);
        } catch (\Exception $e) {
            Log::error('psUpdateStatus error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────
    // QC NHẬP (trang nhập đạt/lỗi theo flow)
    // ─────────────────────────────────────────────
    public function qcNhap()
    {
        return view('san-xuat.qc.nhap');
    }

    // ─────────────────────────────────────────────
    // QC ĐẠT / LỖI
    // ─────────────────────────────────────────────
    public function qcIndex(Request $request)
    {
        $query = PhieuVe::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('phieu_ps',  'like', "%$s%")
                  ->orWhere('ma_hang', 'like', "%$s%");
            });
        }
        if ($request->filled('tu_ngay')) {
            $query->whereDate('created_at', '>=', $request->tu_ngay);
        }
        if ($request->filled('den_ngay')) {
            $query->whereDate('created_at', '<=', $request->den_ngay);
        }
        if ($request->filled('co_loi')) {
            $query->where(function ($q) {
                $q->where('makhac_loi', '>', '0')
                  ->orWhere('front_loi',  '>', '0')
                  ->orWhere('back_loi',   '>', '0');
            });
        }

        $qcList = $query->select(
            'id', 'phieu_ps', 'ma_hang', 'ma_lenh', 'kich_thuoc',
            'mau_vai', 'so_luong_donhang', 'so_luong_nhan',
            'makhac_dat', 'makhac_loi',
            'front_dat',  'front_loi',
            'back_dat',   'back_loi',
            'trang_thai_sx', 'ghi_chu', 'created_at'
        )->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        // Tổng hợp
        $tongHop = PhieuVe::selectRaw('
            COALESCE(SUM(CAST(NULLIF(makhac_dat,"") AS UNSIGNED)),0) as tong_makhac_dat,
            COALESCE(SUM(CAST(NULLIF(makhac_loi,"") AS UNSIGNED)),0) as tong_makhac_loi,
            COALESCE(SUM(CAST(NULLIF(front_dat,"")  AS UNSIGNED)),0) as tong_front_dat,
            COALESCE(SUM(CAST(NULLIF(front_loi,"")  AS UNSIGNED)),0) as tong_front_loi,
            COALESCE(SUM(CAST(NULLIF(back_dat,"")   AS UNSIGNED)),0) as tong_back_dat,
            COALESCE(SUM(CAST(NULLIF(back_loi,"")   AS UNSIGNED)),0) as tong_back_loi
        ')->first();

        return view('san-xuat.qc.index', compact('qcList', 'tongHop'));
    }

    // ─────────────────────────────────────────────
    // XUẤT KHO
    // ─────────────────────────────────────────────
    public function xuatKhoIndex(Request $request)
    {
        $query = PhieuVe::query();

        if ($request->filled('trang_thai_xk')) {
            if ($request->trang_thai_xk === 'chua') {
                $query->whereNull('ngay_xuat_kho');
            } else {
                $query->whereNotNull('ngay_xuat_kho');
            }
        } else {
            // Mặc định hiển thị chưa xuất kho
            $query->whereNull('ngay_xuat_kho');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('phieu_ps',  'like', "%$s%")
                  ->orWhere('ma_hang', 'like', "%$s%")
                  ->orWhere('noi_giao','like', "%$s%");
            });
        }

        $xuatKhoList = $query->orderBy('created_at', 'desc')->paginate(30)->withQueryString();

        return view('san-xuat.xuat-kho.index', compact('xuatKhoList'));
    }

    public function xuatKhoConfirm(Request $request, $id)
    {
        try {
            $ps = PhieuVe::findOrFail($id);

            $ngay = $request->input('ngay_xuat_kho', now()->format('d/m/Y'));
            $ps->update([
                'ngay_xuat_kho' => $ngay,
                'trang_thai_sx' => 'hoan_thanh',
            ]);

            return response()->json([
                'success'       => true,
                'ngay_xuat_kho' => $ps->ngay_xuat_kho,
                'message'       => 'Đã xác nhận xuất kho: ' . $ps->phieu_ps,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
