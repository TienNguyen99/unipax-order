<?php

namespace App\Exports;

use App\Models\PhieuVe;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PhieuVeExport implements FromQuery, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct($ids)
    {
        $this->ids = $ids;
    }

    public function map($row): array
    {
        return [
            $this->formatDate($row->export_date),
            $row->ma_hang,
            $row->phieu_ps,
            $row->kich_thuoc,
            $row->mau_vai,
            $row->mau_logo,
            $this->formatDate($row->ngay_nhan_panel),
            $row->so_phieu,
            $row->so_luong_donhang,
            $row->so_luong_nhan,
            $this->formatDate($row->ngay_xuat_kho),
            $row->makhac_dat,
            $row->makhac_loi,
            $row->front_dat,
            $row->front_loi,
            $row->back_dat,
            $row->back_loi,
            $row->ghi_chu,
            $row->vi_tri,
            $row->thang_chot,
            $row->noi_giao,
            $row->gia_cong,
            $row->ma_lenh
        ];
    }

    private function formatDate($date)
    {
        if (empty($date)) return '';
        try {
            // Nếu đã là định dạng d/m/Y thì giữ nguyên, nếu là Y-m-d thì chuyển sang d/m/Y
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return \Carbon\Carbon::parse($date)->format('d/m/Y');
            }
            return $date;
        } catch (\Exception $e) {
            return $date;
        }
    }

    public function query()
    {
        return PhieuVe::whereIn('id', $this->ids)
            ->select(
                'export_date',
                'ma_hang',
                'phieu_ps',
                'kich_thuoc',
                'mau_vai',
                'mau_logo',
                'ngay_nhan_panel',
                'so_phieu',
                'so_luong_donhang',
                'so_luong_nhan',
                'ngay_xuat_kho',
                'makhac_dat',
                'makhac_loi',
                'front_dat',
                'front_loi',
                'back_dat',
                'back_loi',
                'ghi_chu',
                'vi_tri',
                'thang_chot',
                'noi_giao',
                'gia_cong',
                'ma_lenh'
            );
    }

    public function headings(): array
    {
        return [
            'export_date',
            'ma_hang',
            'phieu_ps',
            'kich_thuoc',
            'mau_vai',
            'mau_logo',
            'ngay_nhan_panel',
            'so_phieu',
            'so_luong_donhang',
            'so_luong_nhan',
            'ngay_xuat_kho',
            'makhac_dat',
            'makhac_loi',
            'front_dat',
            'front_loi',
            'back_dat',
            'back_loi',
            'ghi_chu',
            'vi_tri',
            'thang_chot',
            'noi_giao',
            'gia_cong',
            'ma_lenh'
        ];
    }
}
