<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    protected $table = 'nhan_vien';

    protected $fillable = [
        'ma_nv',
        'ten_nv',
        'ngay_vao',
        'bo_phan',
        'gioi_tinh',
        'ngay_sinh'
    ];
}
