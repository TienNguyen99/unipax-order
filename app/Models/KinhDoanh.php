<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KinhDoanh extends Model
{
    protected $connection = 'sqlite_unipax';
    protected $table = 'kinh_doanh';
    protected $fillable = [
        'ps', 'row', 'ngayxuat', 'mahang', 'mau', 'size', 'mat',
        'logo', 'soluongdonhang', 'sl_thuc'
    ];
}
