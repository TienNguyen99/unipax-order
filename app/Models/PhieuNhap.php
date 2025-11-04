<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuNhap extends Model
{
    use HasFactory;

    protected $connection = 'sqlite_unipax';
    protected $table = 'phieu_nhap';

    protected $fillable = [
        'ps', 'row_kd', 'mahang', 'size', 'mau', 'logo',
        'soluongdonhang', 'sl_thuc', 'dat', 'loi', 'mat',
        'ghichu', 'trangthai', 'ngayxuat', 'ngaynhap', 'nguoitao'
    ];

    protected $casts = [
        'ngayxuat' => 'date',
        'ngaynhap' => 'date',
    ];
}


