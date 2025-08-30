<?php

namespace App\Imports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;


class OrdersImport implements ToModel, WithHeadingRow, WithChunkReading
{
    private static $psCounters = [];
public function __construct()
{
    ini_set('max_execution_time', 300);
}
    public function model(array $row)
    {
        $psCode = $row['ps_sub_don_hang'] ?? null;

        if (!isset(self::$psCounters[$psCode])) {
            self::$psCounters[$psCode] = 1;
        } else {
            self::$psCounters[$psCode]++;
        }

        return Order::updateOrCreate(
            [
                'ps_code'  => $psCode,
                'order_id' => self::$psCounters[$psCode],
            ],
            [
                'ma_hang'      => $row['ma_hang'] ?? null,
                'mau_vai'      => $row['fabric_color_mau_vai'] ?? null,
                'mau_logo'     => $row['logo_color_mau_in'] ?? null,
                'panel'        => $row['panel'] ?? null,
                'ngay_xuat'    => isset($row['export_date_ngay_xuat']) 
                                    ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['export_date_ngay_xuat']) 
                                    : null,
                'po_number'    => $row['so_phieu'] ?? null,
                'size'         => $row['size'] ?? null,
                'sl_dat'       => $row['quantity_order_so_luong_dat_hang_pcs'] ?? null,
                'don_vi_tinh'  => $row['don_vi_tinh'] ?? null,
                'mua'          => $row['mua'] ?? null,
                'gia'          => $row['gia'] ?? null,
                'ten_hang'     => $row['ten_hang'] ?? null,
                'ma_lenh'      => $row['ma_lenh'] ?? null,
                'hang_ve'      => $row['hang_ve'] ?? null,
                'da_giao'      => $row['da_giao'] ?? null,
                'con_lai'      => $row['con_lai'] ?? null,
                'tong_tien'    => $row['tong_tien_vnd'] ?? null,
                'ghi_chu'      => $row['ghi_chu'] ?? null,
            ]
        );
    }
        public function chunkSize(): int
    {
        return 1000; // đọc 1000 dòng mỗi lần
    }
}
