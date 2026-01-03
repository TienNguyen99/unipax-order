<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class NhapSXLog extends Model
{
    use HasFactory;

    protected $table = 'nhap_sx_logs';
    protected $fillable = [
        'so_phieu',
        'ngay_nhap',
        'lenh_sx',
        'cong_doan',
        'may_sx',
        'gio_sx',
        'so_pick',
        'so_cuon',
        'so_dong',
        'so_ban',
        'so_dau',
        'so_khuon',
        'khuon_sx',
        'so_luong_dat',
        'so_luong_loi',
        'dien_giai',
        'nhan_vien_id'
    ];
    public function lenhSanXuat()
    {
        return $this->belongsTo(LenhSanXuat::class, 'lenh_sx', 'ma_lenh');
    }
    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id','ma_nv');
    }
}
