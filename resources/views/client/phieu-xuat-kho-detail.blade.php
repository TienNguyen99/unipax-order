<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi Tiết Phiếu Xuất Kho - {{ $phieuXuatKho->ma_phieu }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid mt-4 fade-in">
        <div class="row">
            <div class="col-md-12">
                <!-- Header -->
                <div class="mb-3 d-flex justify-content-between align-items-center animate-slide-down">
                    <h2 class="mb-0 text-gradient">
                        <i class="fas fa-file-alt me-2"></i>{{ $phieuXuatKho->ma_phieu }}
                    </h2>
                    <div>
                        <a href="{{ route('phieu-xuat-kho.list') }}" class="btn btn-secondary modern-btn">
                            <i class="fas fa-arrow-left"></i> Quay Lại
                        </a>
                        @if (in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed']))
                            <button class="btn btn-primary modern-btn" data-bs-toggle="modal"
                                data-bs-target="#addItemModal">
                                <i class="fas fa-plus"></i> Thêm Item
                            </button>
                        @endif
                        <a href="{{ route('phieu-xuat-kho.print', $phieuXuatKho->id) }}"
                            class="btn btn-success modern-btn">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="card mb-4 modern-card">
                    <div class="card-header bg-info text-white modern-card-header">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Thông Tin Phiếu</h5>
                    </div>
                    <div class="card-body modern-card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <label>Mã Phiếu:</label>
                                    <p><strong>{{ $phieuXuatKho->ma_phieu }}</strong></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <label>Ngày Xuất:</label>
                                    <p>{{ \Carbon\Carbon::parse($phieuXuatKho->ngay_xuat)->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <label>Người Tạo:</label>
                                    <p>{{ $phieuXuatKho->user->name ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <label>Trạng Thái:</label>
                                    <p>
                                        @if ($phieuXuatKho->trang_thai == 'draft')
                                            <span class="badge bg-secondary">Nháp</span>
                                        @elseif($phieuXuatKho->trang_thai == 'confirmed')
                                            <span class="badge bg-primary">Đã xác nhận</span>
                                        @elseif($phieuXuatKho->trang_thai == 'completed')
                                            <span class="badge bg-success">Hoàn thành</span>
                                        @elseif($phieuXuatKho->trang_thai == 'cancelled')
                                            <span class="badge bg-danger">Đã hủy</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <label>Tổng Số Items:</label>
                                    <p><span class="badge bg-info">{{ $phieuXuatKho->tong_so_items }} ô</span></p>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="info-box">
                                    <label>Ghi Chú:</label>
                                    <p>{{ $phieuXuatKho->ghi_chu ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details Section -->
                <div class="card modern-card">
                    <div class="card-header bg-success text-white modern-card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>Chi Tiết - {{ $phieuXuatKho->chiTiet->count() }} items
                        </h5>
                    </div>
                    <div class="card-body modern-card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered modern-table">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Phiếu PS</th>
                                        <th>Mã Hàng</th>
                                        <th>Mã Lệnh</th>
                                        <th>Kích Thước</th>
                                        <th>Vị Trí</th>
                                        <th>SL Đơn Hàng</th>
                                        <th>Mã Khác Đạt</th>
                                        <th>Mã Khác Lỗi</th>
                                        <th>Front Đạt</th>
                                        <th>Front Lỗi</th>
                                        <th>Back Đạt</th>
                                        <th>Back Lỗi</th>
                                        <th>Ghi Chú</th>
                                        @if (in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed']))
                                            <th width="100">Thao Tác</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($phieuXuatKho->chiTiet as $index => $item)
                                        <tr id="item-row-{{ $item->id }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td><strong>{{ $item->phieu_ps }}</strong></td>
                                            <td>{{ $item->ma_hang }}</td>
                                            <td>{{ $item->ma_lenh }}</td>
                                            <td>{{ $item->kich_thuoc }}</td>
                                            <td><span class="badge bg-info item-vi-tri">{{ $item->vi_tri }}</span></td>
                                            <td>{{ $item->so_luong_donhang }}</td>
                                            <td class="item-makhac-dat">{{ $item->makhac_dat ?? '-' }}</td>
                                            <td class="item-makhac-loi">{{ $item->makhac_loi ?? '-' }}</td>
                                            <td class="item-front-dat">{{ $item->front_dat ?? '-' }}</td>
                                            <td class="item-front-loi">{{ $item->front_loi ?? '-' }}</td>
                                            <td class="item-back-dat">{{ $item->back_dat ?? '-' }}</td>
                                            <td class="item-back-loi">{{ $item->back_loi ?? '-' }}</td>
                                            <td class="item-ghi-chu">{{ $item->ghi_chu ?? '-' }}</td>
                                            @if (in_array($phieuXuatKho->trang_thai, ['draft', 'confirmed']))
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button class="btn btn-warning btn-edit-item"
                                                            data-item-id="{{ $item->id }}"
                                                            data-makhac-dat="{{ $item->makhac_dat }}"
                                                            data-makhac-loi="{{ $item->makhac_loi }}"
                                                            data-front-dat="{{ $item->front_dat }}"
                                                            data-front-loi="{{ $item->front_loi }}"
                                                            data-back-dat="{{ $item->back_dat }}"
                                                            data-back-loi="{{ $item->back_loi }}"
                                                            data-vi-tri="{{ $item->vi_tri }}"
                                                            data-ghi-chu="{{ $item->ghi_chu }}" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-danger btn-delete-item"
                                                            data-item-id="{{ $item->id }}" title="Xóa">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Status Update Section -->
                @if ($phieuXuatKho->trang_thai != 'cancelled')
                    <div class="card mt-4 modern-card">
                        <div class="card-header bg-warning modern-card-header">
                            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Cập Nhật Trạng Thái</h5>
                        </div>
                        <div class="card-body modern-card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <select class="form-select modern-input" id="new_status">
                                        <option value="draft"
                                            {{ $phieuXuatKho->trang_thai == 'draft' ? 'selected' : '' }}>Nháp</option>
                                        <option value="confirmed"
                                            {{ $phieuXuatKho->trang_thai == 'confirmed' ? 'selected' : '' }}>Đã xác
                                            nhận
                                        </option>
                                        <option value="completed"
                                            {{ $phieuXuatKho->trang_thai == 'completed' ? 'selected' : '' }}>Hoàn thành
                                        </option>
                                        <option value="cancelled"
                                            {{ $phieuXuatKho->trang_thai == 'cancelled' ? 'selected' : '' }}>Đã hủy
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-warning modern-btn" id="btn_update_status">
                                        <i class="fas fa-save"></i> Cập Nhật
                                    </button>
                                </div>
                            </div>
                            <div id="status_message" class="alert d-none mt-3"></div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Thêm Item -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content modern-card">
                <div class="modal-header bg-primary text-white modern-card-header">
                    <h5 class="modal-title" id="addItemModalLabel">
                        <i class="fas fa-plus me-2"></i>Thêm Item Vào Phiếu
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Form -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <form id="searchPhieuVeForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="search_phieu_ps" class="form-label">Phiếu PS:</label>
                                        <input type="text" class="form-control modern-input" id="search_phieu_ps"
                                            placeholder="Nhập phiếu PS...">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="search_ma_hang" class="form-label">Mã Hàng:</label>
                                        <input type="text" class="form-control modern-input" id="search_ma_hang"
                                            placeholder="Nhập mã hàng...">
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary modern-btn w-100">
                                            <i class="fas fa-search"></i> Tìm Kiếm
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Results -->
                    <div id="search_results" class="d-none">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover table-bordered modern-table">
                                <thead style="position: sticky; top: 0; z-index: 10;">
                                    <tr>
                                        <th><input type="checkbox" id="select_all_items"></th>
                                        <th>Phiếu PS</th>
                                        <th>Mã Hàng</th>
                                        <th>Mã Lệnh</th>
                                        <th>Kích Thước</th>
                                        <th>Vị Trí</th>
                                        <th>SL Đơn Hàng</th>
                                    </tr>
                                </thead>
                                <tbody id="search_results_tbody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div id="search_message" class="alert d-none mt-3"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modern-btn" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Đóng
                    </button>
                    <button type="button" class="btn btn-success modern-btn" id="btn_add_selected_items" disabled>
                        <i class="fas fa-plus"></i> Thêm Items Đã Chọn
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Chỉnh Sửa Item -->
    <div class="modal fade" id="editItemModal" tabindex="-1" aria-labelledby="editItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modern-card">
                <div class="modal-header bg-primary text-white modern-card-header">
                    <h5 class="modal-title" id="editItemModalLabel">
                        <i class="fas fa-edit me-2"></i>Chỉnh Sửa Item
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editItemForm">
                        <input type="hidden" id="edit_item_id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_vi_tri" class="form-label">Vị Trí:</label>
                                <input type="text" class="form-control modern-input" id="edit_vi_tri"
                                    name="vi_tri">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_makhac_dat" class="form-label">Mã Khác Đạt:</label>
                                <input type="text" class="form-control modern-input" id="edit_makhac_dat"
                                    name="makhac_dat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_makhac_loi" class="form-label">Mã Khác Lỗi:</label>
                                <input type="text" class="form-control modern-input" id="edit_makhac_loi"
                                    name="makhac_loi">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_front_dat" class="form-label">Front Đạt:</label>
                                <input type="text" class="form-control modern-input" id="edit_front_dat"
                                    name="front_dat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_front_loi" class="form-label">Front Lỗi:</label>
                                <input type="text" class="form-control modern-input" id="edit_front_loi"
                                    name="front_loi">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_back_dat" class="form-label">Back Đạt:</label>
                                <input type="text" class="form-control modern-input" id="edit_back_dat"
                                    name="back_dat">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit_back_loi" class="form-label">Back Lỗi:</label>
                                <input type="text" class="form-control modern-input" id="edit_back_loi"
                                    name="back_loi">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="edit_ghi_chu" class="form-label">Ghi Chú:</label>
                                <textarea class="form-control modern-input" id="edit_ghi_chu" name="ghi_chu" rows="3"></textarea>
                            </div>
                        </div>
                        <div id="edit_message" class="alert d-none"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modern-btn" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Đóng
                    </button>
                    <button type="button" class="btn btn-primary modern-btn" id="btn_save_edit">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
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

        .modern-input {
            border-radius: 12px !important;
            border: 2px solid #e0e0e0;
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
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
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.005);
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
        }

        .info-box {
            margin-bottom: 1rem;
        }

        .info-box label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }

        .info-box p {
            margin: 0;
            font-size: 1rem;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnUpdateStatus = document.getElementById('btn_update_status');
            const newStatusSelect = document.getElementById('new_status');
            const statusMessage = document.getElementById('status_message');
            const editItemModal = new bootstrap.Modal(document.getElementById('editItemModal'));

            // Update Status Handler
            if (btnUpdateStatus) {
                btnUpdateStatus.addEventListener('click', function() {
                    const newStatus = newStatusSelect.value;

                    btnUpdateStatus.disabled = true;
                    btnUpdateStatus.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Đang cập nhật...';

                    fetch('{{ route('phieu-xuat-kho.update-status', $phieuXuatKho->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                trang_thai: newStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                statusMessage.className = 'alert alert-success mt-3';
                                statusMessage.textContent = '✓ ' + data.message;
                                statusMessage.classList.remove('d-none');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                statusMessage.className = 'alert alert-danger mt-3';
                                statusMessage.textContent = '❌ ' + data.message;
                                statusMessage.classList.remove('d-none');
                            }
                        })
                        .catch(error => {
                            statusMessage.className = 'alert alert-danger mt-3';
                            statusMessage.textContent = '❌ Lỗi: ' + error.message;
                            statusMessage.classList.remove('d-none');
                        })
                        .finally(() => {
                            btnUpdateStatus.disabled = false;
                            btnUpdateStatus.innerHTML = '<i class="fas fa-save"></i> Cập Nhật';
                        });
                });
            }

            // Edit Item Handlers
            document.querySelectorAll('.btn-edit-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;
                    const makhacDat = this.dataset.makhacDat || '';
                    const makhacLoi = this.dataset.makhacLoi || '';
                    const frontDat = this.dataset.frontDat || '';
                    const frontLoi = this.dataset.frontLoi || '';
                    const backDat = this.dataset.backDat || '';
                    const backLoi = this.dataset.backLoi || '';
                    const viTri = this.dataset.viTri || '';
                    const ghiChu = this.dataset.ghiChu || '';

                    // Populate modal
                    document.getElementById('edit_item_id').value = itemId;
                    document.getElementById('edit_vi_tri').value = viTri;
                    document.getElementById('edit_makhac_dat').value = makhacDat;
                    document.getElementById('edit_makhac_loi').value = makhacLoi;
                    document.getElementById('edit_front_dat').value = frontDat;
                    document.getElementById('edit_front_loi').value = frontLoi;
                    document.getElementById('edit_back_dat').value = backDat;
                    document.getElementById('edit_back_loi').value = backLoi;
                    document.getElementById('edit_ghi_chu').value = ghiChu;

                    editItemModal.show();
                });
            });

            // Save Edit Handler
            document.getElementById('btn_save_edit').addEventListener('click', function() {
                const itemId = document.getElementById('edit_item_id').value;
                const formData = {
                    vi_tri: document.getElementById('edit_vi_tri').value,
                    makhac_dat: document.getElementById('edit_makhac_dat').value,
                    makhac_loi: document.getElementById('edit_makhac_loi').value,
                    front_dat: document.getElementById('edit_front_dat').value,
                    front_loi: document.getElementById('edit_front_loi').value,
                    back_dat: document.getElementById('edit_back_dat').value,
                    back_loi: document.getElementById('edit_back_loi').value,
                    ghi_chu: document.getElementById('edit_ghi_chu').value,
                };

                const btnSave = this;
                const editMessage = document.getElementById('edit_message');

                btnSave.disabled = true;
                btnSave.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

                fetch(`/phieu-xuat-kho/item/${itemId}/update`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify(formData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            editMessage.className = 'alert alert-success';
                            editMessage.textContent = '✓ ' + data.message;
                            editMessage.classList.remove('d-none');

                            // Update table cells
                            const row = document.getElementById(`item-row-${itemId}`);
                            if (row) {
                                row.querySelector('.item-vi-tri').textContent = formData.vi_tri || '-';
                                row.querySelector('.item-makhac-dat').textContent = formData
                                    .makhac_dat || '-';
                                row.querySelector('.item-makhac-loi').textContent = formData
                                    .makhac_loi || '-';
                                row.querySelector('.item-front-dat').textContent = formData.front_dat ||
                                    '-';
                                row.querySelector('.item-front-loi').textContent = formData.front_loi ||
                                    '-';
                                row.querySelector('.item-back-dat').textContent = formData.back_dat ||
                                    '-';
                                row.querySelector('.item-back-loi').textContent = formData.back_loi ||
                                    '-';
                                row.querySelector('.item-ghi-chu').textContent = formData.ghi_chu ||
                                '-';
                            }

                            setTimeout(() => {
                                editItemModal.hide();
                                editMessage.classList.add('d-none');
                            }, 1500);
                        } else {
                            editMessage.className = 'alert alert-danger';
                            editMessage.textContent = '❌ ' + data.message;
                            editMessage.classList.remove('d-none');
                        }
                    })
                    .catch(error => {
                        editMessage.className = 'alert alert-danger';
                        editMessage.textContent = '❌ Lỗi: ' + error.message;
                        editMessage.classList.remove('d-none');
                    })
                    .finally(() => {
                        btnSave.disabled = false;
                        btnSave.innerHTML = '<i class="fas fa-save"></i> Lưu Thay Đổi';
                    });
            });

            // Delete Item Handlers
            document.querySelectorAll('.btn-delete-item').forEach(btn => {
                btn.addEventListener('click', function() {
                    const itemId = this.dataset.itemId;

                    if (!confirm('Bạn có chắc chắn muốn xóa item này?')) {
                        return;
                    }

                    const btnDelete = this;
                    btnDelete.disabled = true;
                    btnDelete.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    fetch(`/phieu-xuat-kho/item/${itemId}/delete`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Remove row from table
                                const row = document.getElementById(`item-row-${itemId}`);
                                if (row) {
                                    row.style.transition = 'all 0.3s ease';
                                    row.style.opacity = '0';
                                    row.style.transform = 'translateX(-20px)';

                                    setTimeout(() => {
                                        row.remove();
                                        // Update total count
                                        window.location.reload();
                                    }, 300);
                                }
                            } else {
                                alert('❌ ' + data.message);
                                btnDelete.disabled = false;
                                btnDelete.innerHTML = '<i class="fas fa-trash"></i>';
                            }
                        })
                        .catch(error => {
                            alert('❌ Lỗi: ' + error.message);
                            btnDelete.disabled = false;
                            btnDelete.innerHTML = '<i class="fas fa-trash"></i>';
                        });
                });
            });

            // Add Item Modal Handlers
            const addItemModal = new bootstrap.Modal(document.getElementById('addItemModal'));
            const searchForm = document.getElementById('searchPhieuVeForm');
            const searchResults = document.getElementById('search_results');
            const searchResultsTbody = document.getElementById('search_results_tbody');
            const searchMessage = document.getElementById('search_message');
            const btnAddSelectedItems = document.getElementById('btn_add_selected_items');
            const selectAllCheckbox = document.getElementById('select_all_items');

            // Search Form Handler
            if (searchForm) {
                searchForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const phieuPs = document.getElementById('search_phieu_ps').value;
                    const maHang = document.getElementById('search_ma_hang').value;

                    if (!phieuPs && !maHang) {
                        searchMessage.className = 'alert alert-warning';
                        searchMessage.textContent = 'Vui lòng nhập ít nhất một điều kiện tìm kiếm';
                        searchMessage.classList.remove('d-none');
                        return;
                    }

                    searchMessage.classList.add('d-none');
                    searchResults.classList.add('d-none');

                    fetch('{{ route('phieu-ve-entry.search') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                phieu_ps: phieuPs,
                                ma_hang: maHang
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data.length > 0) {
                                searchResultsTbody.innerHTML = '';

                                data.data.forEach(phieu => {
                                    const tr = document.createElement('tr');
                                    tr.innerHTML = `
                                        <td><input type="checkbox" class="item-checkbox" value="${phieu.id}"></td>
                                        <td>${phieu.phieu_ps || '-'}</td>
                                        <td>${phieu.ma_hang || '-'}</td>
                                        <td>${phieu.ma_lenh || '-'}</td>
                                        <td>${phieu.kich_thuoc || '-'}</td>
                                        <td><span class="badge bg-info">${phieu.vi_tri || '-'}</span></td>
                                        <td>${phieu.so_luong_donhang || '-'}</td>
                                    `;
                                    searchResultsTbody.appendChild(tr);
                                });

                                searchResults.classList.remove('d-none');
                                updateAddButtonState();
                            } else {
                                searchMessage.className = 'alert alert-info';
                                searchMessage.textContent = 'Không tìm thấy kết quả';
                                searchMessage.classList.remove('d-none');
                            }
                        })
                        .catch(error => {
                            searchMessage.className = 'alert alert-danger';
                            searchMessage.textContent = 'Lỗi: ' + error.message;
                            searchMessage.classList.remove('d-none');
                        });
                });
            }

            // Select All Handler
            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function() {
                    document.querySelectorAll('.item-checkbox').forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateAddButtonState();
                });
            }

            // Individual Checkbox Handler
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('item-checkbox')) {
                    updateAddButtonState();
                }
            });

            function updateAddButtonState() {
                const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                btnAddSelectedItems.disabled = checkedBoxes.length === 0;
            }

            // Add Selected Items Handler
            if (btnAddSelectedItems) {
                btnAddSelectedItems.addEventListener('click', function() {
                    const checkedBoxes = document.querySelectorAll('.item-checkbox:checked');
                    const phieuVeIds = Array.from(checkedBoxes).map(cb => cb.value);

                    if (phieuVeIds.length === 0) {
                        alert('Vui lòng chọn ít nhất một item');
                        return;
                    }

                    btnAddSelectedItems.disabled = true;
                    btnAddSelectedItems.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Đang thêm...';

                    fetch('{{ route('phieu-xuat-kho.add-items', $phieuXuatKho->id) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                    .content
                            },
                            body: JSON.stringify({
                                phieu_ve_ids: phieuVeIds
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                searchMessage.className = 'alert alert-success';
                                searchMessage.textContent = '✓ ' + data.message;
                                searchMessage.classList.remove('d-none');

                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            } else {
                                searchMessage.className = 'alert alert-danger';
                                searchMessage.textContent = '❌ ' + data.message;
                                searchMessage.classList.remove('d-none');

                                btnAddSelectedItems.disabled = false;
                                btnAddSelectedItems.innerHTML =
                                    '<i class="fas fa-plus"></i> Thêm Items Đã Chọn';
                            }
                        })
                        .catch(error => {
                            searchMessage.className = 'alert alert-danger';
                            searchMessage.textContent = '❌ Lỗi: ' + error.message;
                            searchMessage.classList.remove('d-none');

                            btnAddSelectedItems.disabled = false;
                            btnAddSelectedItems.innerHTML =
                                '<i class="fas fa-plus"></i> Thêm Items Đã Chọn';
                        });
                });
            }
        });
    </script>
</body>

</html>
