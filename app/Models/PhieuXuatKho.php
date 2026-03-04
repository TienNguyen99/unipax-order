<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuXuatKho extends Model
{
    use HasFactory;

    protected $table = 'phieu_xuat_kho';

    protected $fillable = [
        'ma_phieu',
        'user_id',
        'ngay_xuat',
        'trang_thai',
        'tong_so_items',
        'ghi_chu',
    ];

    protected $casts = [
        'ngay_xuat' => 'date',
    ];

    /**
     * Relationship: Phiếu xuất kho thuộc về user
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship: Phiếu xuất kho có nhiều chi tiết
     */
    public function chiTiet()
    {
        return $this->hasMany(PhieuXuatKhoChiTiet::class, 'phieu_xuat_kho_id');
    }

    /**
     * Relationship: Phiếu xuất kho có nhiều phiếu về (thông qua chi tiết)
     */
    public function phieuVe()
    {
        return $this->hasManyThrough(
            PhieuVe::class,
            PhieuXuatKhoChiTiet::class,
            'phieu_xuat_kho_id',
            'id',
            'id',
            'phieu_ve_id'
        );
    }

    /**
     * Generate mã phiếu tự động: PXK-YYYYMMDD-XXXX
     */
    public static function generateMaPhieu()
    {
        $today = now()->format('Ymd');
        $prefix = "PXK-{$today}-";
        
        // Lấy phiếu cuối cùng trong ngày
        $lastPhieu = self::where('ma_phieu', 'like', $prefix . '%')
            ->orderBy('ma_phieu', 'desc')
            ->first();
        
        if ($lastPhieu) {
            // Lấy số thứ tự cuối và tăng lên
            $lastNumber = intval(substr($lastPhieu->ma_phieu, -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Scope: Lọc theo trạng thái
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('trang_thai', $status);
    }

    /**
     * Scope: Lọc theo ngày
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('ngay_xuat', $date);
    }

    /**
     * Scope: Lọc theo user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
