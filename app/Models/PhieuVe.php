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
        'front_dat',
        'front_loi',
        'back_dat',
        'back_loi',
        'vi_tri',
        'ngay',
        'ghi_chu',
        'so_luong_nhan',
        'noi_giao',
        'ngay_xuat_kho',
        'so_luong_donhang',
    ];

    protected $casts = [
    ];
}
