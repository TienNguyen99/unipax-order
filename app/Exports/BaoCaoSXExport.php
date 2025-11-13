<?php
namespace App\Exports;

use App\Models\NhapSXLog;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;

class BaoCaoSXExport
{
    protected $ngay;
    protected $id;

    public function __construct($ngay = null, $id = null)
    {
        $this->ngay = $ngay;
        $this->id = $id;
    }

    // 🟢 Xuất PDF cho 1 bản ghi (dùng template bcsx.xls)
    public function exportToPDF()
    {
        $log = NhapSXLog::findOrFail($this->id);

        // Nạp template Excel
        $templatePath = storage_path('app/templates/bcsx.xls');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Ghi dữ liệu vào mẫu
        $sheet->setCellValue('B1', now()->format('d/m/Y'));
        $sheet->setCellValue('B2', ''); // Tên nhân viên
        $sheet->setCellValue('B3', ''); // Công việc

            $sheet->setCellValue("B5", $log->lenh_sx);
            $sheet->setCellValue("B6", $log->lenhSanXuat->model_code ?? '');
            $sheet->setCellValue("B7", $log->lenhSanXuat->color ?? '');
            $sheet->setCellValue("F6", $log->lenhSanXuat->size ?? '');
            $sheet->setCellValue("F10", $log->lenhSanXuat->unit ?? '');

            $sheet->setCellValue("B3", $log->cong_doan);
            $sheet->setCellValue("B10", $log->so_luong_dat);
            $sheet->setCellValue("D10", $log->so_luong_loi);
            $sheet->setCellValue("B11", $log->dien_giai);

        // Xuất ra file PDF
        $pdfPath = storage_path("app/public/BaoCaoSX_ID{$this->id}.pdf");
        $writer = new Mpdf($spreadsheet);
        $writer->save($pdfPath);

        // Thêm auto print
        $this->injectAutoPrintScript($pdfPath);

        return $pdfPath;
    }

    // 🧠 Thêm script in tự động vào file PDF
    protected function injectAutoPrintScript($pdfPath)
    {
        $pdfContent = file_get_contents($pdfPath);
        $pdfContent .= "\n<script>window.print();</script>";
        file_put_contents($pdfPath, $pdfContent);
    }
}
