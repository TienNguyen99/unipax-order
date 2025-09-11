<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    /**Bang delivery */
    protected $table = 'deliveries';
    protected $fillable = [
        'order_id',
        'ngay_xuat',
        'ma_hang',
        'ps_code',
        'size',
        'ngay_gui_panel',
        'so_phieu',
        'sl_dat',
        'sl_thuc_nhan',
        'ngay_giao',
        'sl_giao_dat',
        'sl_giao_loi',
        'ghi_chu',
        'panel',
        'loai',
        'noi_giao',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
