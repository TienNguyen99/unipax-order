<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialCalculatorController extends Controller
{
    /**
     * Calculate material requirements for fabric cutting
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateFabricRequirement(Request $request)
    {
        $validated = $request->validate([
            'fabric_width_mm' => 'required|numeric|min:1', // Khổ vải (mm)
            'piece_width_mm' => 'required|numeric|min:1',  // Chiều rộng cắt (mm)
            'piece_length_mm' => 'required|numeric|min:1', // Chiều dài cắt (mm)
            'pieces_per_set' => 'required|numeric|min:1',  // Số pcs/set
            'order_quantity' => 'required|numeric|min:1',  // Số lượng đơn hàng
            'waste_percentage' => 'nullable|numeric|min:0|max:100', // Hao hụt (%) - mặc định 5%
        ]);

        $fabricWidth = $validated['fabric_width_mm'];
        $pieceWidth = $validated['piece_width_mm'];
        $pieceLength = $validated['piece_length_mm'];
        $piecesPerSet = $validated['pieces_per_set'];
        $orderQuantity = $validated['order_quantity'];
        $wastePercentage = $validated['waste_percentage'] ?? 5; // Mặc định 5% hao hụt

        // Bước 1: Tính số set cần sản xuất
        $setsNeeded = ceil($orderQuantity / $piecesPerSet);

        // Bước 2: Tính tổng diện tích vật tư (mm²)
        $pieceArea = $pieceWidth * $pieceLength;
        $totalArea = $pieceArea * $setsNeeded;

        // Bước 3: Tính chiều dài vải cần theo khổ (mm)
        $requiredLength = $totalArea / $fabricWidth;

        // Bước 4: Cộng hao hụt
        $lengthWithWaste = $requiredLength * (1 + $wastePercentage / 100);

        // Chuyển đổi mm sang meter
        $requiredLengthMeters = $requiredLength / 1000;
        $lengthWithWasteMeters = $lengthWithWaste / 1000;

        // Bước 5: Tính định mức (mm/pcs và m/pcs)
        $normPerPieceWithWasteMm = $lengthWithWaste / $orderQuantity;
        $normPerPieceWithWasteM = $lengthWithWasteMeters / $orderQuantity;
        $normPerPieceWithoutWasteMm = $requiredLength / $orderQuantity;
        $normPerPieceWithoutWasteM = $requiredLengthMeters / $orderQuantity;

        return response()->json([
            'success' => true,
            'input' => [
                'fabric_width_mm' => $fabricWidth,
                'piece_width_mm' => $pieceWidth,
                'piece_length_mm' => $pieceLength,
                'pieces_per_set' => $piecesPerSet,
                'order_quantity' => $orderQuantity,
                'waste_percentage' => $wastePercentage,
            ],
            'calculation' => [
                'sets_needed' => $setsNeeded,
                'piece_area_mm2' => $pieceArea,
                'total_area_mm2' => $totalArea,
                'required_length_mm' => round($requiredLength, 2),
                'required_length_meters' => round($requiredLengthMeters, 2),
            ],
            'result' => [
                'without_waste_mm' => round($requiredLength, 2),
                'without_waste_meters' => round($requiredLengthMeters, 2),
                'with_waste_mm' => round($lengthWithWaste, 2),
                'with_waste_meters' => round($lengthWithWasteMeters, 2),
                'waste_percentage' => $wastePercentage,
            ],
            'norm' => [
                'without_waste_mm_per_pcs' => round($normPerPieceWithoutWasteMm, 4),
                'without_waste_m_per_pcs' => round($normPerPieceWithoutWasteM, 6),
                'with_waste_mm_per_pcs' => round($normPerPieceWithWasteMm, 4),
                'with_waste_m_per_pcs' => round($normPerPieceWithWasteM, 6),
            ],
            'recommendation' => [
                'export_length_mm' => round($lengthWithWaste + 100, 0), // Làm tròn + 100mm buffer
                'export_length_meters' => round(($lengthWithWaste + 100) / 1000, 2),
                'fabric_spec' => sprintf('%d mm (khổ) × %d mm (dài)', 
                    $fabricWidth, 
                    round($lengthWithWaste + 100, 0)
                )
            ]
        ]);
    }

    /**
     * Get calculation form/view
     * 
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return view('material-calculator');
    }
}
