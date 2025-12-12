<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>PHIẾU SẢN XUẤT CÔNG NHÂN</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .main-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            margin: 0 auto;
        }

        .header-title {
            text-align: center;
            color: #667eea;
            font-weight: 700;
            margin-bottom: 25px;
        }

        .btn-main {
            width: 100%;
            padding: 20px;
            font-size: 1.3rem;
            font-weight: 600;
            border-radius: 10px;
            margin-top: 15px;
        }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            overflow-y: auto;
            padding: 20px;
            z-index: 1000;
        }

        .overlay.show {
            display: block;
        }

        .modal-content-custom {
            background: white;
            border-radius: 15px;
            padding: 25px;
            max-width: 500px;
            margin: 20px auto;
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.5rem;
            border: none;
            background: none;
            cursor: pointer;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
        }

        .step-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #667eea;
        }

        .work-card {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            margin-bottom: 10px;
            font-weight: 600;
            background: white;
        }

        .work-card:hover {
            border-color: #667eea;
            background: #f8f9ff;
        }

        .work-card:active {
            transform: scale(0.98);
            background: #667eea;
            color: white;
        }

        .section-label {
            font-weight: 700;
            color: #764ba2;
            margin-top: 20px;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .suggest-box {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-top: 10px;
        }

        .suggest-item {
            padding: 12px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }

        .suggest-item:hover {
            background: #f8f9ff;
        }

        .suggest-item:last-child {
            border-bottom: none;
        }

        .form-control,
        .form-select {
            padding: 12px;
            font-size: 1rem;
            border-radius: 8px;
        }

        .btn-group-custom {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-group-custom .btn {
            flex: 1;
        }

        #reviewBox {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <div class="main-card">
        <h3 class="header-title">🏭 PHIẾU SẢN XUẤT CÔNG NHÂN</h3>

        <!-- Import Section -->
        <div class="mb-4">
            <h5 class="text-secondary mb-3">📁 Import Lệnh Sản Xuất</h5>
            <form id="importForm" enctype="multipart/form-data">
                <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" class="form-control mb-2"
                    required>
                <button type="submit" class="btn btn-primary w-100">📤 Import Excel</button>
            </form>
            <div id="importResult" class="mt-2"></div>
        </div>

        <hr>

        <button id="touchBtn" class="btn btn-success btn-main">
            ✏️ NHẬP SẢN XUẤT
        </button>
    </div>

    <!-- Overlay Modal -->
    <div id="overlay" class="overlay">
        <div class="modal-content-custom">
            <button class="close-btn" id="closeBtn">&times;</button>

            <!-- STEP 1: Tìm mã lệnh -->
            <div id="step1" class="step active">
                <h4 class="step-title">🔍 Bước 1: Tìm Mã Lệnh</h4>

                <input type="text" id="searchLenh" placeholder="Nhập mã lệnh..." class="form-control mb-3">
                <div id="suggestBox" class="suggest-box"></div>

                <button class="btn btn-warning w-100 mt-3" id="scanQRBtn">📷 Quét Mã QR</button>

                <div id="qrReader" style="width:100%; display:none;" class="mt-3"></div>
                <button class="btn btn-secondary w-100 mt-2" id="stopScanBtn" style="display:none;">🛑 Dừng
                    Quét</button>
            </div>

            <!-- STEP 2: Chọn công việc -->
            <div id="step2" class="step">
                <h4 class="step-title">⚙️ Bước 2: Chọn Công Việc</h4>

                <div class="section-label">🏢 Tầng Trệt</div>
                <div class="row">
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="DỆT DÂY">DỆT DÂY</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="DỆT NHÃN">DỆT NHÃN</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="QUẤN CUỘN">QUẤN CUỘN</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="THUN BẢN">THUN BẢN</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="BẾ TPU">BẾ TPU</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="QUAY ĐẦU">QUAY ĐẦU</div>
                    </div>
                    <div class="col-12">
                        <div class="work-card congdoan" data-value="CÔNG VIỆC KHÁC">CÔNG VIỆC KHÁC</div>
                    </div>
                </div>

                <div class="section-label">🏢 Tầng 1</div>
                <div class="row">
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="IN LỤA">IN LỤA</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="IN TRỤC">IN TRỤC</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="ĐÚC">ĐÚC</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="CẮT">CẮT</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="ÉP">ÉP</div>
                    </div>
                </div>

                <div class="section-label">✅ QC</div>
                <div class="row">
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="KIỂM HÀNG">KIỂM HÀNG</div>
                    </div>
                    <div class="col-6">
                        <div class="work-card congdoan" data-value="ĐÓNG GÓI">ĐÓNG GÓI</div>
                    </div>
                </div>

                <button class="btn btn-secondary w-100 mt-4" id="back1">↩ Quay Lại</button>
            </div>

            <!-- STEP 3: Nhập thông tin -->
            <div id="step3" class="step">
                <h4 class="step-title">📝 Bước 3: Nhập Thông Tin</h4>

                <div class="mb-3">
                    <label class="form-label fw-bold">Mã (tên) công nhân *</label>
                    <input type="text" id="nhanvienId" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Số lượng đạt *</label>
                    <input type="number" id="soLuongDat" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Số lượng lỗi</label>
                    <input type="number" id="soLuongLoi" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Ghi chú</label>
                    <input type="text" id="dienGiai" class="form-control">
                </div>

                <button id="toggleExtra" class="btn btn-outline-primary w-100 mb-3">
                    ➕ Thêm Thông Tin Chi Tiết
                </button>

                <div id="extraFields" style="display:none;">
                    <div class="mb-3">
                        <label class="form-label">Máy sản xuất</label>
                        <input type="text" id="maySx" class="form-control">
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
                    <button class="btn btn-secondary" id="back2">↩ Quay Lại</button>
                    <button class="btn btn-success" id="confirmBtn">✅ Tiếp Tục</button>
                </div>
            </div>

            <!-- STEP 4: Xác nhận -->
            <div id="step4" class="step">
                <h4 class="step-title">✅ Bước 4: Xác Nhận</h4>

                <div id="reviewBox" class="mb-3"></div>
                <div id="alertBox" class="mb-3"></div>

                <div class="btn-group-custom">
                    <button class="btn btn-secondary" id="back3">↩ Sửa</button>
                    <button class="btn btn-primary" id="submitBtn">💾 Lưu</button>
                </div>
            </div>
        </div>
    </div>

    <script>
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
                document.getElementById("toggleExtra").innerHTML = "➖ Ẩn Thông Tin Chi Tiết";
            } else {
                extra.style.display = "none";
                document.getElementById("toggleExtra").innerHTML = "➕ Thêm Thông Tin Chi Tiết";
            }
        };

        document.querySelectorAll('.congdoan').forEach(btn => {
            btn.onclick = () => {
                nhapData.cong_doan = btn.dataset.value;
                showStep('step3');
            };
        });

        document.getElementById('back1').onclick = () => showStep('step1');
        document.getElementById('back2').onclick = () => showStep('step2');
        document.getElementById('back3').onclick = () => showStep('step3');

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

        document.getElementById('submitBtn').onclick = async () => {
            const formData = new FormData();
            for (const k in nhapData) formData.append(k, nhapData[k]);
            const alertBox = document.getElementById('alertBox');
            alertBox.innerHTML = `<div class='alert alert-info'>⏳ Đang lưu...</div>`;

            const res = await fetch('{{ route('nhap-sx.submit') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            });
            const data = await res.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'ĐÃ LƯU THÀNH CÔNG',
                    html: `
                        <div style="font-size:20px;margin-top:10px">
                            Phiếu số: <b>${data.data.id}</b>
                        </div>
                        <div style="margin-top:15px;font-size:16px;color:#666">
                            Gặp Quản lý sản xuất hoặc Tiến để in phiếu sản xuất.
                        </div>
                    `,
                    confirmButtonText: "ĐỒNG Ý",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    allowEnterKey: false
                }).then(() => {
                    document.body.innerHTML = `<div style="padding:30px;font-size:22px;text-align:center">
                        <b>Phiếu đã lưu thành công.</b><br>
                        NHẮN ANH THÁI HOẶC TIẾN SỐ <b>${data.data.id}</b> ĐỂ IN PHIẾU SẢN XUẤT KHỎI GHI TAY NHA!<br><br>
                        Bạn có thể đóng trang.
                    </div>`;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: data.message,
                });
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
    </script>
</body>

</html>
