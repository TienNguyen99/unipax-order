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

    // 🟢 Xuất PDF cho 1 bản ghi (dùng template bcsx.xls hoặc phieunhapkho.xls cho QC)
    public function exportToPDF()
    {
        $log = NhapSXLog::findOrFail($this->id);

        // Kiểm tra nếu công đoạn là QC, dùng template khác (trim & case-insensitive)
        $cong_doan_trimmed = strtoupper(trim($log->cong_doan ?? ''));
        $isQC = $cong_doan_trimmed === 'QC';
        $templateFile = $isQC ? 'phieunhapkho.xls' : 'bcsx.xls';
        
        // Nạp template Excel
        $templatePath = storage_path('app/templates/' . $templateFile);
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Ghi dữ liệu vào mẫu
        if ($isQC) {
            // Format cho QC (phieunhapkho.xls)
            $this->fillQCTemplate($sheet, $log);
        } else {
            // Format cho SX bình thường (bcsx.xls)
            $this->fillSXTemplate($sheet, $log);
        }

        // Xuất ra file PDF (dùng so_phieu cho QC, dùng id cho normal)
        $identifier = $isQC ? $log->so_phieu : $this->id;
        $pdfPath = storage_path("app/public/BaoCaoSX_{$log->lenh_sx}_{$identifier}.pdf");
        
        // 🗑️ Xóa file cũ nếu tồn tại (force regenerate)
        if (file_exists($pdfPath)) {
            unlink($pdfPath);
        }
        
        $writer = new Mpdf($spreadsheet);
        $writer->save($pdfPath);

        // Thêm auto print
        $this->injectAutoPrintScript($pdfPath);
        return $pdfPath;
    }

    // 📋 Fill template cho SX bình thường (bcsx.xls)
    protected function fillSXTemplate($sheet, $log)
    {
        $sheet->setCellValue('B1', $log->created_at->format('d/m/Y'));
        $sheet->setCellValue('B2', $log->nhan_vien_id);
        $sheet->setCellValue('F2', $log->id);
        $sheet->setCellValue("B5", $log->lenh_sx);
        $sheet->setCellValue("B6", $log->lenhSanXuat->model_code ?? '');
        $sheet->setCellValue("B7", $log->lenhSanXuat->color ?? '');
        $sheet->setCellValue("F6", $log->lenhSanXuat->size ?? '');
        $sheet->setCellValue("F10", $log->lenhSanXuat->unit ?? '');
        $sheet->setCellValue("B3", $log->cong_doan);
        $sheet->setCellValue("B10", $log->so_luong_dat);
        $sheet->setCellValue("D10", $log->so_luong_loi);
        $sheet->setCellValue("B11", $log->dien_giai);
        $sheet->setCellValue("F7", $log->may_sx);
        $sheet->setCellValue("B9", $log->so_pick);
        $sheet->setCellValue("F9", $log->so_cuon);
        $sheet->setCellValue("D9", $log->so_dong);
        $sheet->setCellValue("B8", $log->so_ban);
        $sheet->setCellValue("F8", $log->khuon_sx);
    }

    // 📦 Fill template cho QC (phieunhapkho.xls) - Multiple rows
    protected function fillQCTemplate($sheet, $log)
    {
        // Lấy tất cả logs cùng so_phieu
        $allLogs = NhapSXLog::where('so_phieu', $log->so_phieu)
            ->orderBy('id')
            ->get();

        // Fill header info
        $sheet->setCellValue('J3', $log->so_phieu ?? 'QC-' . date('dmY'));
        $sheet->setCellValue('J33', $log->so_phieu ?? 'QC-' . date('dmY'));
        $sheet->setCellValue('I21', $log->created_at->format('d/m/Y'));
        $sheet->setCellValue('I51', $log->created_at->format('d/m/Y'));
        $sheet->setCellValue('F26', $log->nhan_vien_id);
        $sheet->setCellValue('F56', $log->nhan_vien_id);

        // Fill data rows (rows 6-15 cho 10 items)
        $startRow = 6;
        $startRowDuplicate = 36; // Bảng thứ 2 bắt đầu từ row 36
        foreach ($allLogs as $index => $item) {
            if ($index >= 15) break; // Max 15 rows
            
            $currentRow = $startRow + $index;
            $currentRowDuplicate = $startRowDuplicate + $index;
             // Điền cả bảng duplicate
            $stt = $index + 1;

            $sheet->setCellValue("A{$currentRow}", $stt);
            $sheet->setCellValue("A{$currentRowDuplicate}", $stt);
            $sheet->setCellValue("B{$currentRow}",$item->lenhSanXuat->description ?? $item->lenh_sx);
            $sheet->setCellValue("B{$currentRowDuplicate}",$item->lenhSanXuat->description ?? $item->lenh_sx);
            
            $sheet->setCellValue("D{$currentRow}", $item->lenhSanXuat->model_code ?? '');
            $sheet->setCellValue("D{$currentRowDuplicate}", $item->lenhSanXuat->model_code ?? '');
            $sheet->setCellValue("E{$currentRow}", $item->lenhSanXuat->color ?? '');
            $sheet->setCellValue("E{$currentRowDuplicate}", $item->lenhSanXuat->color ?? '');
            $sheet->setCellValue("F{$currentRow}", $item->lenhSanXuat->size ?? '');
            $sheet->setCellValue("F{$currentRowDuplicate}", $item->lenhSanXuat->size ?? '');
            $sheet->setCellValue("G{$currentRow}", $item->so_luong_dat);
            $sheet->setCellValue("G{$currentRowDuplicate}", $item->so_luong_dat);
            $sheet->setCellValue("H{$currentRow}", $item->lenhSanXuat->unit ?? 'PCS');
            $sheet->setCellValue("H{$currentRowDuplicate}", $item->lenhSanXuat->unit ?? 'PCS');
            $sheet->setCellValue("I{$currentRow}", $item->lenh_sx);
            $sheet->setCellValue("I{$currentRowDuplicate}", $item->lenh_sx);
            $sheet->setCellValue("J{$currentRow}", $item->dien_giai);
            $sheet->setCellValue("J{$currentRowDuplicate}", $item->dien_giai);
        }
    }

    // 🧠 Thêm script in tự động vào file PDF
    protected function injectAutoPrintScript($pdfPath)
    {
        $pdfContent = file_get_contents($pdfPath);
        $pdfContent .= "\n<script>window.print();</script>";
        file_put_contents($pdfPath, $pdfContent);
    }
}
