<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhanTichLog extends Model
{
    use HasFactory;

    protected $table = 'phan_tich_logs';
    
    protected $fillable = [
        'so_phieu',
        'lenh_sx',
        'nhan_vien_id',
        'ngay_nhap',
        'ingredients', // JSON array
        'dien_giai',
        'da_in'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'da_in' => 'boolean',
        'ngay_nhap' => 'datetime',
    ];

    public function lenhSanXuat()
    {
        return $this->belongsTo(LenhSanXuat::class, 'lenh_sx', 'ma_lenh');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'nhan_vien_id', 'ma_nv');
    }
}
