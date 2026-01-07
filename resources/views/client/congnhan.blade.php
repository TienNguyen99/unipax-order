<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PHIẾU SẢN XUẤT CÔNG NHÂN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --secondary: #ec4899;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --border: #e5e7eb;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, var(--primary) 0%, #5b21b6 100%);
            min-height: 100vh;
            color: var(--text-dark);
        }

        /* MAIN CONTAINER */
        .main-card {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 24px;
            box-shadow: var(--shadow-xl);
            max-width: 100%;
            margin: 0 auto;
            animation: slideUp 0.4s ease-out;
        }

        @media (min-width: 768px) {
            .main-card {
                max-width: 600px;
                padding: 32px;
                margin: 40px auto;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            text-align: center;
            color: var(--text-gray);
            font-size: 0.95rem;
            margin-bottom: 24px;
        }

        /* BUTTONS */
        .btn-main {
            width: 100%;
            padding: 16px 24px;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 16px;
            box-shadow: var(--shadow-md);
        }

        .btn-main:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-main:active {
            transform: translateY(0);
        }

        /* OVERLAY & MODAL */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            display: none;
            overflow-y: auto;
            z-index: 1000;
            padding: 20px;
            animation: fadeIn 0.3s ease-out;
        }

        .overlay.show {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding-top: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal-content-custom {
            background: var(--bg-white);
            border-radius: 20px;
            padding: 24px;
            width: 100%;
            max-width: 600px;
            position: relative;
            box-shadow: var(--shadow-xl);
            animation: slideUp 0.4s ease-out;
        }

        @media (min-width: 768px) {
            .modal-content-custom {
                padding: 32px;
            }
        }

        .close-btn {
            position: absolute;
            top: 16px;
            right: 16px;
            width: 40px;
            height: 40px;
            border: none;
            background: var(--bg-light);
            color: var(--text-dark);
            cursor: pointer;
            border-radius: 50%;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .close-btn:hover {
            background: var(--border);
            transform: rotate(90deg);
        }

        /* STEPS */
        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: slideUp 0.3s ease-out;
        }

        .step-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 3px solid var(--primary);
        }

        .step-progress {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            justify-content: center;
        }

        .step-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--border);
            transition: all 0.3s;
        }

        .step-dot.active {
            background: var(--primary);
            width: 24px;
            border-radius: 4px;
        }

        /* WORK CARDS */
        .work-card {
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            background: var(--bg-white);
            color: var(--text-dark);
        }

        .work-card:hover {
            border-color: var(--primary);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .work-card:active {
            transform: scale(0.98);
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        /* SECTIONS */
        .section-label {
            font-weight: 700;
            color: var(--primary);
            margin-top: 24px;
            margin-bottom: 12px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* SUGGEST BOX */
        .suggest-box {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid var(--border);
            border-radius: 8px;
            margin-top: 8px;
            background: var(--bg-light);
        }

        .suggest-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
            cursor: pointer;
            transition: all 0.2s;
        }

        .suggest-item:hover {
            background: var(--bg-white);
            padding-left: 20px;
        }

        .suggest-item:last-child {
            border-bottom: none;
        }

        /* FORMS */
        .form-control,
        .form-select {
            padding: 12px 14px;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border);
            transition: all 0.2s;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        /* BUTTON GROUPS */
        .btn-group-custom {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }

        .btn-group-custom .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-group-custom .btn.btn-secondary {
            background: var(--bg-light);
            color: var(--text-dark);
            border: 1px solid var(--border);
        }

        .btn-group-custom .btn.btn-secondary:hover {
            background: var(--border);
        }

        .btn-group-custom .btn.btn-success {
            background: var(--success);
            color: white;
        }

        .btn-group-custom .btn.btn-success:hover {
            background: #059669;
        }

        /* QC ROWS */
        .qc-row {
            background: var(--bg-light);
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
            position: relative;
            transition: all 0.2s;
        }

        .qc-row:hover {
            border-color: var(--primary);
        }

        .qc-row-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .qc-row-number {
            font-weight: 700;
            color: var(--primary);
            font-size: 1rem;
        }

        .remove-qc-row {
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            font-size: 1.2rem;
            line-height: 1;
            transition: all 0.2s;
        }

        .remove-qc-row:hover {
            background: #dc2626;
            transform: scale(1.1);
        }

        /* REVIEW BOX */
        #reviewBox {
            background: var(--bg-light);
            padding: 16px;
            border-radius: 12px;
            border: 2px solid var(--border);
        }

        #reviewBox > * {
            margin-bottom: 12px;
        }

        #reviewBox hr {
            margin: 16px 0;
        }

        /* EXTRA FIELDS */
        .toggle-extra {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: var(--bg-white);
            color: var(--primary);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 16px;
        }

        .toggle-extra:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.05) 0%, rgba(99, 102, 241, 0.02) 100%);
        }

        /* IMPORT SECTION */
        .import-section {
            background: var(--bg-light);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
        }

        .import-section h5 {
            font-weight: 700;
            margin-bottom: 12px;
            color: var(--text-dark);
        }

        /* RESPONSIVE */
        @media (max-width: 640px) {
            body {
                padding: 0;
            }

            .main-card {
                border-radius: 20px 20px 0 0;
                margin-top: auto;
                padding: 20px;
            }

            .overlay {
                padding: 12px;
            }

            .overlay.show {
                align-items: flex-end;
            }

            .modal-content-custom {
                border-radius: 20px 20px 0 0;
                max-width: 100%;
                padding: 20px;
            }

            .header-title {
                font-size: 1.5rem;
            }

            .step-title {
                font-size: 1.25rem;
            }

            .work-card {
                padding: 12px;
                font-size: 0.9rem;
            }

            .btn-main {
                font-size: 1rem;
                padding: 14px 20px;
            }
        }

        /* UTILITIES */
        .mb-2 { margin-bottom: 8px; }
        .mb-3 { margin-bottom: 16px; }
        .mb-4 { margin-bottom: 24px; }
        .mt-2 { margin-top: 8px; }
        .mt-3 { margin-top: 16px; }
        .mt-4 { margin-top: 24px; }

        .text-center { text-align: center; }
        .text-secondary { color: var(--text-gray); }
        .text-muted { color: var(--text-gray); }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-danger {
            background: #fee2e2;
            color: #7f1d1d;
            border: 1px solid #fca5a5;
        }

        .alert-info {
            background: #dbeafe;
            color: #0c2d6b;
            border: 1px solid #93c5fd;
        }

        .alert-warning {
            background: #fef3c7;
            color: #78350f;
            border: 1px solid #fde68a;
        }
    </style>
</head>

<body>
    <div class="main-card">
        <h1 class="header-title">
            <i class="bi bi-file-earmark-text"></i> PHIẾU SẢN XUẤT
        </h1>
        <p class="header-subtitle">Nhập liệu nhanh chóng, chính xác</p>

        <!-- Import Section -->
        <div class="import-section">
            <h5><i class="bi bi-upload"></i> Import Lệnh Sản Xuất</h5>
            <form id="importForm" enctype="multipart/form-data">
                <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" class="form-control mb-3"
                    required placeholder="Chọn file Excel">
                <button type="submit" class="btn btn-primary w-100" style="padding: 10px; border-radius: 8px; border: none; background: var(--primary); color: white; font-weight: 600;">
                    <i class="bi bi-cloud-upload"></i> Import Excel
                </button>
            </form>
            <div id="importResult" class="mt-3"></div>
        </div>

        <button id="touchBtn" class="btn-main">
            <i class="bi bi-play-circle-fill"></i> BẮTĐẦU NHẬP LIỆU
        </button>
    </div>

    <!-- Overlay Modal -->
    <div id="overlay" class="overlay">
        <div class="modal-content-custom">
            <button class="close-btn" id="closeBtn"><i class="bi bi-x-lg"></i></button>

            <!-- STEP 1: Tìm mã lệnh -->
            <div id="step1" class="step active">
                <div class="step-progress">
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>
                <h4 class="step-title"><i class="bi bi-search"></i> Bước 1: Tìm Mã Lệnh</h4>

                <input type="text" id="searchLenh" placeholder="🔍 Nhập mã lệnh..." class="form-control mb-3">
                <div id="suggestBox" class="suggest-box"></div>

                <button class="btn-main" id="scanQRBtn" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); margin-top: 16px;">
                    <i class="bi bi-qr-code"></i> Quét Mã QR
                </button>

                <div id="qrReader" style="width:100%; display:none; border-radius: 12px; overflow: hidden;" class="mt-3"></div>
                <button class="btn-main" id="stopScanBtn" style="display:none; background: var(--danger); margin-top: 16px;">
                    <i class="bi bi-stop-circle"></i> Dừng Quét
                </button>
            </div>

            <!-- STEP 2: Chọn công việc -->
            <div id="step2" class="step">
                <div class="step-progress">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                    <div class="step-dot"></div>
                </div>
                <h4 class="step-title"><i class="bi bi-collection"></i> Bước 2: Chọn Công Việc</h4>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-geo-alt"></i> Chọn Khu Vực In *</label>
                    <select id="khuVucSelect" class="form-select" required>
                        <option value="">-- Chọn khu vực --</option>
                        <option value="khu_vuc_1" selected>Khu vực 1</option>
                        <option value="khu_vuc_2">Khu vực 2</option>
                        <option value="khu_vuc_3">Khu vực 3</option>
                    </select>
                </div>

                <div class="section-label"><i class="bi bi-diagram-3"></i> Tầng Trệt</div>
                <div class="row g-2">
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="DỆT DÂY" data-type="normal">DỆT DÂY</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="DỆT NHÃN" data-type="normal">DỆT NHÃN</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="QUẤN CUỘN" data-type="normal">QUẤN CUỘN</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="THUN BẢN" data-type="normal">THUN BẢN</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="BẾ TPU" data-type="normal">BẾ TPU</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="QUAY ĐẦU" data-type="normal">QUAY ĐẦU</div>
                    </div>
                    <div class="col-12">
                        <div class="work-card congdoan" data-value="KHÁC" data-type="normal">KHÁC</div>
                    </div>
                </div>

                <div class="section-label mt-4"><i class="bi bi-layers"></i> Tầng 1</div>
                <div class="row g-2">
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="IN LỤA" data-type="normal">IN LỤA</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="IN TRỤC" data-type="normal">IN TRỤC</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="ĐÚC" data-type="normal">ĐÚC</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="CẮT" data-type="normal">CẮT</div>
                    </div>
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="ÉP" data-type="normal">ÉP</div>
                    </div>
                </div>

                <div class="section-label mt-4"><i class="bi bi-check2-circle"></i> QC</div>
                <div class="row g-2">
                    <div class="col-6 col-sm-6">
                        <div class="work-card congdoan" data-value="QC" data-type="qc">KIỂM HÀNG</div>
                    </div>
                </div>

                <div class="btn-group-custom">
                    <button class="btn btn-secondary" id="back1"><i class="bi bi-arrow-left"></i> Quay Lại</button>
                </div>
            </div>

            <!-- STEP 3: Nhập thông tin (Normal) -->
            <div id="step3" class="step">
                <div class="step-progress">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                </div>
                <h4 class="step-title"><i class="bi bi-pencil-square"></i> Bước 3: Nhập Thông Tin</h4>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-badge"></i> Mã (tên) công nhân *</label>
                    <input type="text" id="nhanvienId" class="form-control" placeholder="VD: CN001" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-check-circle"></i> Số lượng đạt *</label>
                    <input type="number" id="soLuongDat" class="form-control" placeholder="Nhập số lượng" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-exclamation-circle"></i> Số lượng lỗi</label>
                    <input type="number" id="soLuongLoi" class="form-control" placeholder="Nhập số lượng lỗi" value="0">
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-chat-dots"></i> Ghi chú</label>
                    <div style="display:flex; gap:10px;">
                        <input type="text" id="dienGiai" class="form-control" placeholder="Nhập ghi chú">
                        <button type="button" id="micBtn" class="btn" style="width:50px; background: var(--danger); color: white; border: none; border-radius: 8px; font-size: 1.2rem;">
                            <i class="bi bi-mic"></i>
                        </button>
                    </div>
                    <div id="micStatus" class="text-muted mt-2" style="font-size: 0.85rem;"></div>
                </div>

                <button id="toggleExtra" class="toggle-extra">
                    <i class="bi bi-plus-circle"></i> Thêm Thông Tin Chi Tiết
                </button>

                <div id="extraFields" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-gear"></i> Máy sản xuất</label>
                        <input type="text" id="maySx" class="form-control" placeholder="VD: Máy 001">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số pick</label>
                        <input type="text" id="soPick" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số cuộn</label>
                        <input type="text" id="soCuon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số dòng</label>
                        <input type="text" id="soDong" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số bản</label>
                        <input type="text" id="soBan" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số đầu</label>
                        <input type="text" id="soDau" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số khuôn</label>
                        <input type="text" id="soKhuon" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Khuôn sản xuất</label>
                        <input type="text" id="khuonSx" class="form-control">
                    </div>
                </div>

                <div class="btn-group-custom">
                    <button class="btn btn-secondary" id="back2"><i class="bi bi-arrow-left"></i> Quay Lại</button>
                    <button class="btn btn-success" id="confirmBtn"><i class="bi bi-check-circle"></i> Tiếp Tục</button>
                </div>
            </div>

            <!-- STEP 3B: Form QC Multi-row -->
            <div id="step3qc" class="step">
                <div class="step-progress">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot"></div>
                </div>
                <h4 class="step-title"><i class="bi bi-check-all"></i> Bước 3: Nhập Liệu QC</h4>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-person-badge"></i> Mã (tên) công nhân *</label>
                    <input type="text" id="nhanvienIdQC" class="form-control" placeholder="VD: CN001" required>
                </div>

                <div class="mb-3">
                    <label class="form-label"><i class="bi bi-chat-dots"></i> Ghi chú chung</label>
                    <input type="text" id="dienGiaiQC" class="form-control" placeholder="Ghi chú chung cho QC">
                </div>

                <hr style="margin: 20px 0; border-top: 1px solid var(--border);">
                <h5 class="mb-3"><i class="bi bi-list-check"></i> Danh sách lệnh kiểm tra</h5>

                <div id="qcRowsContainer">
                    <!-- Rows will be added here -->
                </div>

                <button class="toggle-extra" id="addQCRow" style="border: 1px solid var(--success); color: var(--success); background: rgba(16, 185, 129, 0.05); margin-bottom: 20px;">
                    <i class="bi bi-plus-circle"></i> Thêm Lệnh
                </button>

                <div class="btn-group-custom">
                    <button class="btn btn-secondary" id="backQC"><i class="bi bi-arrow-left"></i> Quay Lại</button>
                    <button class="btn btn-success" id="confirmQCBtn"><i class="bi bi-check-circle"></i> Tiếp Tục</button>
                </div>
            </div>

            <!-- STEP 4: Xác nhận -->
            <div id="step4" class="step">
                <div class="step-progress">
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                    <div class="step-dot active"></div>
                </div>
                <h4 class="step-title"><i class="bi bi-clipboard-check"></i> Bước 4: Xác Nhận</h4>

                <div id="reviewBox" class="mb-3"></div>
                <div id="alertBox" class="mb-3"></div>

                <div class="btn-group-custom">
                    <button class="btn btn-secondary" id="back3"><i class="bi bi-pencil-square"></i> Sửa</button>
                    <button class="btn btn-success" id="submitBtn" style="background: var(--success); color: white;"><i class="bi bi-check-circle"></i> Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Helper function để xử lý Swal an toàn
        function showAlert(config) {
            if (typeof Swal !== 'undefined') {
                return Swal.fire(config);
            } else {
                alert(config.text || config.title);
            }
        }

        const overlay = document.getElementById('overlay');
        const nhapData = {};
        const suggestBox = document.getElementById('suggestBox');
        const searchLenh = document.getElementById('searchLenh');

        document.getElementById('touchBtn').onclick = () => {
            overlay.classList.add('show');
        };

        document.getElementById('closeBtn').onclick = () => {
            overlay.classList.remove('show');
        };

        function showStep(id) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById(id).classList.add('active');
        }

        /* ======================= SEARCH LỆNH ======================= */
        let timer;
        searchLenh.addEventListener('input', function() {
            const keyword = this.value.trim();
            clearTimeout(timer);
            if (keyword.length < 2) {
                suggestBox.innerHTML = '';
                return;
            }
            timer = setTimeout(async () => {
                const res = await fetch(
                    `{{ route('lenh-sx.search') }}?q=${encodeURIComponent(keyword)}`);
                const data = await res.json();
                if (data.length === 0) {
                    suggestBox.innerHTML = "<div class='p-2 text-muted'>Không tìm thấy mã lệnh</div>";
                    return;
                }
                suggestBox.innerHTML = data.map(item =>
                    `<div class='suggest-item' data-value='${item.ma_lenh}'><b>${item.ma_lenh}</b> - ${item.description||''}</div>`
                ).join('');
                document.querySelectorAll('.suggest-item').forEach(it => {
                    it.onclick = () => {
                        nhapData.lenh_sx = it.dataset.value;
                        showStep('step2');
                    };
                });
            }, 400);
        });

        /* ======================= QR CODE SCAN ======================= */
        let qrScanner;
        const qrDiv = document.getElementById('qrReader');
        const btnScan = document.getElementById('scanQRBtn');
        const btnStop = document.getElementById('stopScanBtn');

        btnScan.onclick = () => {
            qrDiv.style.display = 'block';
            btnStop.style.display = 'block';

            qrScanner = new Html5Qrcode("qrReader");

            qrScanner.start({
                    facingMode: "environment"
                }, {
                    fps: 10,
                    qrbox: 240
                },
                qrData => {
                    nhapData.lenh_sx = qrData;

                    qrScanner.stop().then(() => {
                        qrDiv.style.display = 'none';
                        btnStop.style.display = 'none';
                    });

                    showStep("step2");
                }
            );
        };

        btnStop.onclick = () => {
            qrScanner.stop().then(() => {
                qrDiv.style.display = 'none';
                btnStop.style.display = 'none';
            });
        };

        /* ======================= STEP LOGIC ======================= */
        document.getElementById("toggleExtra").onclick = () => {
            const extra = document.getElementById("extraFields");

            if (extra.style.display === "none") {
                extra.style.display = "block";
                document.getElementById("toggleExtra").innerHTML = "<i class='bi bi-minus-circle'></i> Ẩn Thông Tin Chi Tiết";
            } else {
                extra.style.display = "none";
                document.getElementById("toggleExtra").innerHTML = "<i class='bi bi-plus-circle'></i> Thêm Thông Tin Chi Tiết";
            }
        };

        document.querySelectorAll('.congdoan').forEach(btn => {
            btn.onclick = () => {
                nhapData.cong_doan = btn.dataset.value;
                const type = btn.dataset.type;

                if (type === 'qc') {
                    // QC flow - show multi-row form
                    showStep('step3qc');
                    initQCForm();
                } else {
                    // Normal flow
                    showStep('step3');
                }
            };
        });

        document.getElementById('back1').onclick = () => showStep('step1');
        document.getElementById('back2').onclick = () => showStep('step2');
        document.getElementById('back3').onclick = () => {
            if (nhapData.qc_rows) {
                showStep('step3qc');
            } else {
                showStep('step3');
            }
        };
        document.getElementById('backQC').onclick = () => showStep('step2');

        /* ======================= QC MULTI-ROW FORM ======================= */
        let qcRowCounter = 0;

        function initQCForm() {
            qcRowCounter = 0;
            document.getElementById('qcRowsContainer').innerHTML = '';
            addQCRow();
        }

        function addQCRow() {
            qcRowCounter++;
            const rowId = `qc-row-${qcRowCounter}`;

            const rowHTML = `
                <div class="qc-row" id="${rowId}">
                    <div class="qc-row-header">
                        <span class="qc-row-number"><i class="bi bi-list-ol"></i> Lệnh #${qcRowCounter}</span>
                        <button class="remove-qc-row" onclick="removeQCRow('${rowId}')" ${qcRowCounter === 1 ? 'style="display:none"' : ''}>×</button>
                    </div>
                    <div class="mb-2">
                        <label class="form-label"><i class="bi bi-barcode"></i> Mã lệnh *</label>
                        <input type="text" class="form-control qc-lenh" data-row="${rowId}" placeholder="Nhập hoặc tìm mã lệnh" required>
                        <div class="suggest-box" id="suggest-${rowId}"></div>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-2">
                            <label class="form-label"><i class="bi bi-check-circle"></i> SL Đạt *</label>
                            <input type="number" class="form-control qc-dat" data-row="${rowId}" placeholder="SL Đạt" required>
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label"><i class="bi bi-exclamation-circle"></i> SL Hư</label>
                            <input type="number" class="form-control qc-hu" data-row="${rowId}" placeholder="SL Hư" value="0">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label"><i class="bi bi-chat-dots"></i> Ghi chú</label>
                        <input type="text" class="form-control qc-ghi-chu" data-row="${rowId}" placeholder="Ghi chú cho lệnh này">
                    </div>
                </div>
            `;

            document.getElementById('qcRowsContainer').insertAdjacentHTML('beforeend', rowHTML);

            const lenhInput = document.querySelector(`input[data-row="${rowId}"].qc-lenh`);
            const suggestDiv = document.getElementById(`suggest-${rowId}`);

            let searchTimer;
            lenhInput.addEventListener('input', function() {
                const keyword = this.value.trim();
                clearTimeout(searchTimer);

                if (keyword.length < 2) {
                    suggestDiv.innerHTML = '';
                    return;
                }

                searchTimer = setTimeout(async () => {
                    const res = await fetch(
                        `{{ route('lenh-sx.search') }}?q=${encodeURIComponent(keyword)}`);
                    const data = await res.json();

                    if (data.length === 0) {
                        suggestDiv.innerHTML =
                            "<div class='p-2 text-muted'>Không tìm thấy mã lệnh</div>";
                        return;
                    }

                    suggestDiv.innerHTML = data.map(item =>
                        `<div class='suggest-item' data-value='${item.ma_lenh}'><b>${item.ma_lenh}</b> - ${item.description||''}</div>`
                    ).join('');

                    suggestDiv.querySelectorAll('.suggest-item').forEach(it => {
                        it.onclick = () => {
                            lenhInput.value = it.dataset.value;
                            suggestDiv.innerHTML = '';
                        };
                    });
                }, 400);
            });
        }

        window.removeQCRow = function(rowId) {
            document.getElementById(rowId).remove();
        }

        document.getElementById('addQCRow').onclick = () => {
            addQCRow();
        };

        document.getElementById('confirmQCBtn').onclick = () => {
            const rows = document.querySelectorAll('.qc-row');
            const qcData = [];

            for (let row of rows) {
                const rowId = row.id;
                const lenh = document.querySelector(`input[data-row="${rowId}"].qc-lenh`).value;
                const dat = document.querySelector(`input[data-row="${rowId}"].qc-dat`).value;
                const hu = document.querySelector(`input[data-row="${rowId}"].qc-hu`).value;
                const ghichu = document.querySelector(`input[data-row="${rowId}"].qc-ghi-chu`).value;

                if (!lenh || !dat) {
                    alert('Vui lòng điền đầy đủ Mã lệnh và SL Đạt cho tất cả các dòng!');
                    return;
                }

                qcData.push({
                    lenh_sx: lenh,
                    so_luong_dat: dat,
                    so_luong_loi: hu || 0,
                    dien_giai: ghichu || ''
                });
            }

            nhapData.nhan_vien_id = document.getElementById('nhanvienIdQC').value;
            nhapData.dien_giai = document.getElementById('dienGiaiQC').value;
            nhapData.qc_rows = qcData;

            if (!nhapData.nhan_vien_id) {
                alert('Vui lòng nhập Mã công nhân!');
                return;
            }

            let reviewHTML = `
                <b>Công đoạn:</b> ${nhapData.cong_doan}<br>
                <b>Mã nhân viên:</b> ${nhapData.nhan_vien_id}<br>
                <b>Ghi chú:</b> ${nhapData.dien_giai || '-'}<br>
                <hr>
                <b>Danh sách lệnh kiểm tra:</b><br>
            `;

            qcData.forEach((item, idx) => {
                reviewHTML += `
                    <div style="margin:10px 0; padding:10px; background:#f8f9fa; border-radius:5px;">
                        ${idx + 1}. <b>${item.lenh_sx}</b><br>
                        &nbsp;&nbsp;&nbsp;&nbsp;SL Đạt: ${item.so_luong_dat} | SL Hư: ${item.so_luong_loi}
                    </div>
                `;
            });

            document.getElementById('reviewBox').innerHTML = reviewHTML;
            showStep('step4');
        };

        let isSubmitting = false; // 🚫 Prevent double submit

        document.getElementById('submitBtn').onclick = async () => {
            // ✋ Kiểm tra đang submit rồi
            if (isSubmitting) return;

            const formData = new FormData();
            const alertBox = document.getElementById('alertBox');
            const submitBtn = document.getElementById('submitBtn');
            const khuVuc = document.getElementById('khuVucSelect').value;

            // Validation
            if (!khuVuc) {
                showAlert({
                    icon: 'warning',
                    title: 'Chưa chọn khu vực!',
                    text: 'Vui lòng chọn khu vực in trước khi lưu.',
                });
                return;
            }

            // Disable button & set flag
            isSubmitting = true;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Đang lưu...';

            try {
                // Check if this is QC multi-row submission
                if (nhapData.qc_rows && nhapData.qc_rows.length > 0) {
                    // QC Multi-row submission
                    formData.append('cong_doan', nhapData.cong_doan);
                    formData.append('nhan_vien_id', nhapData.nhan_vien_id);
                    formData.append('dien_giai', nhapData.dien_giai || '');
                    formData.append('qc_rows', JSON.stringify(nhapData.qc_rows));
                    formData.append('is_qc_multi', '1');
                    formData.append('khu_vuc', khuVuc);

                    alertBox.innerHTML =
                        `<div class='alert alert-info'>Đang lưu ${nhapData.qc_rows.length} lệnh QC...</div>`;
                } else {
                    // Normal single submission
                    for (const k in nhapData) {
                        if (k !== 'qc_rows') formData.append(k, nhapData[k]);
                    }
                    formData.append('khu_vuc', khuVuc);
                    alertBox.innerHTML = `<div class='alert alert-info'>Đang lưu thông tin...</div>`;
                }

                const res = await fetch('{{ route('nhap-sx.submit') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    // 🖨️ GỌI IN NGAY cho normal flow
                    if (data.data.id && !nhapData.qc_rows) {
                        await fetch(`/nhap-sx/${data.data.id}/print-direct`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                khu_vuc: khuVuc
                            })
                        });
                    }

                    const successMessage = nhapData.qc_rows ?
                        `Đã lưu ${nhapData.qc_rows.length} lệnh QC thành công!` :
                        `Phiếu số: <b>${data.data.id}</b>`;

                    showAlert({
                        icon: 'success',
                        title: '✅ ĐÃ LƯU THÀNH CÔNG',
                        html: `
                        <div style="font-size:18px;margin-top:10px">
                            ${successMessage}
                        </div>
                        <div style="margin-top:15px;font-size:14px;color:#666">
                            ${nhapData.qc_rows ? '✓ Các phiếu QC đã được in tự động.' : '✓ Gặp Quản lý sản xuất hoặc Tiến để in phiếu sản xuất.'}
                        </div>
                    `,
                        confirmButtonText: "ĐÓNG",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        allowEnterKey: false
                    }).then(() => {
                        document.body.innerHTML = `<div style="padding:30px;font-size:18px;text-align:center;background: linear-gradient(135deg, var(--primary) 0%, #5b21b6 100%); color: white; min-height: 100vh; display: flex; align-items: center; justify-content: center;">
                        
                        <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15); color: var(--text-dark);">
                            <div style="font-size: 3rem; margin-bottom: 20px;">✅</div>
                            <div style="font-weight: 700; margin-bottom: 15px;">NHẮN ANH THÁI HOẶC TIẾN SỐ <b style="color: var(--primary);">${data.data.id}</b></div>
                            <div style="font-size: 0.95rem; color: var(--text-gray);">ĐỂ IN PHIẾU SẢN XUẤT KHỎI GHI TAY</div>
                            <div style="margin-top: 20px; font-size: 0.9rem; color: var(--text-gray);">Bạn có thể đóng trang.</div>
                        </div>
                    </div>`;
                    });
                } else {
                    showAlert({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message,
                    });
                }

                // ✅ Reset button state
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '✅ LƯU PHIẾU';
            } catch (e) {
                console.error('Error:', e);
                showAlert({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra: ' + e.message,
                });
                isSubmitting = false;
                submitBtn.disabled = false;
                submitBtn.innerHTML = '✅ LƯU PHIẾU';
            }
        };

        /* ====================== IMPORT ====================== */
        document.getElementById('importForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const result = document.getElementById('importResult');
            result.innerHTML = `<div class='alert alert-info'>⏳ Đang import...</div>`;
            const res = await fetch('{{ route('lenh-sx.import') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                result.innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
                setTimeout(() => location.reload(), 1000);
            } else result.innerHTML = `<div class='alert alert-danger'>${data.message}</div>`;
        });

        @if (!empty($ma_lenh_url))
            nhapData.lenh_sx = "{{ $ma_lenh_url }}";
            overlay.classList.add('show');
            showStep("step2");
            document.getElementById('searchLenh').value = "{{ $ma_lenh_url }}";
        @endif

        /* 🎤 SPEECH TO TEXT (VI TIẾNG VIỆT) */
        let recognizing = false;
        let recognition;

        if ("webkitSpeechRecognition" in window) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = "vi-VN";
            recognition.continuous = true;
            recognition.interimResults = true;

            recognition.onstart = function() {
                recognizing = true;
                document.getElementById("micStatus").innerHTML = "<i class='bi bi-mic-fill'></i> Đang nghe...";
                document.getElementById("micBtn").style.background = "var(--success)";
            };

            recognition.onend = function() {
                recognizing = false;
                document.getElementById("micStatus").innerHTML = "";
                document.getElementById("micBtn").style.background = "var(--danger)";
            };

            recognition.onresult = function(event) {
                const input = document.getElementById("dienGiai");
                let interimText = "";
                let finalText = "";

                for (let i = event.resultIndex; i < event.results.length; i++) {
                    const transcript = event.results[i][0].transcript;
                    if (event.results[i].isFinal) {
                        finalText += transcript + " ";
                    } else {
                        interimText += transcript;
                    }
                }

                if (finalText) {
                    input.value = input.value ? input.value.trim() + " " + finalText.trim() : finalText
                        .trim();
                }

                if (interimText) {
                    document.getElementById("micStatus").innerHTML = `<i class='bi bi-mic-fill'></i> Đang nghe: <i>${interimText}</i>`;
                } else if (recognizing) {
                    document.getElementById("micStatus").innerHTML = "<i class='bi bi-mic-fill'></i> Đang nghe...";
                }
            };

            recognition.onerror = function(event) {
                console.error("Speech recognition error:", event.error);
                if (event.error === 'no-speech') {
                    document.getElementById("micStatus").innerHTML = "<i class='bi bi-exclamation-circle'></i> Không nghe thấy giọng nói";
                }
            };
        } else {
            document.getElementById("micStatus").innerHTML =
                "Máy không hỗ trợ nhận diện giọng nói.";
        }

        document.getElementById("micBtn").onclick = function() {
            if (!recognition) return;

            if (!recognizing) {
                recognition.start();
            } else {
                recognition.stop();
            }
        };

        // Xử lý STEP 3 Normal form
        document.getElementById('confirmBtn').onclick = () => {
            nhapData.so_luong_dat = document.getElementById('soLuongDat').value;
            nhapData.so_luong_loi = document.getElementById('soLuongLoi').value;
            nhapData.dien_giai = document.getElementById('dienGiai').value;
            nhapData.nhan_vien_id = document.getElementById('nhanvienId').value;
            nhapData.may_sx = document.getElementById('maySx').value;
            nhapData.so_pick = document.getElementById('soPick').value;
            nhapData.so_cuon = document.getElementById('soCuon').value;
            nhapData.so_dong = document.getElementById('soDong').value;
            nhapData.so_ban = document.getElementById('soBan').value;
            nhapData.so_dau = document.getElementById('soDau').value;
            nhapData.so_khuon = document.getElementById('soKhuon').value;
            nhapData.khuon_sx = document.getElementById('khuonSx').value;

            document.getElementById('reviewBox').innerHTML = `
                <b>Mã lệnh:</b> ${nhapData.lenh_sx}<br>
                <b>Công đoạn:</b> ${nhapData.cong_doan}<br>
                <b>Số lượng đạt:</b> ${nhapData.so_luong_dat}<br>
                <b>Số lượng lỗi:</b> ${nhapData.so_luong_loi || 0}<br>
                <b>Diễn giải:</b> ${nhapData.dien_giai || '-'} <br>
                <b>Mã nhân viên:</b> ${nhapData.nhan_vien_id || '-'}
            `;
            showStep('step4');
        };
    </script>
</body>

</html>
