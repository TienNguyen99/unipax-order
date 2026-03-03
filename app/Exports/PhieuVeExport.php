<?php

namespace App\Exports;

use App\Models\PhieuVe;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PhieuVeExport implements FromQuery, WithHeadings
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function query()
    {
        return PhieuVe::whereIn('id', $this->ids)
            ->select(
                'id',
                'phieu_ps',
                'ma_hang',
                'ma_lenh',
                'kich_thuoc',
                'mau_vai',
                'so_luong_donhang',
                'so_luong_nhan',
                'makhac_dat',
                'makhac_loi',
                'front_dat',
                'front_loi',
                'back_dat',
                'back_loi',
                'ghi_chu'
            );
    }

    public function headings(): array
    {
        return [
            'ID',
            'Phiếu PS',
            'Mã Hàng',
            'Mã Lệnh',
            'Kích Thước',
            'Màu Vải',
            'SL Đơn Hàng',
            'SL Nhận',
            'Mã Khác Đạt',
            'Mã Khác Lỗi',
            'Front Đạt',
            'Front Lỗi',
            'Back Đạt',
            'Back Lỗi',
            'Ghi Chú',
        ];
    }
}
