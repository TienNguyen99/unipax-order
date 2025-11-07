<?php
namespace App\Exports;

use App\Models\NhapSXLog;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Storage;

class BaoCaoSXExport implements WithEvents
{
    protected $ngay;

    public function __construct($ngay)
    {
        $this->ngay = $ngay;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Mở template Excel gốc
                $templatePath = storage_path('app/templates/bcsx.xls');
                $spreadsheet = IOFactory::load($templatePath);
                $sheet = $spreadsheet->getActiveSheet();

                // Lấy dữ liệu từ DB
                $logs = NhapSXLog::whereDate('ngay_nhap', $this->ngay)->get();

                // Ghi thông tin chung
                $sheet->setCellValue('B1', now()->format('d/m/Y')); // Ngày sản xuất
                $sheet->setCellValue('B2', ''); // Tên nhân viên (có thể từ user đăng nhập)
                $sheet->setCellValue('B3', ''); // Công việc

                // Ghi dữ liệu sản xuất theo từng dòng
                $row = 9;
                foreach ($logs as $log) {
                    $sheet->setCellValue("B5", $log->lenh_sx);
                    $sheet->setCellValue("B3", $log->cong_doan);
                    $sheet->setCellValue("B10", $log->so_luong_dat);
                    $sheet->setCellValue("D10", $log->so_luong_loi);
                    $sheet->setCellValue("B11", $log->dien_giai);
                    // $row++;
                }

                // Ghi file ra tạm thời để download
                $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
                $outputPath = storage_path("app/public/BaoCaoSX_{$this->ngay}.xlsx");
                $writer->save($outputPath);

                return $outputPath;
            }
        ];
    }
}
