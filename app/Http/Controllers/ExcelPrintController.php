<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\PrintLog;
use Carbon\Carbon;

class ExcelPrintController extends Controller
{
    // Đường dẫn Python (chỉnh lại theo máy bạn)
    private $pythonPath = 'C:\Users\ADMIN\AppData\Local\Programs\Python\Python313\python.exe';

    // Đường dẫn file script
    private $listScript;
    private $printScript;

    public function __construct()
    {
        $this->listScript = base_path('scripts/listsheets.py');
        $this->printScript = base_path('scripts/print_excel.py');
    }

    public function index()
    {
        $sheets = [];
        $output = [];
        $returnCode = 0;

        exec("{$this->pythonPath} " . escapeshellarg($this->listScript), $output, $returnCode);

        if ($returnCode === 0) {
            $sheets = $output;
        } else {
            $sheets = [];
            $errorMsg = implode("\n", $output);

            return view('excel', [
                'sheets' => $sheets,
                'error' => "Lỗi khi đọc danh sách sheet: " . $errorMsg,
                'fileUrls' => $this->getFileList()
            ]);
        }

        return view('excel', [
            'sheets' => $sheets,
            'fileUrls' => $this->getFileList()
        ]);
    }

    public function print(Request $request)
    {
        $sheet = $request->input('sheet');
        $output = [];
        $returnCode = 0;

        $cmd = "{$this->pythonPath} " . escapeshellarg($this->printScript) . " " . escapeshellarg($sheet);
        exec($cmd . " 2>&1", $output, $returnCode);  // Thêm 2>&1 để capture cả stderr

        // Debug: Log output
        \Log::info("Print Command: " . $cmd);
        \Log::info("Return Code: " . $returnCode);
        \Log::info("Output: " . print_r($output, true));

        if ($returnCode === 0) {
            $pdfPath = null;
            $successMsg = null;
            $warningMsg = null;
            $errorMsg = null;

            foreach ($output as $line) {
                if (strpos($line, "PREVIEW::") === 0) {
                    $pdfPath = trim(str_replace("PREVIEW::", "", $line));
                    // Normalize path cho Windows
                    $pdfPath = str_replace('/', DIRECTORY_SEPARATOR, $pdfPath);
                }
                if (strpos($line, "SUCCESS::") === 0) {
                    $successMsg = trim(str_replace("SUCCESS::", "", $line));
                }
                // Xử lý cả WARNING
                if (strpos($line, "WARNING::") === 0) {
                    $warningMsg = trim(str_replace("WARNING::", "", $line));
                }
                // Xử lý ERROR
                if (strpos($line, "ERROR::") === 0) {
                    $errorMsg = trim(str_replace("ERROR::", "", $line));
                }
            }

            // Kiểm tra error message trước
            if ($errorMsg) {
                return back()->with('error', $errorMsg);
            }

            if ($pdfPath && file_exists($pdfPath)) {
                // Lưu vào database
                PrintLog::create([
                    'sheet_name' => $sheet,
                    'printed_by' => auth()->user()->name ?? 'Không',
                    'printed_at' => Carbon::now(),
                    'pdf_path' => $pdfPath,
                ]);

                $relativePath = str_replace(public_path(), '', $pdfPath);
                return view('excel', [
                    'sheets' => [],
                    'preview' => $relativePath,
                    'success' => $successMsg,
                    'fileUrls' => $this->getFileList()
                ]);
            }

            // Debug: Hiển thị thông tin nếu không tìm thấy PDF
            $debugInfo = "PDF Path: " . ($pdfPath ?? 'NULL') . "\n";
            $debugInfo .= "File exists: " . ($pdfPath ? (file_exists($pdfPath) ? 'YES' : 'NO') : 'N/A') . "\n";
            $debugInfo .= "Success msg: " . ($successMsg ?? 'NULL') . "\n";
            $debugInfo .= "Full output: " . implode("\n", $output);
            
            return back()->with('error', "Không tạo được preview sau khi in.\n\nDebug:\n" . $debugInfo);
        } else {
            // Parse error message từ Python output
            $errorMsg = null;
            foreach ($output as $line) {
                if (strpos($line, "ERROR::") === 0) {
                    $errorMsg = trim(str_replace("ERROR::", "", $line));
                    break;
                }
            }
            
            if ($errorMsg) {
                return back()->with('error', $errorMsg);
            }
            
            return back()->with('error', "Lỗi khi in:\n" . implode("\n", $output));
        }
    }

    /**
     * Hiển thị list duyệt cho sếp
     */
    public function approvalList()
    {
        $logs = PrintLog::orderBy('printed_at', 'desc')->get();
        return view('print-approval', ['logs' => $logs]);
    }

    /**
     * Sếp duyệt & ký
     */
    public function approve(Request $request, $id)
    {
        $log = PrintLog::findOrFail($id);
        
        $log->update([
            'is_approved' => true,
            'approved_by' => auth()->user()->name ?? 'Sếp',
            'signature' => $request->input('signature', 'Đã ký duyệt'),
            'approved_at' => Carbon::now(),
        ]);

        return back()->with('success', "Đã duyệt lệnh {$log->sheet_name}");
    }

    /**
     * Xóa lệnh
     */
    public function deleteLog($id)
    {
        $log = PrintLog::findOrFail($id);
        $sheetName = $log->sheet_name;
        
        // Xóa file PDF nếu tồn tại
        if ($log->pdf_path && file_exists($log->pdf_path)) {
            unlink($log->pdf_path);
        }
        
        $log->delete();
        
        return back()->with('success', "Đã xóa lệnh {$sheetName}");
    }

    /**
     * Test route để debug
     */
    public function test($sheet = "1")
    {
        $output = [];
        $returnCode = 0;

        $cmd = "{$this->pythonPath} " . escapeshellarg($this->printScript) . " " . escapeshellarg($sheet);
        exec($cmd . " 2>&1", $output, $returnCode);

        return response()->json([
            'command' => $cmd,
            'return_code' => $returnCode,
            'output' => $output,
            'python_path' => $this->pythonPath,
            'script_path' => $this->printScript,
            'base_path' => base_path(),
            'public_path' => public_path(),
        ], 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Lấy danh sách file trong storage/app/public/prints
     */
    private function getFileList()
    {
        $files = Storage::files('public');

        return collect($files)->map(function ($file) {
            return [
                'name' => basename($file),
                'url'  => Storage::url($file)
            ];
        });
    }
}
