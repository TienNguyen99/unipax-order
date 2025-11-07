<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Danh sách nhập liệu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <h3>📑 Dữ liệu nhập gần nhất</h3>
        <table class="table table-bordered table-sm mt-3">
            <thead class="table-secondary">
                <tr>
                    <th>Ngày nhập</th>
                    <th>Lệnh SX</th>
                    <th>Công đoạn</th>
                    <th>Máy SX</th>
                    <th>SL đạt</th>
                    <th>SL lỗi</th>
                    <th>Diễn giải</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr>
                        <td>{{ $row->ngay_nhap }}</td>
                        <td>{{ $row->lenh_sx }}</td>
                        <td>{{ $row->cong_doan }}</td>
                        <td>{{ $row->may_sx }}</td>
                        <td>{{ $row->so_luong_dat }}</td>
                        <td>{{ $row->so_luong_loi }}</td>
                        <td>{{ $row->dien_giai }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
