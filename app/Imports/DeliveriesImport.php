<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Delivery;




class DeliveriesImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public function calculatedFormulas(): bool
    {
        return true; // luôn lấy kết quả tính toán của công thức
    }

    public function model(array $row)
    {
        $ngay_xuat = (isset($row['export_date']) && is_numeric($row['export_date']))
            ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['export_date'])
            : null;

        $ps_code = $row['ps_sub'] ?? null;

        // tìm max order_id trong nhóm (ps_code + ngay_xuat)
        $lastOrder = Delivery::where('ps_code', $ps_code)
            ->whereDate('ngay_xuat', $ngay_xuat)
            ->max('order_id');

        // nếu chưa có thì reset về 1, nếu có thì tăng lên
        $newOrderId = $lastOrder ? $lastOrder + 1 : 1;

        return Delivery::create([
            'order_id'      => $newOrderId,
            'ngay_xuat'     => $ngay_xuat,
            'ngay_gui_panel' => (isset($row['date_out_panel']) && is_numeric($row['date_out_panel']))
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_out_panel'])
                : null,
            'ngay_giao'     => (isset($row['delivery_date']) && is_numeric($row['delivery_date']))
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['delivery_date'])
                : null,
            'ma_hang'       => $row['ma_hang'] ?? null,
            'ps_code'       => $ps_code,
            'size'          => $row['size'] ?? null,
            'so_phieu'      => $row['so_phieu'] ?? null,
            'sl_dat'        => $row['quantity_order'] ?? null,
            'sl_thuc_nhan'  => $row['quantity_nhan'] ?? null,
            'sl_giao_dat'   => $row['dat'] ?? null,
            'sl_giao_loi'   => $row['loi'] ?? null,
            'ghi_chu'       => $row['ghi_chu'] ?? null,
            'panel'         => $row['mat'] ?? null,
            'noi_giao'      => $row['noi_giao'] ?? null,
            'loai'          => $row['type'] ?? null,
        ]);
    }
}
