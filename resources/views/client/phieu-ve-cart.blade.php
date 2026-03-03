<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giỏ Hàng - Phiếu Về</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>🛒 Giỏ Hàng Phiếu Về</h2>
                    <a href="{{ route('phieu-ve-entry.show') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
                </div>

                @if (empty($cart))
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Giỏ hàng trống. <a
                            href="{{ route('phieu-ve-entry.show') }}">Tìm và thêm phiếu</a>
                    </div>
                @else
                    <div class="mb-3">
                        <span class="badge bg-success">{{ count($cart) }} phiếu</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mb-3 d-flex gap-2">
                        <button class="btn btn-success" id="btn_save_all">
                            <i class="fas fa-save"></i> Lưu Tất Cả vào Database
                        </button>
                        <a href="{{ route('phieu-ve-entry.export-cart') }}" class="btn btn-info">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                        <button class="btn btn-danger" id="btn_clear_cart">
                            <i class="fas fa-trash"></i> Xóa Giỏ
                        </button>
                    </div>

                    <!-- Alert Messages -->
                    <div id="alert_success" class="alert alert-success d-none" role="alert"></div>
                    <div id="alert_error" class="alert alert-danger d-none" role="alert"></div>

                    <!-- Cart Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">STT</th>
                                    <th>Phiếu PS</th>
                                    <th>Mã Hàng</th>
                                    <th>Mã Lệnh</th>
                                    <th>Kích Thước</th>
                                    <th>Mã Khác Đạt</th>
                                    <th>Mã Khác Lỗi</th>
                                    <th>Front Đạt</th>
                                    <th>Front Lỗi</th>
                                    <th>Back Đạt</th>
                                    <th>Back Lỗi</th>
                                    <th>Ghi Chú</th>
                                    <th>Hành Động</th>
                                </tr>
                            </thead>
                            <tbody id="cart_tbody">
                                @foreach ($cart as $phieuId => $item)
                                    <tr id="row_{{ $phieuId }}">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $item['phieu_ps'] }}</strong></td>
                                        <td>{{ $item['ma_hang'] ?? '-' }}</td>
                                        <td>{{ $item['ma_lenh'] ?? '-' }}</td>
                                        <td>{{ $item['kich_thuoc'] ?? '-' }}</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm cart-input"
                                                data-phieu-id="{{ $phieuId }}" data-field="makhac_dat"
                                                value="{{ $item['makhac_dat'] ?? '' }}" placeholder="Nhập">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm cart-input"
                                                data-phieu-id="{{ $phieuId }}" data-field="makhac_loi"
                                                value="{{ $item['makhac_loi'] ?? '' }}" placeholder="Nhập">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm cart-input"
                                                data-phieu-id="{{ $phieuId }}" data-field="front_dat"
                                                value="{{ $item['front_dat'] ?? '' }}" placeholder="Nhập">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm cart-input"
                                                data-phieu-id="{{ $phieuId }}" data-field="front_loi"
                                                value="{{ $item['front_loi'] ?? '' }}" placeholder="Nhập">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm cart-input"
                                                data-phieu-id="{{ $phieuId }}" data-field="back_dat"
                                                value="{{ $item['back_dat'] ?? '' }}" placeholder="Nhập">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm cart-input"
                                                data-phieu-id="{{ $phieuId }}" data-field="back_loi"
                                                value="{{ $item['back_loi'] ?? '' }}" placeholder="Nhập">
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm cart-input" data-phieu-id="{{ $phieuId }}" data-field="ghi_chu"
                                                rows="2" placeholder="Nhập">{{ $item['ghi_chu'] ?? '' }}</textarea>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-danger btn-remove"
                                                data-phieu-id="{{ $phieuId }}">
                                                <i class="fas fa-trash"></i> Xóa
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Bottom Action Buttons -->
                    <div class="mt-3 d-flex gap-2">
                        <button class="btn btn-success btn-lg" id="btn_save_all_bottom">
                            <i class="fas fa-save"></i> Lưu Tất Cả vào Database
                        </button>
                        <a href="{{ route('phieu-ve-entry.export-cart') }}" class="btn btn-info btn-lg">
                            <i class="fas fa-file-excel"></i> Xuất Excel
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .cart-input {
            font-size: 12px;
            padding: 5px;
        }

        .table-responsive {
            max-height: 70vh;
            overflow-y: auto;
        }

        thead {
            position: sticky;
            top: 0;
        }

        .btn-remove {
            white-space: nowrap;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btnSaveAll = document.getElementById('btn_save_all');
            const btnSaveAllBottom = document.getElementById('btn_save_all_bottom');
            const btnClearCart = document.getElementById('btn_clear_cart');
            const cartInputs = document.querySelectorAll('.cart-input');
            const alertSuccess = document.getElementById('alert_success');
            const alertError = document.getElementById('alert_error');
            const cartTbody = document.getElementById('cart_tbody');

            let cartData = {};

            // Initialize cart data
            initializeCartData();

            // Save button click
            if (btnSaveAll) {
                btnSaveAll.addEventListener('click', handleSaveAll);
            }
            if (btnSaveAllBottom) {
                btnSaveAllBottom.addEventListener('click', handleSaveAll);
            }

            // Clear cart button
            if (btnClearCart) {
                btnClearCart.addEventListener('click', handleClearCart);
            }

            // Input change events
            cartInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const phieuId = this.dataset.phieuId;
                    const field = this.dataset.field;
                    const value = this.value;

                    if (!cartData[phieuId]) {
                        cartData[phieuId] = {};
                    }
                    cartData[phieuId][field] = value;
                });
            });

            // Remove button click
            document.querySelectorAll('.btn-remove').forEach(btn => {
                btn.addEventListener('click', function() {
                    handleRemoveFromCart(this.dataset.phieuId);
                });
            });

            function initializeCartData() {
                cartInputs.forEach(input => {
                    const phieuId = input.dataset.phieuId;
                    const field = input.dataset.field;
                    const value = input.value;

                    if (!cartData[phieuId]) {
                        cartData[phieuId] = {};
                    }
                    cartData[phieuId][field] = value;
                });
            }

            function handleSaveAll() {
                // First update all items in session
                const updatePromises = [];
                for (const phieuId in cartData) {
                    const data = cartData[phieuId];
                    data.phieu_id = phieuId;

                    updatePromises.push(
                        fetch('{{ route('phieu-ve-entry.update-cart-item') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(data)
                        }).then(r => r.json())
                    );
                }

                Promise.all(updatePromises).then(() => {
                    // Now save all to database
                    saveToDB();
                });
            }

            function saveToDB() {
                btnSaveAll.disabled = true;
                btnSaveAll.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

                fetch('{{ route('phieu-ve-entry.save-cart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({})
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showSuccess(alertSuccess, data.message + ` (${data.saved_count} phiếu)`);
                            setTimeout(() => {
                                location.href = '{{ route('phieu-ve-entry.show') }}';
                            }, 1500);
                        } else {
                            showError(alertError, data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError(alertError, 'Lỗi: ' + error.message);
                    })
                    .finally(() => {
                        btnSaveAll.disabled = false;
                        btnSaveAll.innerHTML = '<i class="fas fa-save"></i> Lưu Tất Cả vào Database';
                    });
            }

            function handleRemoveFromCart(phieuId) {
                if (!confirm('Xóa phiếu này khỏi giỏ?')) {
                    return;
                }

                fetch('{{ route('phieu-ve-entry.remove-from-cart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            phieu_id: phieuId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const row = document.getElementById(`row_${phieuId}`);
                            row.remove();
                            showSuccess(alertSuccess, 'Đã xóa khỏi giỏ hàng');

                            // If no more rows, redirect
                            if (cartTbody.children.length === 0) {
                                setTimeout(() => {
                                    location.href = '{{ route('phieu-ve-entry.show') }}';
                                }, 1500);
                            }
                        } else {
                            showError(alertError, data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError(alertError, 'Lỗi: ' + error.message);
                    });
            }

            function handleClearCart() {
                if (!confirm('Xóa tất cả phiếu khỏi giỏ? Dữ liệu nhập sẽ bị mất!')) {
                    return;
                }

                // Remove all items
                const rows = Array.from(cartTbody.children);
                rows.forEach(row => {
                    const phieuId = row.id.replace('row_', '');
                    fetch('{{ route('phieu-ve-entry.remove-from-cart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({
                            phieu_id: phieuId
                        })
                    }).then(r => r.json());
                });

                showSuccess(alertSuccess, 'Đã xóa tất cả');
                setTimeout(() => {
                    location.href = '{{ route('phieu-ve-entry.show') }}';
                }, 1500);
            }

            function showError(element, message) {
                element.textContent = message;
                element.classList.remove('d-none');
                window.scrollTo(0, 0);
            }

            function showSuccess(element, message) {
                element.textContent = '✓ ' + message;
                element.classList.remove('d-none');
                window.scrollTo(0, 0);
            }
        });
    </script>
</body>

</html>
