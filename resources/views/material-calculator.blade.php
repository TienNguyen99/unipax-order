<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tính Toán Vật Tư Vải</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 30px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border-radius: 8px;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .alert-success {
            border: 2px solid #28a745;
        }

        .card.border-success {
            border-width: 2px !important;
        }

        h5 {
            font-weight: 700;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">🧮 Tính Toán Vật Tư Vải</h4>
                    </div>
                    <div class="card-body">
                        <form id="materialCalculatorForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="fabricWidth" class="form-label">Khổ vải (mm) *</label>
                                    <input type="number" class="form-control" id="fabricWidth" name="fabric_width_mm"
                                        placeholder="VD: 1280" required>
                                    <small class="form-text text-muted">Chiều ngang của cuộn vải</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="pieceWidth" class="form-label">Chiều rộng cắt (mm) *</label>
                                    <input type="number" class="form-control" id="pieceWidth" name="piece_width_mm"
                                        placeholder="VD: 200" required>
                                    <small class="form-text text-muted">Chiều ngang mỗi mẩu cắt</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pieceLength" class="form-label">Chiều dài cắt (mm) *</label>
                                    <input type="number" class="form-control" id="pieceLength" name="piece_length_mm"
                                        placeholder="VD: 210" required>
                                    <small class="form-text text-muted">Chiều dài mỗi mẩu cắt</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="piecesPerSet" class="form-label">Số pcs/set *</label>
                                    <input type="number" class="form-control" id="piecesPerSet" name="pieces_per_set"
                                        placeholder="VD: 20" required>
                                    <small class="form-text text-muted">Số lượng mẩu trong 1 set</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="orderQuantity" class="form-label">Số lượng đơn hàng (pcs) *</label>
                                    <input type="number" class="form-control" id="orderQuantity" name="order_quantity"
                                        placeholder="VD: 10000" required>
                                    <small class="form-text text-muted">Tổng số mẩu cần sản xuất</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="wastePercentage" class="form-label">Hao hụt (%) - Tùy chọn</label>
                                    <input type="number" step="0.1" class="form-control" id="wastePercentage"
                                        name="waste_percentage" placeholder="VD: 5" value="5">
                                    <small class="form-text text-muted">Mặc định: 5% (cắt, lỗi, canh hoa)</small>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    📊 Tính Toán
                                </button>
                                <button type="reset" class="btn btn-secondary btn-lg ms-2">
                                    🔄 Xóa
                                </button>
                            </div>
                        </form>

                        <div id="resultContainer" style="display:none;" class="mt-5">
                            <div class="alert alert-success" role="alert">
                                <h5 class="alert-heading">✅ Kết Quả Tính Toán</h5>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">📋 Thông Tin Nhập</h6>
                                                <ul class="list-unstyled small">
                                                    <li><strong>Khổ vải:</strong> <span id="inputFabricWidth"></span> mm
                                                    </li>
                                                    <li><strong>Kích thước cắt:</strong> <span
                                                            id="inputPieceSize"></span> mm</li>
                                                    <li><strong>Pcs/set:</strong> <span id="inputPiecesPerSet"></span>
                                                    </li>
                                                    <li><strong>Đơn hàng:</strong> <span id="inputOrderQty"></span> pcs
                                                    </li>
                                                    <li><strong>Hao hụt:</strong> <span id="inputWaste"></span>%</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <h6 class="card-title">🔢 Các Bước Tính</h6>
                                                <ul class="list-unstyled small">
                                                    <li><strong>Số set cần:</strong> <span id="calcSets"></span> set
                                                    </li>
                                                    <li><strong>Diện tích/mẩu:</strong> <span
                                                            id="calcPieceArea"></span> mm²</li>
                                                    <li><strong>Tổng diện tích:</strong> <span
                                                            id="calcTotalArea"></span> mm²</li>
                                                    <li><strong>Chiều dài cần (cơ bản):</strong> <span
                                                            id="calcBasicLength"></span> mm</li>
                                                    <li><strong>= <span id="calcBasicLengthM"></span> mét</strong></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card border-success">
                                            <div class="card-header bg-success text-white">
                                                <h6 class="mb-0">📌 Kết Quả Xuất Vật Tư (Đã Tính Hao Hụt)</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h5 class="text-danger">
                                                            <span id="resultLengthM"></span> mét
                                                        </h5>
                                                        <p class="text-muted mb-0">Chiều dài vải cần xuất</p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h5 class="text-info">
                                                            <span id="resultSpec"></span>
                                                        </h5>
                                                        <p class="text-muted mb-0">Quy cách vải</p>
                                                    </div>
                                                </div>
                                                <div class="row mt-3">
                                                    <div class="col-12">
                                                        <small class="text-muted">
                                                            <strong>Chi tiết:</strong>
                                                            Không tính hao hụt: <span id="resultWithoutWaste"></span> m
                                                            |
                                                            Tính hao hụt: <span id="resultWithWaste"></span> m
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card border-info">
                                            <div class="card-header bg-info text-white">
                                                <h6 class="mb-0">📐 Định Mức Vải (Norm)</h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <h6>Không tính hao hụt</h6>
                                                        <ul class="list-unstyled small">
                                                            <li><strong><span id="normWithoutWasteMmPcs"></span>
                                                                    mm/pcs</strong></li>
                                                            <li><strong><span id="normWithoutWasteMPcs"></span>
                                                                    m/pcs</strong></li>
                                                        </ul>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Tính hao hụt</h6>
                                                        <ul class="list-unstyled small">
                                                            <li><strong class="text-danger"><span
                                                                        id="normWithWasteMmPcs"></span> mm/pcs</strong>
                                                            </li>
                                                            <li><strong class="text-danger"><span
                                                                        id="normWithWasteMPcs"></span> m/pcs</strong>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info mt-3 small">
                                    <strong>📝 Ghi chú:</strong> Kết quả đã bao gồm hao hụt cắt, lỗi biên và canh hoa.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('materialCalculatorForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = {
                fabric_width_mm: parseFloat(formData.get('fabric_width_mm')),
                piece_width_mm: parseFloat(formData.get('piece_width_mm')),
                piece_length_mm: parseFloat(formData.get('piece_length_mm')),
                pieces_per_set: parseFloat(formData.get('pieces_per_set')),
                order_quantity: parseFloat(formData.get('order_quantity')),
                waste_percentage: parseFloat(formData.get('waste_percentage')) || 5
            };

            try {
                const response = await fetch('/api/material-calculator/fabric-requirement', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    // Điền thông tin nhập
                    document.getElementById('inputFabricWidth').textContent = result.input.fabric_width_mm
                        .toLocaleString('vi-VN');
                    document.getElementById('inputPieceSize').textContent =
                        `${result.input.piece_width_mm} × ${result.input.piece_length_mm}`;
                    document.getElementById('inputPiecesPerSet').textContent = result.input.pieces_per_set;
                    document.getElementById('inputOrderQty').textContent = result.input.order_quantity
                        .toLocaleString('vi-VN');
                    document.getElementById('inputWaste').textContent = result.input.waste_percentage;

                    // Điền các bước tính
                    document.getElementById('calcSets').textContent = result.calculation.sets_needed
                        .toLocaleString('vi-VN');
                    document.getElementById('calcPieceArea').textContent = result.calculation.piece_area_mm2
                        .toLocaleString('vi-VN');
                    document.getElementById('calcTotalArea').textContent = result.calculation.total_area_mm2
                        .toLocaleString('vi-VN');
                    document.getElementById('calcBasicLength').textContent = result.calculation
                        .required_length_mm.toLocaleString('vi-VN');
                    document.getElementById('calcBasicLengthM').textContent = result.calculation
                        .required_length_meters.toLocaleString('vi-VN');

                    // Điền kết quả
                    document.getElementById('resultWithoutWaste').textContent = result.result.with_waste_meters;
                    document.getElementById('resultWithWaste').textContent = result.result.with_waste_meters;
                    document.getElementById('resultLengthM').textContent = result.recommendation
                        .export_length_meters.toLocaleString('vi-VN');
                    document.getElementById('resultSpec').textContent = result.recommendation.fabric_spec;

                    // Điền định mức (norm)
                    document.getElementById('normWithoutWasteMmPcs').textContent = result.norm
                        .without_waste_mm_per_pcs.toLocaleString('vi-VN');
                    document.getElementById('normWithoutWasteMPcs').textContent = result.norm
                        .without_waste_m_per_pcs.toLocaleString('vi-VN');
                    document.getElementById('normWithWasteMmPcs').textContent = result.norm
                        .with_waste_mm_per_pcs.toLocaleString('vi-VN');
                    document.getElementById('normWithWasteMPcs').textContent = result.norm.with_waste_m_per_pcs
                        .toLocaleString('vi-VN');

                    // Hiển thị kết quả
                    document.getElementById('resultContainer').style.display = 'block';
                    document.querySelector('html').scrollTop = document.querySelector('#resultContainer')
                        .offsetTop - 100;
                } else {
                    alert('Lỗi tính toán. Vui lòng kiểm tra lại dữ liệu.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Lỗi kết nối. Vui lòng thử lại.');
            }
        });
    </script>

    <style>
        .card {
            border-radius: 8px;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-lg {
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
        }

        .alert-success {
            border: 2px solid #28a745;
        }

        .card.border-success {
            border-width: 2px !important;
        }

        h5 {
            font-weight: 700;
        }
    </style>
</body>

</html>
