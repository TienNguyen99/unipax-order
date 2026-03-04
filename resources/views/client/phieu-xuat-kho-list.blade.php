<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Danh Sách Phiếu Xuất Kho</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid mt-4 fade-in">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="mb-3 d-flex justify-content-between align-items-center animate-slide-down">
                    <h2 class="mb-0 text-gradient"><i class="fas fa-box-open me-2"></i>Danh Sách Phiếu Xuất Kho</h2>
                    <a href="{{ route('phieu-ve-entry.show') }}" class="btn btn-primary modern-btn">
                        <i class="fas fa-plus"></i> Tạo Phiếu Mới
                    </a>
                </div>

                <!-- Filter Section -->
                <div class="card mb-4 modern-card">
                    <div class="card-header bg-primary text-white modern-card-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Bộ Lọc</h5>
                    </div>
                    <div class="card-body modern-card-body">
                        <form method="GET" action="{{ route('phieu-xuat-kho.list') }}">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="ngay_xuat" class="form-label">Ngày Xuất:</label>
                                    <input type="date" class="form-control modern-input" id="ngay_xuat"
                                        name="ngay_xuat" value="{{ request('ngay_xuat') }}">
                                </div>
                                <div class="col-md-3">
                                    <label for="trang_thai" class="form-label">Trạng Thái:</label>
                                    <select class="form-select modern-input" id="trang_thai" name="trang_thai">
                                        <option value="">Tất cả</option>
                                        <option value="draft" {{ request('trang_thai') == 'draft' ? 'selected' : '' }}>
                                            Nháp</option>
                                        <option value="confirmed"
                                            {{ request('trang_thai') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận
                                        </option>
                                        <option value="completed"
                                            {{ request('trang_thai') == 'completed' ? 'selected' : '' }}>Hoàn thành
                                        </option>
                                        <option value="cancelled"
                                            {{ request('trang_thai') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary modern-btn">
                                            <i class="fas fa-search"></i> Tìm
                                        </button>
                                        <a href="{{ route('phieu-xuat-kho.list') }}"
                                            class="btn btn-secondary modern-btn">
                                            <i class="fas fa-redo"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Results Section -->
                <div class="card modern-card">
                    <div class="card-header bg-success text-white modern-card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Danh Sách - {{ $phieuXuatKhos->total() }} phiếu
                        </h5>
                    </div>
                    <div class="card-body modern-card-body">
                        @if ($phieuXuatKhos->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered modern-table">
                                    <thead>
                                        <tr>
                                            <th>Mã Phiếu</th>
                                            <th>Ngày Xuất</th>
                                            <th>Người Tạo</th>
                                            <th>Số Items</th>
                                            <th>Trạng Thái</th>
                                            <th>Ghi Chú</th>
                                            <th>Thao Tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($phieuXuatKhos as $phieu)
                                            <tr>
                                                <td><strong>{{ $phieu->ma_phieu }}</strong></td>
                                                <td>{{ \Carbon\Carbon::parse($phieu->ngay_xuat)->format('d/m/Y') }}
                                                </td>
                                                <td>{{ $phieu->user->name ?? '-' }}</td>
                                                <td><span class="badge bg-info">{{ $phieu->tong_so_items }} ô</span>
                                                </td>
                                                <td>
                                                    @if ($phieu->trang_thai == 'draft')
                                                        <span class="badge bg-secondary">Nháp</span>
                                                    @elseif($phieu->trang_thai == 'confirmed')
                                                        <span class="badge bg-primary">Đã xác nhận</span>
                                                    @elseif($phieu->trang_thai == 'completed')
                                                        <span class="badge bg-success">Hoàn thành</span>
                                                    @elseif($phieu->trang_thai == 'cancelled')
                                                        <span class="badge bg-danger">Đã hủy</span>
                                                    @endif
                                                </td>
                                                <td>{{ $phieu->ghi_chu ?? '-' }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('phieu-xuat-kho.view', $phieu->id) }}"
                                                            class="btn btn-info modern-btn" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('phieu-xuat-kho.print', $phieu->id) }}"
                                                            class="btn btn-success modern-btn" title="Xuất Excel">
                                                            <i class="fas fa-file-excel"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $phieuXuatKhos->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Chưa có phiếu xuất kho nào
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Modern animations and styles */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        .animate-slide-down {
            animation: slideDown 0.5s ease-out;
        }

        .modern-card {
            border-radius: 16px !important;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .modern-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .modern-card-header {
            border-radius: 16px 16px 0 0 !important;
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
        }

        .modern-card-body {
            padding: 1.5rem;
        }

        .modern-btn {
            border-radius: 12px !important;
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .modern-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }

        .modern-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .modern-input {
            border-radius: 12px !important;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            transform: translateY(-1px);
        }

        .modern-table {
            border-radius: 12px;
            overflow: hidden;
            border: none;
        }

        .modern-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .modern-table thead th {
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .modern-table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }

        .badge {
            padding: 6px 14px;
            font-weight: 500;
            border-radius: 8px !important;
            transition: all 0.3s ease;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
