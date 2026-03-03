<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Nhập Dữ Liệu Phiếu Về</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <!-- Top Bar with Cart Button -->
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">📋 Nhập Dữ Liệu Phiếu Về</h2>
                    <button type="button" class="btn btn-info btn-lg position-relative" id="btn_open_cart">
                        <i class="fas fa-box"></i> Phiếu Xuất Kho
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            id="cart_count">
                            0
                        </span>
                    </button>
                </div>

                <!-- Search Section -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">🔍 Tìm Kiếm Phiếu</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="phieu_ps">Tìm nhanh theo P/S, Mã Hàng, Vị Trí:</label>
                                    <div class="input-group">
                                        <input type="text" id="phieu_ps" class="form-control form-control-lg"
                                            placeholder="Gõ để tìm... (Enter hoặc click kết quả)" 
                                            autocomplete="off" autofocus>
                                        <button class="btn btn-primary" type="button" id="btn_search">
                                            <i class="fas fa-search"></i> Tìm
                                        </button>
                                    </div>
                                    <small class="text-muted">💡 Gõ 2 ký tự để xem gợi ý. Nhấn Enter để tìm tất cả.</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-secondary" type="button" id="btn_show_recent">
                                        <i class="fas fa-history"></i> Phiếu gần đây
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick suggestions -->
                        <div id="quick_suggestions" class="mt-2 d-none">
                            <div class="d-flex gap-2 flex-wrap" id="suggestions_container"></div>
                        </div>
                        
                        <div id="search_error" class="alert alert-danger d-none mt-2"></div>
                        <div id="search_info" class="alert alert-info d-none mt-2"></div>
                    </div>
                </div>

                <!-- Results Section -->
                <div id="results_section" class="card d-none">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">📊 Danh Sách - <span id="results_count">0</span> ô</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm" id="phieu_ve_table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Phiếu PS</th>
                                        <th>Mã Hàng</th>
                                        <th>Kích Thước</th>
                                        <th>SL Đơn Hàng</th>
                                        <th>Vị Trí</th>
                                        <th>Thêm vào</th>
                                    </tr>
                                </thead>
                                <tbody id="phieu_ve_tbody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Edit Modal -->
                <div class="modal fade" id="editModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-warning">
                                <h5 class="modal-title">
                                    ✏️ Nhập Dữ Liệu - <span id="modal_phieu_ps"></span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form id="entry_form">
                                    <input type="hidden" id="phieu_id" name="phieu_id">

                                    <!-- Display Info -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label><strong>Mã Hàng:</strong></label>
                                                <span id="info_ma_hang" class="badge bg-light text-dark">-</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label><strong>Mã Lệnh:</strong></label>
                                                <span id="info_ma_lenh" class="badge bg-light text-dark">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label><strong>Kích Thước:</strong></label>
                                                <span id="info_kich_thuoc" class="badge bg-light text-dark">-</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="info-group">
                                                <label><strong>SL Đơn Hàng:</strong></label>
                                                <span id="info_so_luong_donhang"
                                                    class="badge bg-light text-dark">-</span>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- Input Fields -->
                                    <h6 class="text-primary mb-3">📝 Các Trường Nhập Liệu:</h6>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="makhac_dat" class="form-label">Mã Khác Đạt:</label>
                                                <input type="text" class="form-control" id="makhac_dat"
                                                    name="makhac_dat" placeholder="Nhập mã khác đạt">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="makhac_loi" class="form-label">Mã Khác Lỗi:</label>
                                                <input type="text" class="form-control" id="makhac_loi"
                                                    name="makhac_loi" placeholder="Nhập mã khác lỗi">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="front_dat" class="form-label">Front Đạt:</label>
                                                <input type="text" class="form-control" id="front_dat"
                                                    name="front_dat" placeholder="Nhập front đạt">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="front_loi" class="form-label">Front Lỗi:</label>
                                                <input type="text" class="form-control" id="front_loi"
                                                    name="front_loi" placeholder="Nhập front lỗi">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="back_dat" class="form-label">Back Đạt:</label>
                                                <input type="text" class="form-control" id="back_dat"
                                                    name="back_dat" placeholder="Nhập back đạt">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="back_loi" class="form-label">Back Lỗi:</label>
                                                <input type="text" class="form-control" id="back_loi"
                                                    name="back_loi" placeholder="Nhập back lỗi">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="ghi_chu" class="form-label">Ghi Chú:</label>
                                        <textarea class="form-control" id="ghi_chu" name="ghi_chu" rows="3" placeholder="Nhập ghi chú"></textarea>
                                    </div>

                                    <div id="form_error" class="alert alert-danger d-none"></div>
                                    <div id="form_success" class="alert alert-success d-none"></div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Hủy
                                </button>
                                <button type="button" class="btn btn-warning" id="btn_save_form">
                                    <i class="fas fa-save"></i> Lưu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Modal -->
                <div class="modal fade" id="cartModal" tabindex="-1">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">
                                    📦 Phiếu Xuất Kho - <span id="cart_total_items">0</span> ô
                                </h5>
                                <button type="button" class="btn-close btn-close-white"
                                    data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div id="cart_alert_error" class="alert alert-danger d-none"></div>
                                <div id="cart_alert_success" class="alert alert-success d-none"></div>

                                <div id="cart_empty_msg" class="alert alert-info">
                                    <i class="fas fa-box"></i> Phiếu xuất kho trống
                                </div>

                                <div id="cart_container" class="d-none">
                                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                                        <table class="table table-hover table-bordered" id="cart_table">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th width="50">STT</th>
                                                    <th>Phiếu PS</th>
                                                    <th>Mã Hàng</th>
                                                    <th>Vị Trí</th>
                                                    <th>Mã Khác Đạt</th>
                                                    <th>Mã Khác Lỗi</th>
                                                    <th>Front Đạt</th>
                                                    <th>Front Lỗi</th>
                                                    <th>Back Đạt</th>
                                                    <th>Back Lỗi</th>
                                                    <th>Ghi Chú</th>
                                                    <th width="80">Xóa</th>
                                                </tr>
                                            </thead>
                                            <tbody id="cart_tbody">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    Đóng
                                </button>
                                <button type="button" class="btn btn-danger" id="btn_clear_cart">
                                    <i class="fas fa-trash"></i> Xóa Hết
                                </button>
                                <a href="#" id="btn_export_cart" class="btn btn-info" download>
                                    <i class="fas fa-file-excel"></i> Xuất Excel
                                </a>
                                <button type="button" class="btn btn-success" id="btn_save_cart">
                                    <i class="fas fa-save"></i> Lưu Phiếu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-group {
            margin-bottom: 10px;
        }

        .info-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }

        .badge {
            padding: 5px 10px;
            font-weight: normal;
        }

        /* Mobile optimizations */
        @media (max-width: 768px) {
            .table {
                font-size: 13px;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 11px;
            }

            .btn {
                white-space: nowrap;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.25rem;
            }

            .btn-add-shipment {
                width: 100%;
            }

            .form-control,
            .input-group {
                font-size: 16px;
            }
        }

        .btn-add-shipment {
            min-width: 50px;
        }

        .table-success {
            background-color: #d4edda !important;
        }

        /* Disabled input styling */
        input:disabled,
        textarea:disabled {
            background-color: #e9ecef !important;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Quick suggestions styling */
        #quick_suggestions .badge {
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
            padding: 8px 12px;
        }

        #quick_suggestions .badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Search input focus */
        #phieu_ps:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
    </style>
    </div>
</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phieuPsInput = document.getElementById('phieu_ps');
        const searchError = document.getElementById('search_error');
        const searchInfo = document.getElementById('search_info');
        const resultsSection = document.getElementById('results_section');
        const resultsCount = document.getElementById('results_count');
        const phieuVeTbody = document.getElementById('phieu_ve_tbody');
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const btnSaveForm = document.getElementById('btn_save_form');
        const cartCountBadge = document.getElementById('cart_count');
        const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
        const btnOpenCart = document.getElementById('btn_open_cart');
        const btnSaveCart = document.getElementById('btn_save_cart');
        const btnClearCart = document.getElementById('btn_clear_cart');
        const btnExportCart = document.getElementById('btn_export_cart');

        let currentData = [];
        let allPhieus = {};
        let recentSearches = JSON.parse(localStorage.getItem('recent_phieu_searches') || '[]');

        // Danh sách mã hàng đặc biệt (chỉ cho nhập front/back)
        const SPECIAL_MA_HANG = [
            'S2315CA1028',
            'S2315CA1028U',
            'S2515CA02GFU',
            'S2615CA1028U',
            'S2662LHAU350',
            'S2662LHAU351',
            'S2662LHAU362',
            'SMSUB2S26LEN05',
            'SMSUS25RUWFCAP'
        ];

        // Kiểm tra xem ma_hang có phải là loại đặc biệt không
        function isSpecialMaHang(maHang) {
            return SPECIAL_MA_HANG.includes(maHang);
        }

        // Autocomplete on input
        phieuPsInput.addEventListener('input', handleAutocomplete);

        // Enter key or click result
        phieuPsInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const value = phieuPsInput.value.trim();
                if (value) {
                    handleSearch(value);
                }
            }
        });

        // Search button
        document.getElementById('btn_search').addEventListener('click', () => {
            const value = phieuPsInput.value.trim();
            if (value) {
                handleSearch(value);
            }
        });

        // Recent button
        document.getElementById('btn_show_recent').addEventListener('click', showRecentSearches);

        // Show recent searches on load
        displayRecentSuggestions();

        // Save button
        btnSaveForm.addEventListener('click', handleSave);

        // Cart button click
        btnOpenCart.addEventListener('click', loadAndShowCart);

        // Cart modal buttons
        btnSaveCart.addEventListener('click', handleSaveCart);
        btnClearCart.addEventListener('click', handleClearCart);

        // Update cart count on page load
        updateCartCount();

        function handleAutocomplete() {
            const value = phieuPsInput.value.trim();

            if (value.length < 2) {
                resultsSection.classList.add('d-none');
                searchError.classList.add('d-none');
                searchInfo.classList.add('d-none');
                return;
            }

            fetch('{{ route('phieu-ve-entry.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        phieu_ps: value
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        currentData = data.data;
                        displayResults(data.data);
                        showInfo(searchInfo, `✓ Tìm thấy ${data.count} ô`);
                        searchError.classList.add('d-none');
                        
                        // Save to recent searches
                        saveRecentSearch(value);
                    } else {
                        resultsSection.classList.add('d-none');
                        searchInfo.classList.add('d-none');
                        showError(searchError, '❌ Không tìm thấy kết quả cho: ' + value);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function handleSearch(value) {
            handleAutocomplete();
        }

        // Save recent search to localStorage
        function saveRecentSearch(keyword) {
            if (!keyword || keyword.length < 2) return;
            
            // Remove if exists
            recentSearches = recentSearches.filter(s => s !== keyword);
            // Add to front
            recentSearches.unshift(keyword);
            // Keep only last 10
            recentSearches = recentSearches.slice(0, 10);
            
            localStorage.setItem('recent_phieu_searches', JSON.stringify(recentSearches));
            displayRecentSuggestions();
        }

        // Display recent suggestions as badges
        function displayRecentSuggestions() {
            const container = document.getElementById('suggestions_container');
            const quickSuggestions = document.getElementById('quick_suggestions');
            
            if (recentSearches.length === 0) {
                quickSuggestions.classList.add('d-none');
                return;
            }
            
            container.innerHTML = '<small class="text-muted me-2">Gần đây:</small>';
            recentSearches.slice(0, 5).forEach(keyword => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-secondary';
                badge.textContent = keyword;
                badge.style.cursor = 'pointer';
                badge.onclick = () => {
                    phieuPsInput.value = keyword;
                    handleSearch(keyword);
                };
                container.appendChild(badge);
            });
            
            quickSuggestions.classList.remove('d-none');
        }

        // Show all recent searches
        function showRecentSearches() {
            if (recentSearches.length === 0) {
                alert('Chưa có lịch sử tìm kiếm');
                return;
            }
            
            const keyword = recentSearches[0];
            phieuPsInput.value = keyword;
            handleSearch(keyword);
        }

        function displayResults(data) {
            phieuVeTbody.innerHTML = '';
            resultsCount.textContent = data.length;

            data.forEach((row) => {
                const tr = document.createElement('tr');
                tr.id = `row_${row.id}`;
                tr.innerHTML = `
                <td><strong>${row.phieu_ps}</strong></td>
                <td>${row.ma_hang || '-'}</td>
                <td>${row.kich_thuoc || '-'}</td>
                <td>${row.so_luong_donhang || '-'}</td>
                <td><span class="badge bg-info">${row.vi_tri || '-'}</span></td>
                <td>
                    <button class="btn btn-sm btn-success btn-add-shipment" data-phieu-id="${row.id}" title="Thêm vào phiếu xuất kho">
                        <i class="fas fa-plus"></i> Thêm
                    </button>
                </td>
            `;
                phieuVeTbody.appendChild(tr);
            });

            resultsSection.classList.remove('d-none');

            // Add event listeners for add buttons
            document.querySelectorAll('.btn-add-shipment').forEach(btn => {
                btn.addEventListener('click', function() {
                    addSingleToShipment(this.dataset.phieuId);
                });
            });
        }

        function handleSave() {
            const phieuId = document.getElementById('phieu_id').value;
            const formError = document.getElementById('form_error');
            const formSuccess = document.getElementById('form_success');

            formError.classList.add('d-none');
            formSuccess.classList.add('d-none');

            const data = {
                phieu_id: phieuId,
                makhac_dat: document.getElementById('makhac_dat').value,
                makhac_loi: document.getElementById('makhac_loi').value,
                front_dat: document.getElementById('front_dat').value,
                front_loi: document.getElementById('front_loi').value,
                back_dat: document.getElementById('back_dat').value,
                back_loi: document.getElementById('back_loi').value,
                ghi_chu: document.getElementById('ghi_chu').value
            };

            btnSaveForm.disabled = true;
            btnSaveForm.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

            fetch('{{ route('phieu-ve-entry.save') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showSuccess(formSuccess, result.message);
                        setTimeout(() => {
                            editModal.hide();
                        }, 1500);
                    } else {
                        showError(formError, result.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError(formError, 'Lỗi: ' + error.message);
                })
                .finally(() => {
                    btnSaveForm.disabled = false;
                    btnSaveForm.innerHTML = '<i class="fas fa-save"></i> Lưu';
                });
        }

        function showError(element, message) {
            element.textContent = message;
            element.classList.remove('d-none');
        }

        function showInfo(element, message) {
            element.textContent = message;
            element.classList.remove('d-none');
        }

        function showSuccess(element, message) {
            element.textContent = message;
            element.classList.remove('d-none');
        }

        function updateCheckAllState() {
            const checkboxes = document.querySelectorAll('.row-check');
            const checkedBoxes = document.querySelectorAll('.row-check:checked');
            if (checkAllCheckbox) {
                checkAllCheckbox.checked = checkboxes.length > 0 && checkboxes.length === checkedBoxes.length;
            }
        }

        function addSingleToShipment(phieuId) {
            console.log('Adding phieu_id:', phieuId);
            fetch('{{ route('phieu-ve-entry.add-to-cart') }}', {
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
                        updateCartCount();
                        // Highlight the row
                        const row = document.getElementById(`row_${phieuId}`);
                        if (row) {
                            row.classList.add('table-success');
                        }
                    } else {
                        alert(data.message || 'Không thể thêm');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function updateCartCount() {
            fetch('{{ route('phieu-ve-entry.cart-count') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    cartCountBadge.textContent = data.count || 0;
                })
                .catch(error => console.error('Error:', error));
        }

        // Make openEditModal global so it can be called from inline onclick
        window.openEditModal = function(id, phieuPs, row) {
            document.getElementById('phieu_id').value = id;
            document.getElementById('modal_phieu_ps').textContent = phieuPs;

            // Set info
            document.getElementById('info_ma_hang').textContent = row.ma_hang || '-';
            document.getElementById('info_ma_lenh').textContent = row.ma_lenh || '-';
            document.getElementById('info_kich_thuoc').textContent = row.kich_thuoc || '-';
            document.getElementById('info_so_luong_donhang').textContent = row.so_luong_donhang || '-';

            // Set form values
            document.getElementById('makhac_dat').value = row.makhac_dat || '';
            document.getElementById('makhac_loi').value = row.makhac_loi || '';
            document.getElementById('front_dat').value = row.front_dat || '';
            document.getElementById('front_loi').value = row.front_loi || '';
            document.getElementById('back_dat').value = row.back_dat || '';
            document.getElementById('back_loi').value = row.back_loi || '';
            document.getElementById('ghi_chu').value = row.ghi_chu || '';

            // Reset alerts
            document.getElementById('form_error').classList.add('d-none');
            document.getElementById('form_success').classList.add('d-none');

            // Enable/disable fields based on ma_hang
            updateFieldsBasedOnMaHang(row.ma_hang);

            editModal.show();
        };

        // Enable/disable fields dựa trên ma_hang
        function updateFieldsBasedOnMaHang(maHang) {
            const makhacDat = document.getElementById('makhac_dat');
            const makhacLoi = document.getElementById('makhac_loi');
            const frontDat = document.getElementById('front_dat');
            const frontLoi = document.getElementById('front_loi');
            const backDat = document.getElementById('back_dat');
            const backLoi = document.getElementById('back_loi');

            if (isSpecialMaHang(maHang)) {
                // Mã hàng đặc biệt: KHÔNG cho nhập makhac, CHO nhập front/back
                makhacDat.disabled = true;
                makhacLoi.disabled = true;
                makhacDat.value = '';
                makhacLoi.value = '';
                makhacDat.placeholder = 'Không áp dụng cho mã hàng này';
                makhacLoi.placeholder = 'Không áp dụng cho mã hàng này';

                frontDat.disabled = false;
                frontLoi.disabled = false;
                backDat.disabled = false;
                backLoi.disabled = false;
                frontDat.placeholder = 'Nhập front đạt';
                frontLoi.placeholder = 'Nhập front lỗi';
                backDat.placeholder = 'Nhập back đạt';
                backLoi.placeholder = 'Nhập back lỗi';
            } else {
                // Mã hàng thường: CHO nhập makhac, KHÔNG cho nhập front/back
                makhacDat.disabled = false;
                makhacLoi.disabled = false;
                makhacDat.placeholder = 'Nhập mã khác đạt';
                makhacLoi.placeholder = 'Nhập mã khác lỗi';

                frontDat.disabled = true;
                frontLoi.disabled = true;
                backDat.disabled = true;
                backLoi.disabled = true;
                frontDat.value = '';
                frontLoi.value = '';
                backDat.value = '';
                backLoi.value = '';
                frontDat.placeholder = 'Không áp dụng cho mã hàng này';
                frontLoi.placeholder = 'Không áp dụng cho mã hàng này';
                backDat.placeholder = 'Không áp dụng cho mã hàng này';
                backLoi.placeholder = 'Không áp dụng cho mã hàng này';
            }
        }

        // ========== CART MODAL FUNCTIONS ==========

        function loadAndShowCart() {
            btnOpenCart.disabled = true;
            btnOpenCart.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang tải...';

            fetch('{{ route('phieu-ve-entry.get-cart') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.cart && Object.keys(data.cart).length > 0) {
                        displayCart(data.cart);
                    } else {
                        showCartEmpty();
                    }
                    cartModal.show();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Lỗi khi tải giỏ hàng');
                })
                .finally(() => {
                    btnOpenCart.disabled = false;
                    btnOpenCart.innerHTML = '<i class="fas fa-shopping-cart"></i> Giỏ Hàng';
                });
        }

        function displayCart(cart) {
            const cartEmptyMsg = document.getElementById('cart_empty_msg');
            const cartContainer = document.getElementById('cart_container');
            const cartTbody = document.getElementById('cart_tbody');
            const cartTotalItems = document.getElementById('cart_total_items');

            cartTbody.innerHTML = '';
            let index = 1;

            for (const phieuId in cart) {
                const item = cart[phieuId];
                const tr = document.createElement('tr');
                tr.id = `cart_row_${phieuId}`;
                
                const isSpecial = isSpecialMaHang(item.ma_hang);
                const makhacDisabled = isSpecial ? 'disabled' : '';
                const frontBackDisabled = isSpecial ? '' : 'disabled';
                const makhacPlaceholder = isSpecial ? 'Không áp dụng' : '';
                const frontBackPlaceholder = isSpecial ? '' : 'Không áp dụng';
                
                tr.innerHTML = `
                <td>${index}</td>
                <td><strong>${item.phieu_ps}</strong></td>
                <td>${item.ma_hang || '-'}</td>
                <td>${item.vi_tri || '-'}</td>
                <td>
                    <input type="text" class="form-control form-control-sm cart-edit-input" 
                           data-phieu-id="${phieuId}" data-field="makhac_dat"
                           value="${isSpecial ? '' : (item.makhac_dat || '')}"
                           ${makhacDisabled}
                           placeholder="${makhacPlaceholder}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm cart-edit-input" 
                           data-phieu-id="${phieuId}" data-field="makhac_loi"
                           value="${isSpecial ? '' : (item.makhac_loi || '')}"
                           ${makhacDisabled}
                           placeholder="${makhacPlaceholder}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm cart-edit-input" 
                           data-phieu-id="${phieuId}" data-field="front_dat"
                           value="${isSpecial ? (item.front_dat || '') : ''}"
                           ${frontBackDisabled}
                           placeholder="${frontBackPlaceholder}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm cart-edit-input" 
                           data-phieu-id="${phieuId}" data-field="front_loi"
                           value="${isSpecial ? (item.front_loi || '') : ''}"
                           ${frontBackDisabled}
                           placeholder="${frontBackPlaceholder}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm cart-edit-input" 
                           data-phieu-id="${phieuId}" data-field="back_dat"
                           value="${isSpecial ? (item.back_dat || '') : ''}"
                           ${frontBackDisabled}
                           placeholder="${frontBackPlaceholder}">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm cart-edit-input" 
                           data-phieu-id="${phieuId}" data-field="back_loi"
                           value="${isSpecial ? (item.back_loi || '') : ''}"
                           ${frontBackDisabled}
                           placeholder="${frontBackPlaceholder}">
                </td>
                <td>
                    <textarea class="form-control form-control-sm cart-edit-input" 
                              data-phieu-id="${phieuId}" data-field="ghi_chu"
                              rows="1">${item.ghi_chu || ''}</textarea>
                </td>
                <td>
                    <button class="btn btn-sm btn-danger cart-remove-btn" data-phieu-id="${phieuId}">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
                cartTbody.appendChild(tr);
                index++;
            }

            cartTotalItems.textContent = index - 1;
            cartEmptyMsg.classList.add('d-none');
            cartContainer.classList.remove('d-none');

            // Add event listeners for remove buttons
            document.querySelectorAll('.cart-remove-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    removeFromCartModal(this.dataset.phieuId);
                });
            });

            // Update export link
            btnExportCart.href = '{{ route('phieu-ve-entry.export-cart') }}';
        }

        function showCartEmpty() {
            const cartEmptyMsg = document.getElementById('cart_empty_msg');
            const cartContainer = document.getElementById('cart_container');

            cartEmptyMsg.classList.remove('d-none');
            cartContainer.classList.add('d-none');
        }

        function removeFromCartModal(phieuId) {
            if (!confirm('Xóa phiếu này?')) return;

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
                        const row = document.getElementById(`cart_row_${phieuId}`);
                        if (row) row.remove();
                        updateCartCount();
                        showCartSuccess('Đã xóa khỏi giỏ hàng');

                        if (document.getElementById('cart_tbody').children.length === 0) {
                            showCartEmpty();
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        function handleSaveCart() {
            // Collect all values from inputs
            const updates = {};
            document.querySelectorAll('.cart-edit-input').forEach(input => {
                const phieuId = input.dataset.phieuId;
                const field = input.dataset.field;

                if (!updates[phieuId]) {
                    updates[phieuId] = {};
                }
                updates[phieuId][field] = input.value;
            });

            // Send updates to session first
            const promises = [];
            for (const phieuId in updates) {
                const data = {
                    phieu_id: phieuId,
                    ...updates[phieuId]
                };
                promises.push(
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

            btnSaveCart.disabled = true;
            btnSaveCart.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang lưu...';

            Promise.all(promises).then(() => {
                    // Now save to database
                    return fetch('{{ route('phieu-ve-entry.save-cart') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .content
                        },
                        body: JSON.stringify({})
                    }).then(r => r.json());
                })
                .then(data => {
                    if (data.success) {
                        showCartSuccess(data.message);
                        updateCartCount();
                        setTimeout(() => {
                            cartModal.hide();
                            loadAndShowCart();
                        }, 1500);
                    } else {
                        showCartError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showCartError('Lỗi: ' + error.message);
                })
                .finally(() => {
                    btnSaveCart.disabled = false;
                    btnSaveCart.innerHTML = '<i class="fas fa-save"></i> Lưu Phiếu';
                });
        }

        function handleClearCart() {
            if (!confirm('Xóa tất cả phiếu? (Dữ liệu chưa lưu sẽ mất)')) return;

            // Get all rows and delete
            const rows = document.getElementById('cart_tbody').children;
            const promises = [];

            Array.from(rows).forEach(row => {
                const phieuId = row.id.replace('cart_row_', '');
                promises.push(
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
                    }).then(r => r.json())
                );
            });

            Promise.all(promises).then(() => {
                showCartSuccess('Đã xóa tất cả');
                updateCartCount();
                showCartEmpty();
            });
        }

        function showCartError(message) {
            const alertError = document.getElementById('cart_alert_error');
            alertError.textContent = message;
            alertError.classList.remove('d-none');
            document.getElementById('cart_alert_success').classList.add('d-none');
        }

        function showCartSuccess(message) {
            const alertSuccess = document.getElementById('cart_alert_success');
            alertSuccess.textContent = '✓ ' + message;
            alertSuccess.classList.remove('d-none');
            document.getElementById('cart_alert_error').classList.add('d-none');
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
