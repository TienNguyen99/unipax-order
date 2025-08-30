<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        exec($cmd, $output, $returnCode);

        if ($returnCode === 0) {
            $pdfPath = null;
            $successMsg = null;

            foreach ($output as $line) {
                if (strpos($line, "PREVIEW::") === 0) {
                    $pdfPath = trim(str_replace("PREVIEW::", "", $line));
                }
                if (strpos($line, "SUCCESS::") === 0) {
                    $successMsg = trim(str_replace("SUCCESS::", "", $line));
                }
            }

            if ($pdfPath && file_exists($pdfPath)) {
                $relativePath = str_replace(public_path(), '', $pdfPath);
                return view('excel', [
                    'sheets' => [],
                    'preview' => $relativePath,
                    'success' => $successMsg,
                    'fileUrls' => $this->getFileList()
                ]);
            }

            return back()->with('error', "Không tạo được preview sau khi in");
        } else {
            return back()->with('error', "Lỗi khi in: " . implode("\n", $output));
        }
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
