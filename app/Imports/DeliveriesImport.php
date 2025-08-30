<?php
namespace App\Imports;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Delivery;




class DeliveriesImport implements ToModel, WithHeadingRow, WithCalculatedFormulas
{
    public function calculatedFormulas(): bool
    {
        return true; // luôn lấy kết quả tính toán của công thức
    }

    public function model(array $row)
    {
return Delivery::create([
'ngay_xuat' => (isset($row['export_date']) && is_numeric($row['export_date']))
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['export_date'])
                : null,

'ngay_gui_panel' => (isset($row['date_out_panel']) && is_numeric($row['date_out_panel']))
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date_out_panel'])
                : null,

'ngay_giao' => (isset($row['delivery_date']) && is_numeric($row['delivery_date']))
                ? \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['delivery_date'])
                : null,
    'ma_hang'       => $row['ma_hang'] ?? null,
    'ps_code'       => $row['ps_sub'] ?? null,
    'size'          => $row['size'] ?? null,
    'mau_vai'       => $row['fabric_color'] ?? null,
    'mau_logo'      => $row['logo_color'] ?? null,

    'so_phieu'      => $row['so_phieu'] ?? null,
    'sl_dat'        => $row['quantity_order'] ?? null,
    'sl_thuc_nhan'  => $row['quantity_front'] ?? null,

    'sl_giao_dat'   => $row['dat'] ?? null,            // đúng tên DB
    'sl_giao_loi'   => $row['loi'] ?? null,            // đúng tên DB
    'ghi_chu'       => $row['ghi_chu'] ?? null,
    'mat'           => $row['mat'] ?? null,
    'thang_chot'    => $row['thang_chot'] ?? null,
    'noi_giao'      => $row['noi_giao'] ?? null,
]);

    }
}
