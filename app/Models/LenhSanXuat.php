<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LenhSanXuat extends Model
{
    protected $table = 'lenh_sx';
    protected $fillable = [
        'ma_lenh', 'po', 'nhan_vien_theo_doi', 'khach_hang', 'model_code',
        'item_code', 'description', 'size', 'color', 'unit', 'so_luong_dat',
        'don_gia', 'ngay_nhan', 'ngay_hen_giao', 'ngay_khach_yeu_cau', 'noi_giao'
    ];
}
