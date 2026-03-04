<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhieuVe;
use App\Models\PhieuXuatKho;
use App\Models\PhieuXuatKhoChiTiet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PhieuVeExport;

class PhieuVeEntryController extends Controller
{
    /**
     * Danh sách mã hàng đặc biệt (chỉ cho nhập front/back, không cho nhập makhac)
     */
    private const SPECIAL_MA_HANG = [
        'S2315CA1028',
        'S2315CA1028U',
        'S2515CA02GFU',
        'S2615CA1028U',
        'S2662LHAU350',
        'S2662LHAU351',
        'S2662LHAU362',
        'SMSUB2S26LEN05',
        'SMSUS25RUWFCAP'
    ];

    /**
     * Kiểm tra xem mã hàng có phải là loại đặc biệt không
     */
    private function isSpecialMaHang($maHang)
    {
        return in_array($maHang, self::SPECIAL_MA_HANG);
    }

    /**
     * Validate và lọc dữ liệu dựa trên ma_hang
     * Trả về array dữ liệu đã được validate
     */
    private function validateDataByMaHang($data, $maHang)
    {
        $validated = [];
        
        if ($this->isSpecialMaHang($maHang)) {
            // Mã hàng đặc biệt: chỉ lưu front/back
            $validated['makhac_dat'] = null;
            $validated['makhac_loi'] = null;
            $validated['front_dat'] = $data['front_dat'] ?? null;
            $validated['front_loi'] = $data['front_loi'] ?? null;
            $validated['back_dat'] = $data['back_dat'] ?? null;
            $validated['back_loi'] = $data['back_loi'] ?? null;
        } else {
            // Mã hàng thường: chỉ lưu makhac
            $validated['makhac_dat'] = $data['makhac_dat'] ?? null;
            $validated['makhac_loi'] = $data['makhac_loi'] ?? null;
            $validated['front_dat'] = null;
            $validated['front_loi'] = null;
            $validated['back_dat'] = null;
            $validated['back_loi'] = null;
        }
        
        $validated['ghi_chu'] = $data['ghi_chu'] ?? null;
        
        return $validated;
    }

    /**
     * Hiển thị form nhập dữ liệu phiếu về
     */
    public function show()
    {
        return view('client.phieu-ve-entry');
    }

    /**
     * Tìm kiếm phieu_ps và trả về dữ liệu
     * AJAX endpoint
     */
    public function search(Request $request)
    {
        try {
            $phieuPsSearch = $request->input('phieu_ps', '');
            
            if (empty($phieuPsSearch)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng nhập phieu_ps'
                ]);
            }

            // Tìm theo nhiều tiêu chí: phieu_ps, ma_hang, vi_tri
            $phieus = PhieuVe::where(function($query) use ($phieuPsSearch) {
                    $query->where('phieu_ps', 'like', '%' . $phieuPsSearch . '%')
                          ->orWhere('ma_hang', 'like', '%' . $phieuPsSearch . '%')
                          ->orWhere('vi_tri', 'like', '%' . $phieuPsSearch . '%')
                          ->orWhere('ma_lenh', 'like', '%' . $phieuPsSearch . '%');
                })
                ->select(
                    'id',
                    'phieu_ps',
                    'ma_hang',
                    'ma_lenh',
                    'kich_thuoc',
                    'mau_vai',
                    'mau_logo',
                    'ngay_nhan_panel',
                    'so_phieu',
                    'so_luong_donhang',
                    'so_luong_nhan',
                    'ngay_xuat_kho',
                    'makhac_dat',
                    'makhac_loi',
                    'front_dat',
                    'front_loi',
                    'back_dat',
                    'back_loi',
                    'ghi_chu',
                    'vi_tri',
                    'noi_giao',
                    'gia_cong'
                )
                ->orderBy('phieu_ps')
                ->get();

            if ($phieus->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy phieu_ps: ' . $phieuPsSearch
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $phieus,
                'count' => $phieus->count()
            ]);
        } catch (\Exception $e) {
            Log::error('PhieuVe Search Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tìm kiếm: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu dữ liệu nhập của công nhân
     * Chỉ cập nhật các cột: makhac_dat, makhac_loi, front_dat, front_loi, back_dat, back_loi, ghi_chu
     * AJAX endpoint
     */
    public function save(Request $request)
    {
        try {
            $phieuId = $request->input('phieu_id');
            
            $validated = $request->validate([
                'phieu_id' => 'required|integer|exists:phieu_ve,id',
                'makhac_dat' => 'nullable|string|max:1000',
                'makhac_loi' => 'nullable|string|max:1000',
                'front_dat' => 'nullable|string|max:1000',
                'front_loi' => 'nullable|string|max:1000',
                'back_dat' => 'nullable|string|max:1000',
                'back_loi' => 'nullable|string|max:1000',
                'ghi_chu' => 'nullable|string|max:1000',
            ]);

            // Lấy phiếu và validate dữ liệu dựa trên ma_hang
            $phieuVe = PhieuVe::findOrFail($phieuId);
            $validatedData = $this->validateDataByMaHang($validated, $phieuVe->ma_hang);
            
            // Cập nhật với dữ liệu đã được validate
            $phieuVe->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Dữ liệu đã lưu thành công'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('PhieuVe Save Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu hàng loạt từ form
     * Sử dụng khi công nhân submit form với dữ liệu từ nhiều phiếu
     */
    public function saveMultiple(Request $request)
    {
        try {
            $rows = $request->input('rows', []);
            
            if (empty($rows)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu để lưu'
                ]);
            }

            $savedCount = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $index => $rowData) {
                try {
                    $phieuId = $rowData['phieu_id'] ?? null;
                    
                    if (!$phieuId) {
                        $errors[] = "Hàng $index: Thiếu phieu_id";
                        continue;
                    }

                    $phieuVe = PhieuVe::findOrFail($phieuId);
                    
                    // Validate dữ liệu dựa trên ma_hang
                    $validatedData = $this->validateDataByMaHang($rowData, $phieuVe->ma_hang);
                    $phieuVe->update($validatedData);
                    
                    $savedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Hàng $index: " . $e->getMessage();
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Đã lưu $savedCount phiếu",
                'saved_count' => $savedCount,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('PhieuVe Save Multiple Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu dữ liệu: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm phiếu vào giỏ hàng (session)
     */
    public function addToCart(Request $request)
    {
        try {
            $phieuId = $request->input('phieu_id');
            
            if (!$phieuId) {
                return response()->json([
                    'success' => false,
                    'message' => 'phieu_id không hợp lệ'
                ]);
            }

            $phieuVe = PhieuVe::findOrFail($phieuId);
            
            // Lấy giỏ hàng hiện tại từ session
            $cart = session()->get('phieu_ve_cart', []);
            
            // Kiểm tra nếu đã có trong giỏ
            if (array_key_exists($phieuId, $cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu này đã có trong giỏ hàng'
                ]);
            }

            // Thêm vào giỏ
            $cart[$phieuId] = [
                'id' => $phieuVe->id,
                'phieu_ps' => $phieuVe->phieu_ps,
                'ma_hang' => $phieuVe->ma_hang,
                'ma_lenh' => $phieuVe->ma_lenh,
                'kich_thuoc' => $phieuVe->kich_thuoc,
                'mau_vai' => $phieuVe->mau_vai,
                'so_luong_donhang' => $phieuVe->so_luong_donhang,
                'so_luong_nhan' => $phieuVe->so_luong_nhan,
                'makhac_dat' => $phieuVe->makhac_dat,
                'makhac_loi' => $phieuVe->makhac_loi,
                'front_dat' => $phieuVe->front_dat,
                'front_loi' => $phieuVe->front_loi,
                'back_dat' => $phieuVe->back_dat,
                'back_loi' => $phieuVe->back_loi,
                'ghi_chu' => $phieuVe->ghi_chu,
                'vi_tri' => $phieuVe->vi_tri,
            ];

            session()->put('phieu_ve_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm vào giỏ hàng',
                'count' => count($cart)
            ]);
        } catch (\Exception $e) {
            Log::error('Add to Cart Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy số lượng phiếu trong giỏ
     */
    public function getCartCount()
    {
        $cart = session()->get('phieu_ve_cart', []);
        return response()->json([
            'count' => count($cart)
        ]);
    }

    /**
     * Lấy dữ liệu giỏ hàng (AJAX)
     */
    public function getCart()
    {
        $cart = session()->get('phieu_ve_cart', []);
        return response()->json([
            'cart' => $cart,
            'count' => count($cart)
        ]);
    }

    /**
     * Hiển thị giỏ hàng
     */
    public function showCart()
    {
        $cart = session()->get('phieu_ve_cart', []);
        return view('client.phieu-ve-cart', [
            'cart' => $cart
        ]);
    }

    /**
     * Xóa phiếu khỏi giỏ
     */
    public function removeFromCart(Request $request)
    {
        try {
            $phieuId = $request->input('phieu_id');
            $cart = session()->get('phieu_ve_cart', []);
            
            unset($cart[$phieuId]);
            
            session()->put('phieu_ve_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi giỏ hàng',
                'count' => count($cart)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách phiếu xuất kho chưa hoàn thành (để thêm items vào)
     */
    public function getAvailablePhieuXuatKho()
    {
        try {
            $phieus = PhieuXuatKho::with('chiTiet')
                ->whereIn('trang_thai', ['draft', 'confirmed'])
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function($phieu) {
                    return [
                        'id' => $phieu->id,
                        'ma_phieu' => $phieu->ma_phieu,
                        'ngay_xuat' => $phieu->ngay_xuat,
                        'trang_thai' => $phieu->trang_thai,
                        'tong_so_items' => $phieu->tong_so_items,
                        'current_items_count' => $phieu->chiTiet->count(),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $phieus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật dữ liệu trong giỏ hàng
     */
    public function updateCartItem(Request $request)
    {
        try {
            $phieuId = $request->input('phieu_id');
            $cart = session()->get('phieu_ve_cart', []);

            if (!isset($cart[$phieuId])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Phiếu không tìm thấy trong giỏ'
                ]);
            }

            // Cập nhật các trường
            $cart[$phieuId]['makhac_dat'] = $request->input('makhac_dat', $cart[$phieuId]['makhac_dat']);
            $cart[$phieuId]['makhac_loi'] = $request->input('makhac_loi', $cart[$phieuId]['makhac_loi']);
            $cart[$phieuId]['front_dat'] = $request->input('front_dat', $cart[$phieuId]['front_dat']);
            $cart[$phieuId]['front_loi'] = $request->input('front_loi', $cart[$phieuId]['front_loi']);
            $cart[$phieuId]['back_dat'] = $request->input('back_dat', $cart[$phieuId]['back_dat']);
            $cart[$phieuId]['back_loi'] = $request->input('back_loi', $cart[$phieuId]['back_loi']);
            $cart[$phieuId]['ghi_chu'] = $request->input('ghi_chu', $cart[$phieuId]['ghi_chu']);

            session()->put('phieu_ve_cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật, trước khi lưu vào database'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lưu tất cả phiếu trong giỏ vào database
     * Tạo phiếu xuất kho và cập nhật phiếu về
     * Hoặc thêm vào phiếu xuất kho đã có
     */
    public function saveCart(Request $request)
    {
        try {
            $cart = session()->get('phieu_ve_cart', []);

            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Giỏ hàng trống'
                ]);
            }

            $savedCount = 0;
            $errors = [];

            DB::beginTransaction();

            // 1. Kiểm tra xem có chọn phiếu xuất kho đã có không
            $existingPhieuId = $request->input('phieu_xuat_kho_id');
            
            if ($existingPhieuId) {
                // Thêm vào phiếu đã có
                $phieuXuatKho = PhieuXuatKho::findOrFail($existingPhieuId);
                
                // Kiểm tra trạng thái
                if (!in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed'])) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Phiếu xuất kho đã hoàn thành hoặc bị hủy, không thể thêm items'
                    ]);
                }
            } else {
                // Tạo phiếu xuất kho mới
                $phieuXuatKho = PhieuXuatKho::create([
                    'ma_phieu' => PhieuXuatKho::generateMaPhieu(),
                    'user_id' => Auth::id(),
                    'ngay_xuat' => now()->toDateString(),
                    'trang_thai' => 'confirmed',
                    'tong_so_items' => count($cart),
                    'ghi_chu' => $request->input('ghi_chu_phieu'),
                ]);
            }

            // 2. Lưu từng item và cập nhật phiếu về
            foreach ($cart as $phieuId => $data) {
                try {
                    $phieuVe = PhieuVe::findOrFail($phieuId);
                    
                    // Validate dữ liệu dựa trên ma_hang
                    $validatedData = $this->validateDataByMaHang($data, $phieuVe->ma_hang);
                    
                    // Cập nhật phiếu về
                    $phieuVe->update($validatedData);
                    
                    // Tạo chi tiết phiếu xuất kho (snapshot)
                    PhieuXuatKhoChiTiet::createFromPhieuVe(
                        $phieuXuatKho->id,
                        $phieuVe,
                        $validatedData
                    );
                    
                    $savedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Phiếu {$data['phieu_ps']}: " . $e->getMessage();
                }
            }

            // 3. Cập nhật tổng số items trong phiếu xuất kho
            if ($existingPhieuId) {
                // Nếu append vào phiếu có sẵn, cập nhật tổng số items
                $phieuXuatKho->tong_so_items = $phieuXuatKho->chiTiet()->count();
                $phieuXuatKho->save();
            }

            DB::commit();

            // Xóa giỏ sau khi lưu
            session()->forget('phieu_ve_cart');

            $actionMessage = $existingPhieuId 
                ? "Đã thêm $savedCount items vào phiếu xuất kho: {$phieuXuatKho->ma_phieu}"
                : "Đã lưu $savedCount phiếu vào phiếu xuất kho mới: {$phieuXuatKho->ma_phieu}";

            return response()->json([
                'success' => true,
                'message' => $actionMessage,
                'saved_count' => $savedCount,
                'ma_phieu' => $phieuXuatKho->ma_phieu,
                'phieu_xuat_kho_id' => $phieuXuatKho->id,
                'is_append' => (bool)$existingPhieuId,
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Save Cart Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xuất Excel từ giỏ hàng
     */
    public function exportCart()
    {
        $cart = session()->get('phieu_ve_cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Giỏ hàng trống');
        }

        // Collect IDs for export
        $ids = array_keys($cart);
        
        // Import Export class
        return Excel::download(
            new \App\Exports\PhieuVeExport($ids),
            'phieu_ve_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Danh sách phiếu xuất kho
     */
    public function listPhieuXuatKho(Request $request)
    {
        $query = PhieuXuatKho::with(['user', 'chiTiet'])
            ->orderBy('created_at', 'desc');

        // Lọc theo ngày
        if ($request->has('ngay_xuat')) {
            $query->whereDate('ngay_xuat', $request->ngay_xuat);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }

        // Lọc theo user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $phieuXuatKhos = $query->paginate(20);

        return view('client.phieu-xuat-kho-list', [
            'phieuXuatKhos' => $phieuXuatKhos
        ]);
    }

    /**
     * Chi tiết phiếu xuất kho
     */
    public function viewPhieuXuatKho($id)
    {
        $phieuXuatKho = PhieuXuatKho::with(['user', 'chiTiet.phieuVe'])
            ->findOrFail($id);

        return view('client.phieu-xuat-kho-detail', [
            'phieuXuatKho' => $phieuXuatKho
        ]);
    }

    /**
     * In/Xuất PDF phiếu xuất kho
     */
    public function printPhieuXuatKho($id)
    {
        $phieuXuatKho = PhieuXuatKho::with(['user', 'chiTiet'])
            ->findOrFail($id);

        // Collect phieu_ve IDs from chi tiết
        $phieuVeIds = $phieuXuatKho->chiTiet->pluck('phieu_ve_id')->toArray();

        return Excel::download(
            new \App\Exports\PhieuVeExport($phieuVeIds),
            $phieuXuatKho->ma_phieu . '_' . date('Y-m-d_His') . '.xlsx'
        );
    }

    /**
     * Cập nhật trạng thái phiếu xuất kho
     */
    public function updateStatusPhieuXuatKho(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'trang_thai' => 'required|in:draft,confirmed,completed,cancelled'
            ]);

            $phieuXuatKho = PhieuXuatKho::findOrFail($id);
            $phieuXuatKho->update([
                'trang_thai' => $validated['trang_thai']
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật trạng thái'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật item trong phiếu xuất kho
     */
    public function updatePhieuXuatKhoItem(Request $request, $itemId)
    {
        try {
            $item = PhieuXuatKhoChiTiet::findOrFail($itemId);
            $phieuXuatKho = $item->phieuXuatKho;

            // Kiểm tra trạng thái phiếu
            if (!in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể chỉnh sửa phiếu đã hoàn thành hoặc đã hủy'
                ], 403);
            }

            // Validate dữ liệu
            $validated = $request->validate([
                'makhac_dat' => 'nullable|string|max:50',
                'makhac_loi' => 'nullable|string|max:50',
                'front_dat' => 'nullable|string|max:50',
                'front_loi' => 'nullable|string|max:50',
                'back_dat' => 'nullable|string|max:50',
                'back_loi' => 'nullable|string|max:50',
                'vi_tri' => 'nullable|string|max:100',
                'ghi_chu' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Cập nhật item trong phiếu xuất kho
            $item->update($validated);

            // Cập nhật phiếu về tương ứng (nếu có)
            if ($item->phieu_ve_id) {
                $phieuVe = PhieuVe::find($item->phieu_ve_id);
                if ($phieuVe) {
                    $phieuVe->update($validated);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật item',
                'item' => $item->fresh()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa item khỏi phiếu xuất kho
     */
    public function deletePhieuXuatKhoItem($itemId)
    {
        try {
            $item = PhieuXuatKhoChiTiet::findOrFail($itemId);
            $phieuXuatKho = $item->phieuXuatKho;

            // Kiểm tra trạng thái phiếu
            if (!in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xóa item từ phiếu đã hoàn thành hoặc đã hủy'
                ], 403);
            }

            DB::beginTransaction();

            // Xóa item
            $item->delete();

            // Cập nhật tổng số items
            $phieuXuatKho->tong_so_items = $phieuXuatKho->chiTiet()->count();
            $phieuXuatKho->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa item',
                'new_total' => $phieuXuatKho->tong_so_items
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Thêm items vào phiếu xuất kho đã có
     */
    public function addItemsToPhieuXuatKho(Request $request, $id)
    {
        try {
            $phieuXuatKho = PhieuXuatKho::findOrFail($id);

            // Kiểm tra trạng thái phiếu
            if (!in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể thêm item vào phiếu đã hoàn thành hoặc đã hủy'
                ], 403);
            }

            $phieuVeIds = $request->input('phieu_ve_ids', []);

            if (empty($phieuVeIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chưa chọn phiếu nào'
                ]);
            }

            DB::beginTransaction();

            $addedCount = 0;
            $errors = [];

            foreach ($phieuVeIds as $phieuVeId) {
                try {
                    $phieuVe = PhieuVe::findOrFail($phieuVeId);

                    // Tạo chi tiết phiếu xuất kho (cho phép trùng)
                    PhieuXuatKhoChiTiet::create([
                        'phieu_xuat_kho_id' => $phieuXuatKho->id,
                        'phieu_ve_id' => $phieuVe->id,
                        'phieu_ps' => $phieuVe->phieu_ps,
                        'ma_hang' => $phieuVe->ma_hang,
                        'ma_lenh' => $phieuVe->ma_lenh,
                        'kich_thuoc' => $phieuVe->kich_thuoc,
                        'vi_tri' => $phieuVe->vi_tri,
                        'so_luong_donhang' => $phieuVe->so_luong_donhang,
                        'so_luong_nhan' => $phieuVe->so_luong_nhan,
                        'makhac_dat' => $phieuVe->makhac_dat,
                        'makhac_loi' => $phieuVe->makhac_loi,
                        'front_dat' => $phieuVe->front_dat,
                        'front_loi' => $phieuVe->front_loi,
                        'back_dat' => $phieuVe->back_dat,
                        'back_loi' => $phieuVe->back_loi,
                        'ghi_chu' => $phieuVe->ghi_chu,
                    ]);

                    $addedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Phiếu ID {$phieuVeId}: " . $e->getMessage();
                }
            }

            // Cập nhật tổng số items
            $phieuXuatKho->tong_so_items = $phieuXuatKho->chiTiet()->count();
            $phieuXuatKho->save();

            DB::commit();

            $message = "Đã thêm {$addedCount} item vào phiếu";
            if (!empty($errors)) {
                $message .= ". Lỗi: " . implode(', ', $errors);
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'added_count' => $addedCount,
                'errors' => $errors,
                'new_total' => $phieuXuatKho->tong_so_items
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }
}

