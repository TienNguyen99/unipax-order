<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Duyệt lệnh sản xuất</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .content {
            padding: 30px;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: block;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: block;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        table tr:hover {
            background: #f9f9f9;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-approved {
            background: #d4edda;
            color: #155724;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-success:hover {
            background: #218838;
            transform: translateY(-2px);
        }

        .btn-info {
            background: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background: #138496;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .modal-header h2 {
            margin: 0;
            color: #333;
        }

        .close-btn {
            background: none;
            border: none;
            font-size: 28px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s;
        }

        .close-btn:hover {
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102, 126, 234, 0.3);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #667eea;
            text-decoration: none;
            transition: color 0.3s;
        }

        .back-link:hover {
            color: #5568d3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📋 DUYỆT LỆNH SẢN XUẤT</h1>
            <p>Quản lý duyệt và ký các lệnh sản xuất</p>
        </div>

        <div class="content">
            <a href="{{ route('excel') }}" class="back-link">← Quay lại in lệnh</a>

            @if (session('success'))
                <div class="alert success">✓ {{ session('success') }}</div>
            @endif

            @if ($logs->count() > 0)
                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 20%">Tên Sheet</th>
                                <th style="width: 15%">Người In</th>
                                <th style="width: 15%">Thời Gian In</th>
                                <th style="width: 15%">Trạng Thái</th>
                                <th style="width: 15%">Duyệt Bởi</th>
                                <th style="width: 20%">Hành Động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                // Đếm số lần mỗi sheet xuất hiện
                                $sheetCounts = $logs->groupBy('sheet_name')->map->count();
                            @endphp
                            @foreach ($logs as $log)
                                @php
                                    $isDuplicate = $sheetCounts[$log->sheet_name] > 1;
                                @endphp
                                <tr
                                    style="{{ $isDuplicate ? 'background-color: #fff3cd; border-left: 4px solid #ffc107;' : '' }}">
                                    <td>
                                        <strong>{{ $log->sheet_name }}</strong>
                                        @if ($isDuplicate)
                                            <span
                                                style="background: #ffc107; color: #000; padding: 2px 6px; border-radius: 3px; font-size: 11px; margin-left: 5px;">⚠️
                                                Trùng</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->printed_by }}</td>
                                    <td>{{ $log->printed_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if ($log->is_approved)
                                            <span class="status-badge status-approved">✓ Đã duyệt</span>
                                        @else
                                            <span class="status-badge status-pending">⏳ Chờ duyệt</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->approved_by)
                                            <strong>{{ $log->approved_by }}</strong>
                                            <br><small>{{ $log->approved_at->format('d/m/Y H:i') }}</small>
                                        @else
                                            <span style="color: #999;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($log->pdf_path)
                                            <a href="{{ asset($log->pdf_path) }}" target="_blank" class="btn btn-info"
                                                style="margin-right: 5px;">👁️ Xem</a>
                                        @endif
                                        @if (!$log->is_approved)
                                            <button class="btn btn-success"
                                                onclick="openApprovalModal({{ $log->id }}, '{{ $log->sheet_name }}')"
                                                style="margin-right: 5px;">✓ Duyệt</button>
                                        @else
                                            <span style="color: #28a745; margin-right: 5px;">Đã ký</span>
                                        @endif
                                        <button class="btn btn-danger"
                                            onclick="deleteLog({{ $log->id }}, '{{ $log->sheet_name }}')">🗑️
                                            Xóa</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <p>Chưa có lệnh nào cần duyệt</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Duyệt & Ký -->
    <div id="approvalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>✍️ Duyệt & Ký Lệnh</h2>
                <button class="close-btn" onclick="closeApprovalModal()">&times;</button>
            </div>

            <form id="approvalForm" method="POST">
                @csrf
                <div class="form-group">
                    <label>Lệnh: <span id="sheetName" style="color: #667eea; font-weight: 700;"></span></label>
                </div>

                <div class="form-group">
                    <label for="signature">Chữ Ký / Ghi Chú *</label>
                    <textarea id="signature" name="signature"
                        placeholder="Nhập chữ ký hoặc ghi chú duyệt (ví dụ: Đã duyệt, Phê chuẩn, v.v.)" required></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" onclick="closeApprovalModal()">Hủy</button>
                    <button type="submit" class="btn btn-success">✓ Xác Nhận Duyệt</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApprovalModal(logId, sheetName) {
            document.getElementById('sheetName').textContent = sheetName;
            document.getElementById('approvalForm').action = `/print/approve/${logId}`;
            document.getElementById('approvalModal').style.display = 'block';
        }

        function closeApprovalModal() {
            document.getElementById('approvalModal').style.display = 'none';
            document.getElementById('signature').value = '';
        }

        function deleteLog(logId, sheetName) {
            if (confirm(`Bạn chắc chắn muốn xóa lệnh "${sheetName}" không?\n\nHành động này không thể hoàn tác!`)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/print/delete/${logId}`;
                form.innerHTML = `
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Đóng modal khi click ngoài
        window.onclick = function(event) {
            const modal = document.getElementById('approvalModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>

</html>
