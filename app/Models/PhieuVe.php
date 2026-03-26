<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuVe extends Model
{
    use HasFactory;

    protected $table = 'phieu_ve';
    protected $fillable = [
        'export_date',
        'phieu_ps',
        'kich_thuoc',
        'mau_vai',
        'mau_logo',
        'ngay_nhan_panel',
        'so_phieu',
        'ma_hang',
        'ma_lenh',
        'trang_thai_sx',
        'front_dat',
        'front_loi',
        'back_dat',
        'back_loi',
        'makhac_dat',
        'makhac_loi',
        'vi_tri',
        'thang_chot',
        'ngay',
        'ghi_chu',
        'so_luong_nhan',
        'noi_giao',
        'ngay_xuat_kho',
        'so_luong_donhang',
        'gia_cong',
    ];

    protected $casts = [
        'trang_thai_sx' => 'string',
    ];

    public function scopeChuaSanXuat($query)
    {
        return $query->where('trang_thai_sx', 'chua_sx');
    }

    public function scopeDangSanXuat($query)
    {
        return $query->where('trang_thai_sx', 'dang_sx');
    }

    public function scopeHoanThanh($query)
    {
        return $query->where('trang_thai_sx', 'hoan_thanh');
    }

    public function scopeChuaXuatKho($query)
    {
        return $query->where('trang_thai_sx', 'hoan_thanh')
                     ->whereNull('ngay_xuat_kho');
    }
}
