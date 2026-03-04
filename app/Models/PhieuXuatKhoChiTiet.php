<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuXuatKhoChiTiet extends Model
{
    use HasFactory;

    protected $table = 'phieu_xuat_kho_chi_tiet';

    protected $fillable = [
        'phieu_xuat_kho_id',
        'phieu_ve_id',
        'phieu_ps',
        'ma_hang',
        'ma_lenh',
        'kich_thuoc',
        'vi_tri',
        'so_luong_donhang',
        'so_luong_nhan',
        'makhac_dat',
        'makhac_loi',
        'front_dat',
        'front_loi',
        'back_dat',
        'back_loi',
        'ghi_chu',
    ];

    /**
     * Relationship: Chi tiết thuộc về phiếu xuất kho
     */
    public function phieuXuatKho()
    {
        return $this->belongsTo(PhieuXuatKho::class, 'phieu_xuat_kho_id');
    }

    /**
     * Relationship: Chi tiết thuộc về phiếu về
     */
    public function phieuVe()
    {
        return $this->belongsTo(PhieuVe::class, 'phieu_ve_id');
    }

    /**
     * Tạo chi tiết từ dữ liệu phiếu về
     */
    public static function createFromPhieuVe($phieuXuatKhoId, PhieuVe $phieuVe, array $inputData = [])
    {
        return self::create([
            'phieu_xuat_kho_id' => $phieuXuatKhoId,
            'phieu_ve_id' => $phieuVe->id,
            // Snapshot data
            'phieu_ps' => $phieuVe->phieu_ps,
            'ma_hang' => $phieuVe->ma_hang,
            'ma_lenh' => $phieuVe->ma_lenh,
            'kich_thuoc' => $phieuVe->kich_thuoc,
            'vi_tri' => $phieuVe->vi_tri,
            'so_luong_donhang' => $phieuVe->so_luong_donhang,
            'so_luong_nhan' => $phieuVe->so_luong_nhan,
            // Input data (đã nhập)
            'makhac_dat' => $inputData['makhac_dat'] ?? null,
            'makhac_loi' => $inputData['makhac_loi'] ?? null,
            'front_dat' => $inputData['front_dat'] ?? null,
            'front_loi' => $inputData['front_loi'] ?? null,
            'back_dat' => $inputData['back_dat'] ?? null,
            'back_loi' => $inputData['back_loi'] ?? null,
            'ghi_chu' => $inputData['ghi_chu'] ?? null,
        ]);
    }
}
