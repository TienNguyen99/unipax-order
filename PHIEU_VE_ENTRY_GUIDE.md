# Hướng Dẫn Sử Dụng: Nhập Dữ Liệu Phiếu Về

## Tổng Quan
Tính năng này cho phép công nhân nhập dữ liệu vào các cột cụ thể của phiếu về sau khi tìm kiếm theo `phieu_ps`.

## Cấu Trúc

### 1. Controller: `PhieuVeEntryController`
Đường dẫn: `app/Http/Controllers/PhieuVeEntryController.php`

**Các method chính:**
- `show()` - Hiển thị form nhập dữ liệu
- `search(Request $request)` - Tìm kiếm phieu_ps (AJAX)
- `save(Request $request)` - Lưu dữ liệu một phiếu (AJAX)
- `saveMultiple(Request $request)` - Lưu dữ liệu nhiều phiếu (AJAX)

### 2. View: `phieu-ve-entry.blade.php`
Đường dẫn: `resources/views/client/phieu-ve-entry.blade.php`

**Giao diện chứa:**
- Section tìm kiếm: Nhập phieu_ps
- Bảng kết quả: Hiển thị dữ liệu from phiếu tìm thấy
- Modal chỉnh sửa: Các trường nhập liệu

### 3. Routes
Thêm vào `routes/web.php`:
```php
Route::get('/phieu-ve-entry', [PhieuVeEntryController::class, 'show'])->name('phieu-ve-entry.show');
Route::post('/phieu-ve-entry/search', [PhieuVeEntryController::class, 'search'])->name('phieu-ve-entry.search');
Route::post('/phieu-ve-entry/save', [PhieuVeEntryController::class, 'save'])->name('phieu-ve-entry.save');
Route::post('/phieu-ve-entry/save-multiple', [PhieuVeEntryController::class, 'saveMultiple'])->name('phieu-ve-entry.save-multiple');
```

## Flow Sử Dụng

### Bước 1: Truy cập trang
```
http://your-domain/phieu-ve-entry
```

### Bước 2: Tìm kiếm phiếu
1. Nhập mã **phieu_ps** (ví dụ: PS001, PS002)
2. Nhấn nút "Tìm Kiếm" hoặc Enter
3. Hệ thống hiển thị danh sách phiếu khớp

### Bước 3: Chọn phiếu và nhập dữ liệu
1. Nhấn nút "Sửa" trên hàng phiếu cần nhập
2. Modal mở ra hiển thị:
   - **Thông tin hiển thị (chỉ đọc)**: Mã Hàng, Mã Lệnh, Kích Thước, SL Đơn Hàng
   - **Các trường nhập liệu:**
     - Mã Khác Đạt
     - Mã Khác Lỗi
     - Front Đạt
     - Front Lỗi
     - Back Đạt
     - Back Lỗi
     - Ghi Chú

### Bước 4: Lưu dữ liệu
1. Nhập dữ liệu vào các trường
2. Nhấn nút "Lưu"
3. Hệ thống lưu và hiển thị thông báo thành công

## Chi Tiết Các Trường

| Trường | Kiểu | Max Length | Mô Tả |
|--------|------|-----------|-------|
| makhac_dat | Text | 1000 | Mã khác đạt chất lượng |
| makhac_loi | Text | 1000 | Mã khác lỗi chất lượng |
| front_dat | Text | 1000 | Số lượng front đạt |
| front_loi | Text | 1000 | Số lượng front lỗi |
| back_dat | Text | 1000 | Số lượng back đạt |
| back_loi | Text | 1000 | Số lượng back lỗi |
| ghi_chu | Text | 1000 | Ghi chú thêm |

## API Endpoints

### 1. Search `/phieu-ve-entry/search`
**Method:** POST  
**Content-Type:** application/json

**Request:**
```json
{
  "phieu_ps": "PS001"
}
```

**Response (Success):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "phieu_ps": "PS001",
      "ma_hang": "MH123",
      "ma_lenh": "ML456",
      "kich_thuoc": "10x10",
      "mau_vai": "Đỏ",
      "so_luong_donhang": "100",
      "so_luong_nhan": "100",
      "makhac_dat": "...",
      "makhac_loi": "...",
      "front_dat": "...",
      "front_loi": "...",
      "back_dat": "...",
      "back_loi": "...",
      "ghi_chu": "..."
    }
  ],
  "count": 1
}
```

**Response (Error):**
```json
{
  "success": false,
  "message": "Không tìm thấy phieu_ps: PS001"
}
```

### 2. Save `/phieu-ve-entry/save`
**Method:** POST  
**Content-Type:** application/json

**Request:**
```json
{
  "phieu_id": 1,
  "makhac_dat": "200",
  "makhac_loi": "5",
  "front_dat": "100",
  "front_loi": "0",
  "back_dat": "100",
  "back_loi": "0",
  "ghi_chu": "Hoàn thành xuất sắc"
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Dữ liệu đã lưu thành công"
}
```

**Response (Validation Error):**
```json
{
  "success": false,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "phieu_id": ["The phieu id field is required."]
  }
}
```

### 3. Save Multiple `/phieu-ve-entry/save-multiple`
**Method:** POST  
**Content-Type:** application/json

**Request:**
```json
{
  "rows": [
    {
      "phieu_id": 1,
      "makhac_dat": "200",
      "makhac_loi": "5",
      "front_dat": "100",
      "front_loi": "0",
      "back_dat": "100",
      "back_loi": "0",
      "ghi_chu": "Hoàn thành"
    },
    {
      "phieu_id": 2,
      "makhac_dat": "150",
      "makhac_loi": "10",
      ...
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Đã lưu 2 phiếu",
  "saved_count": 2,
  "errors": []
}
```

## Tính Năng

✅ Tìm kiếm phiếu theo mã  
✅ Hiển thị thông tin phiếu trong bảng  
✅ Modal chỉnh sửa với form input  
✅ Lưu dữ liệu một phiếu (AJAX)  
✅ Lưu dữ liệu nhiều phiếu cùng lúc  
✅ Validation input  
✅ Error handling & user feedback  
✅ Responsive design (Bootstrap 5)

## Ghi Chú

- **Tìm kiếm**: Hỗ trợ tìm kiếm không phân biệt hoa thường
- **Lưu**: Sử dụng partial update, chỉ lưu các trường được nhập
- **Validation**: Server-side validation trên PHP, dữ liệu tối đa 1000 ký tự
- **Database**: Transaction support cho save multiple
- **Error Log**: Các lỗi được ghi vào logs/laravel.log

## Hỗ Trợ Thêm

Nếu cần thêm tính năng:
- Export data thành Excel
- Báo cáo thống kê
- Filter nâng cao
- Pagination

Liên hệ team development để cập nhật.
