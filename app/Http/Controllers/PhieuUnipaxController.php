<?php
namespace App\Http\Controllers;

use App\Models\PhieuNhap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhieuUnipaxController extends Controller
{
    public function index()
    {
        $psList = PhieuNhap::select('ps')->distinct()->pluck('ps');
        return view('client.toolunipax', compact('psList'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'ps' => 'required|string',
            'row_kd' => 'nullable|integer',
            'dat' => 'required|integer|min:0',
            'loi' => 'required|integer|min:0',
            'ghichu' => 'nullable|string|max:255',
        ]);

        $data['nguoitao'] = Auth::user()->name ?? 'unknown';
        $data['ngaynhap'] = now();

        PhieuNhap::create($data);

        return back()->with('success', "✅ Đã lưu phiếu nhập cho P/S {$data['ps']}!");
    }

    public function viewAll()
    {
        return response()->json(PhieuNhap::orderByDesc('id')->get());
    }

    public function delete($id)
    {
        PhieuNhap::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }
}
