<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PhieuVe;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

            foreach ($cart as $phieuId => $data) {
                try {
                    $phieuVe = PhieuVe::findOrFail($phieuId);
                    
                    // Validate dữ liệu dựa trên ma_hang
                    $validatedData = $this->validateDataByMaHang($data, $phieuVe->ma_hang);
                    $phieuVe->update($validatedData);
                    
                    $savedCount++;
                } catch (\Exception $e) {
                    $errors[] = "Phiếu {$data['phieu_ps']}: " . $e->getMessage();
                }
            }

            DB::commit();

            // Xóa giỏ sau khi lưu
            session()->forget('phieu_ve_cart');

            return response()->json([
                'success' => true,
                'message' => "Đã lưu $savedCount phiếu",
                'saved_count' => $savedCount,
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
}

