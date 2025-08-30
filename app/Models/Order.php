<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**  table order */
    protected $table = 'orders';
    protected $fillable = [
        'order_id',
        'ma_hang',
        'ps_code',
        'mau_vai',
        'mau_logo',
        'panel',
        'ngay_xuat',
        'po_number',
        'size',
        'sl_dat',
        'don_vi_tinh',
        'mua',
        'gia',
        'ten_hang',
        'ma_lenh',
        'tong_tien',
        'hang_ve',
        'da_giao',
        'con_lai',
        'ghi_chu'
    ];

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
