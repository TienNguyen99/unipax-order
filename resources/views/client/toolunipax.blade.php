<!doctype html>
<html lang="vi">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Phiếu kho Unipax</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container py-3">

    <h4 class="text-center mb-3 fw-bold text-primary">📦 Nhập phiếu kho (SQLite)</h4>

    @if (session('success'))
        <div class="alert alert-success text-center">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('phieuunipax.store') }}" class="card p-3 mb-3">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-bold">P/S</label>
            <input list="psOptions" name="ps" class="form-control" required>
            <datalist id="psOptions">
                @foreach ($psList as $ps)
                    <option value="{{ $ps }}">
                @endforeach
            </datalist>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Số đạt</label>
            <input type="number" name="dat" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold text-danger">Số lỗi</label>
            <input type="number" name="loi" class="form-control text-danger" required>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Ghi chú</label>
            <input type="text" name="ghichu" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary w-100">💾 Lưu phiếu</button>
    </form>

    <div class="text-end mb-2">
        <button id="btnViewAll" class="btn btn-outline-primary btn-sm">📋 Xem tất cả</button>
    </div>

    <div id="listArea" style="display:none;">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>P/S</th>
                        <th>Đạt</th>
                        <th>Lỗi</th>
                        <th>Ghi chú</th>
                        <th>Ngày nhập</th>
                        <th>Xóa</th>
                    </tr>
                </thead>
                <tbody id="tableBody"></tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('btnViewAll').addEventListener('click', () => {
            fetch('/phieu-nhap/view-all')
                .then(r => r.json())
                .then(data => {
                    const area = document.getElementById('listArea');
                    const body = document.getElementById('tableBody');
                    body.innerHTML = '';
                    if (data.length === 0) {
                        body.innerHTML =
                            '<tr><td colspan="7" class="text-center text-muted">Chưa có dữ liệu</td></tr>';
                        area.style.display = 'block';
                        return;
                    }
                    data.forEach(item => {
                        body.innerHTML += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.ps}</td>
                        <td>${item.dat}</td>
                        <td>${item.loi}</td>
                        <td>${item.ghichu ?? ''}</td>
                        <td>${item.ngaynhap ?? ''}</td>
                        <td><button class="btn btn-danger btn-sm" onclick="deleteRow(${item.id})">🗑</button></td>
                    </tr>`;
                    });
                    area.style.display = 'block';
                });
        });

        function deleteRow(id) {
            if (!confirm('Xóa dòng này?')) return;
            fetch(`/phieu-nhap/delete/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => location.reload());
        }
    </script>

</body>

</html>
