<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Theo dõi Đơn hàng Unipax</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- DataTables Buttons CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <style>
        td,
        th {
            font-size: 13px;
            vertical-align: middle;
        }

        .text-danger {
            color: #dc3545;
            font-weight: bold;
        }

        .text-success {
            color: #28a745;
            font-weight: bold;
        }

        .text-warning {
            color: #ffc107;
            font-weight: bold;
        }

        .text-primary {
            color: #007bff;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container-fluid mt-4">
        <h3 class="mb-4">📋 Theo dõi đơn sản xuất - Realtime</h3>

        <!-- 🔍 Bộ lọc -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label for="filterKhachHang" class="form-label">Khách hàng</label>
                <input type="text" class="form-control" id="filterKhachHang" placeholder="Nhập tên khách hàng">
            </div>
            <div class="col-md-3">
                <label for="filterMaHH" class="form-label">Mã HH</label>
                <input type="text" class="form-control" id="filterMaHH" placeholder="Nhập mã hàng hóa">
            </div>
            <div class="col-md-3">
                <label for="filterTinhTrang" class="form-label">Tình trạng</label>
                <select class="form-select" id="filterTinhTrang">
                    <option value="">Tất cả</option>
                    <option value="✔️ Hoàn thành">✔️ Hoàn thành</option>
                    <option value="📦 Chưa xuất kho">📦 Chưa xuất kho</option>
                    <option value="⛔ Chưa nhập kho">⛔ Chưa nhập kho</option>
                    <option value="📦 Chưa đủ số lượng">📦 Chưa đủ số lượng</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="filterNgayGiao" class="form-label">Tháng giao</label>
                <input type="month" class="form-control" id="filterNgayGiao">
            </div>
            <div class="col-md-3">
                <label for="filterLenhSanXuat" class="form-label">Lệnh sản xuất</label>
                <input type="text" class="form-control" id="filterLenhSanXuat" placeholder="Nhập lệnh sản xuất">
            </div>
            <div class="col-md-12 mt-2 text-end">
                <button class="btn btn-secondary" id="clearFilters">🧹 Xóa bộ lọc</button>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="productionTable" style="width: 100%;">
            <thead class="table-dark">
                <tr>
                    <th>STT</th>
                    <th>Ngày xuất</th>
                    <th>Mã hàng</th>
                    <th>P/S</th>
                    <th>Size</th>
                    <th>Ngày gửi Panel</th>
                    <th>Số phiếu</th>
                    <th>Số lượng đơn hàng</th>
                    <th>Số lượng thực nhận</th>
                    <th>Ngày giao</th>

                    <th>Đạt</th>
                    <th>Lỗi</th>
                    <th>Ghi chú</th>
                    <th>Panel</th>
                    <th>Loại</th>
                    <th>Nơi giao</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        let dataTable;

        function fetchData() {
            fetch("http://192.168.1.14:8888/api/production-orders")
                .then(res => res.json())
                .then(response => {
                    const {
                        data
                    } = response;

                    const rows = data.map((row, index) => {
                        const key = `${row.So_ct}|${row.Ma_hh}`;

                        const tongvnd = row.gia * row.sl_dat;

                        return [
                            index + 1,
                            row.ngay_xuat || '',
                            row.ma_hang || '',
                            row.ps_code || '',
                            row.size || '',
                            row.ngay_gui_panel || '',
                            row.so_phieu || '',
                            row.sl_dat || 0,
                            row.sl_thuc_nhan || 0,
                            row.ngay_giao || '',
                            row.sl_giao_dat || 0,
                            row.sl_giao_loi || 0,
                            row.ghi_chu || '',
                            row.panel || '',
                            row.loai || '',
                            row.noi_giao || '',


                        ];
                    });

                    if (!dataTable) {
                        dataTable = $('#productionTable').DataTable({
                            data: rows,
                            columns: Array(16).fill().map((_, i) => ({
                                title: $('thead th').eq(i).text()
                            })),
                            pageLength: 25,
                            language: {
                                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/vi.json'
                            },
                            dom: 'Bfrtip',
                            buttons: [{
                                extend: 'excelHtml5',
                                text: '📤 Xuất Excel',
                                className: 'btn btn-success',
                                exportOptions: {
                                    columns: ':visible'
                                },
                                title: 'Bang_Lenh_San_Xuat',
                            }]
                        });

                        $('#filterKhachHang, #filterMaHH, #filterTinhTrang, #filterNgayGiao,#filterLenhSanXuat').on(
                            'input change',
                            function() {
                                dataTable.draw();
                            });
                        $('#clearFilters').on('click', function() {
                            $('#filterKhachHang').val('');
                            $('#filterMaHH').val('');
                            $('#filterTinhTrang').val('');
                            $('#filterNgayGiao').val('');
                            $('#filterLenhSanXuat').val('');
                            dataTable.draw();
                        });

                        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                            const khachHang = $('#filterKhachHang').val().toLowerCase();
                            const maHH = $('#filterMaHH').val().toLowerCase();
                            const tinhTrang = $('#filterTinhTrang').val();
                            const ngayGiao = $('#filterNgayGiao').val();
                            const lenhSanXuat = $('#filterLenhSanXuat').val(); // yyyy-MM

                            const khachHangCol = data[4].toLowerCase();
                            const maHHCol = data[5].toLowerCase();
                            const tinhTrangCol = $('<div>').html(data[21]).text(); // get text without span
                            const ngayGiaoCol = data[14]; // dd/mm/yyyy
                            const lenhSanXuatCol = data[3];

                            if (khachHang && !khachHangCol.includes(khachHang)) return false;
                            if (maHH && !maHHCol.includes(maHH)) return false;
                            if (tinhTrang && !tinhTrangCol.includes(tinhTrang)) return false;
                            if (lenhSanXuat && !lenhSanXuatCol.includes(lenhSanXuat)) return false;

                            if (ngayGiao) {
                                const [day, month, year] = ngayGiaoCol.split('/');
                                const tableMonth = `${year}-${month.padStart(2, '0')}`;
                                if (!tableMonth.startsWith(ngayGiao)) return false;
                            }

                            return true;
                        });

                    } else {
                        dataTable.clear();
                        dataTable.rows.add(rows);
                        dataTable.draw(false);
                    }
                })
                .catch(err => {
                    console.error("Lỗi khi tải dữ liệu:", err);
                });
        }

        fetchData();
        setInterval(fetchData, 10000);
    </script>

    <!-- Buttons + JSZip (Excel) -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
    {{-- Script copy --}}
    <script>
        $(document).on('click', '.copy-text', function() {
            const text = $(this).data('text');
            const tempInput = document.createElement("input");
            document.body.appendChild(tempInput);
            tempInput.value = text;
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // For mobile
            document.execCommand("copy");
            document.body.removeChild(tempInput);

        });
    </script>
</body>

</html>
