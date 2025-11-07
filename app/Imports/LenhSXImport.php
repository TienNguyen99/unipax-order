<?php
namespace App\Imports;

use App\Models\LenhSanXuat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class LenhSXImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    public function model(array $row)
    {
        if (empty($row['ma_lenh'])) return null;

        $soLuong = isset($row['so_luong_dat']) && is_numeric($row['so_luong_dat'])
            ? (int)$row['so_luong_dat'] : null;
        $donGia = isset($row['don_gia']) && is_numeric($row['don_gia'])
            ? (float)$row['don_gia'] : null;

        // Kiểm tra nếu mã lệnh đã có thì bỏ qua
        if (LenhSanXuat::where('ma_lenh', trim($row['ma_lenh']))->exists()) {
            return null;
        }

        return new LenhSanXuat([
            'ma_lenh' => trim($row['ma_lenh']),
            'po' => trim($row['po'] ?? ''),
            'nhan_vien_theo_doi' => trim($row['nhan_vien_theo_doi'] ?? ''),
            'khach_hang' => trim($row['khach_hang'] ?? ''),
            'model_code' => trim($row['model_code'] ?? ''),
            'item_code' => trim($row['item_code'] ?? ''),
            'description' => trim($row['description'] ?? ''),
            'size' => trim($row['size'] ?? ''),
            'color' => trim($row['color'] ?? ''),
            'unit' => trim($row['unit'] ?? ''),
            'so_luong_dat' => $soLuong,
            'don_gia' => $donGia,
            'ngay_nhan' => now(),
        ]);
    }

    public function onError(Throwable $error)
    {
        // Bỏ qua lỗi duplicate hoặc dòng lỗi khác, không dừng toàn bộ import
    }
}
