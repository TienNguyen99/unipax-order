<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách nhập liệu</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery + DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.3.1/css/rowGroup.dataTables.min.css">

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/rowgroup/1.3.1/js/dataTables.rowGroup.min.js"></script>

    <!-- Particles.js -->
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

    <style>
        /* Hiệu ứng nền pastel */
        #particles-js {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #fcecff, #e4f0ff, #e8ffe4);
            background-size: 300% 300%;
            animation: pastelMove 20s ease infinite;
        }

        @keyframes pastelMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Làm bảng nhìn nổi và dịu hơn */
        .container {
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(6px);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        table.dataTable tbody tr {
            background-color: rgba(255,255,255,0.6) !important;
        }
    </style>

</head>

<body>

    <!-- Layer particle -->
    <div id="particles-js"></div>

    <div class="container mt-4">
        <h3>BẢNG PHIẾU SẢN XUẤT CÔNG NHÂN</h3>

        <table class="table table-bordered table-sm mt-3" id="data-table">
            <thead class="table-secondary">
                <tr>
                    <th>ID</th>
                    <th>Ngày nhập phiếu</th>
                    <th>Lệnh SX</th>
                    <th>Công đoạn</th>
                    <th>Công nhân</th>
                    <th>SL đạt</th>
                    <th>SL lỗi</th>
                    <th>Diễn giải</th>
                    <th>Thao tác</th>
                    <th>Phiếu</th>
                    <th>Ngày nhóm</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>


    <!-- Pastel Particle Config -->
    <script>
        particlesJS("particles-js", {
            particles: {
                number: { value: 60, density: { enable: true, value_area: 800 } },
                color: { value: ["#ffb3ba", "#baffc9", "#bae1ff", "#ffffba"] },
                shape: { type: "circle" },
                opacity: { value: 0.6, random: true },
                size: { value: 6, random: true },
                line_linked: {
                    enable: true,
                    distance: 120,
                    color: "#cccccc",
                    opacity: 0.4,
                    width: 1
                },
                move: {
                    enable: true,
                    speed: 2,
                    direction: "none",
                    random: true,
                    out_mode: "out"
                }
            },
            interactivity: {
                detect_on: "canvas",
                events: {
                    onhover: { enable: true, mode: "grab" },
                    onclick: { enable: true, mode: "repulse" }
                }
            },
            retina_detect: true
        });
    </script>


    <!-- DataTable + Fetch logic -->
    <script>
        let dataTable;

        async function fetchLatestData() {
            try {
                const response = await fetch("{{ route('api.nhap-sx.latest') }}");
                const data = await response.json();

                const rows = data.map(row => {
                    const ngayNhapFull = row.created_at
                        ? new Date(row.created_at).toLocaleString('vi-VN', {
                            timeZone: 'Asia/Ho_Chi_Minh',
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit',
                            hour12: false
                        })
                        : "";

                    const groupDate = row.created_at
                        ? new Date(row.created_at).toLocaleString('vi-VN', {
                            timeZone: 'Asia/Ho_Chi_Minh',
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour12: false
                        })
                        : "";

                    const inButton = `<button class="btn btn-sm btn-primary" onclick="printSX(${row.id})">In</button>`;

                    const daInHtml = row.da_in == 1
                        ? '<span style="color:green;font-weight:bold;">&#10004;</span>'
                        : '<span style="color:red;font-weight:bold;">&#10006;</span>';

                    return [
                        row.id,
                        ngayNhapFull,
                        row.lenh_sx,
                        row.cong_doan,
                        row.nhan_vien_id ?? "",
                        row.so_luong_dat,
                        row.so_luong_loi ?? "",
                        row.dien_giai ?? "",
                        inButton,
                        daInHtml,
                        groupDate
                    ];
                });

                if (dataTable) {
                    dataTable.clear().rows.add(rows).draw(false);
                } else {
                    initDataTable(rows);
                }

            } catch (err) {
                console.error("Lỗi fetch:", err);
            }
        }

        function initDataTable(rows) {
            dataTable = $("#data-table").DataTable({
                data: rows,
                columnDefs: [
                    { targets: 10, visible: false }
                ],
                rowGroup: {
                    dataSrc: 10
                },
                order: [[10, "desc"]],
                pageLength: 50
            });
        }

        async function printSX(id, force = false) {
    try {
        const response = await fetch(`/nhap-sx/${id}/print?force=${force}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        });

        const result = await response.json();

        if (result.confirm) {
            if (confirm(result.message)) printSX(id, true);
            return;
        }

        // Thành công → mở PDF, KHÔNG báo message
        if (result.success) {
            if (result.pdf_url) window.open(result.pdf_url, "_blank");
        } else {
            // Chỉ báo lỗi nếu có
            alert(result.message ?? "Không thể in phiếu");
        }

    } catch (err) {
        alert("Lỗi khi in!");
    }
}


        fetchLatestData();
        setInterval(fetchLatestData, 10000);
    </script>

</body>
</html>
