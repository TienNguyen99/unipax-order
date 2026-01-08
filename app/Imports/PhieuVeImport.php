<?php
namespace App\Imports;

use App\Models\PhieuVe;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Throwable;

class PhieuVeImport implements ToModel, WithHeadingRow, SkipsOnError
{
    use SkipsErrors;

    private $rowNumber = 1;
    private $importedRows = 0;
    private $failedRows = [];

    public function model(array $row)
    {
        $this->rowNumber++;
        
        try {
            // Helper function để convert Excel serial number thành date
            $convertExcelDate = function($value) {
                if (empty($value)) return '';
                
                // Kiểm tra nếu là số (Excel serial)
                if (is_numeric($value)) {
                    $value = (int)$value;
                    // Excel serial: 46053 = 2026-01-07, etc
                    if ($value > 30000 && $value < 100000) {
                        try {
                            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
                            return $date->format('Y-m-d');
                        } catch (\Exception $e) {
                            return '';
                        }
                    }
                }
                
                // Nếu là text, return nguyên
                return trim($value);
            };

            $phieuVe = new PhieuVe([
                'export_date' => $convertExcelDate($row['export_date'] ?? ''),
                'phieu_ps' => trim($row['phieu_ps'] ?? ''),
                'kich_thuoc' => trim($row['kich_thuoc'] ?? ''),
                'mau_vai' => trim($row['mau_vai'] ?? ''),
                'mau_logo' => trim($row['mau_logo'] ?? ''),
                'ngay_nhan_panel' => $convertExcelDate($row['ngay_nhan_panel'] ?? ''),
                'so_phieu' => trim($row['so_phieu'] ?? ''),
                'ma_hang' => trim($row['ma_hang'] ?? ''),
                'front_dat' => trim($row['front_dat'] ?? ''),
                'front_loi' => trim($row['front_loi'] ?? ''),
                'back_dat' => trim($row['back_dat'] ?? ''),
                'back_loi' => trim($row['back_loi'] ?? ''),
                'vi_tri' => trim($row['vi_tri'] ?? ''),
                'ngay' => $convertExcelDate($row['ngay'] ?? ''),
                'ghi_chu' => trim($row['ghi_chu'] ?? ''),
                'so_luong_nhan' => trim($row['so_luong_nhan'] ?? ''),
                'noi_giao' => trim($row['noi_giao'] ?? ''),
                'ngay_xuat_kho' => $convertExcelDate($row['ngay_xuat_kho'] ?? ''),
                'so_luong_donhang' => trim($row['so_luong_donhang'] ?? ''),
                'ngay_nhan' => now(),
            ]);
            
            // Cố gắng save, nếu fail thì log
            try {
                $phieuVe->save();
                $this->importedRows++;
            } catch (\Exception $saveError) {
                $this->failedRows[] = [
                    'row_number' => $this->rowNumber,
                    'so_phieu' => trim($row['so_phieu'] ?? ''),
                    'ma_hang' => trim($row['ma_hang'] ?? ''),
                    'vi_tri' => trim($row['vi_tri'] ?? ''),
                    'error' => $saveError->getMessage()
                ];
                \Log::warning("PhieuVe save error at row {$this->rowNumber}: " . $saveError->getMessage());
            }
            
            return null; // Return null để não double insert
            
        } catch (\Exception $e) {
            $this->failedRows[] = [
                'row_number' => $this->rowNumber,
                'error' => $e->getMessage()
            ];
            
            \Log::warning("PhieuVe import error at row {$this->rowNumber}: " . $e->getMessage());
            return null;
        }
    }

    public function onError(Throwable $error)
    {
        // Log lỗi
        \Log::error("PhieuVe import error: " . $error->getMessage());
    }

    public function getImportStats()
    {
        return [
            'imported_rows' => $this->importedRows,
            'failed_rows' => count($this->failedRows),
            'failed_details' => $this->failedRows
        ];
    }
}
