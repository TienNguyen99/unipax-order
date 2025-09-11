<!DOCTYPE html>
<html>

<head>
    <title>In lệnh sản xuất</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-align: center;
            width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #45a049;
        }

        p {
            font-size: 14px;
        }

        /* ==== Modal ==== */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 4% auto;
            padding: 25px 30px;
            border-radius: 12px;
            width: 85%;
            max-width: 1100px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid-col {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            max-height: 350px;
            overflow-y: auto;
        }

        .year-header {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
            color: #333;
        }

        .grid-col ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .grid-col li {
            margin: 8px 0;
        }

        .grid-col a {
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
        }

        .grid-col a:hover {
            text-decoration: underline;
        }

        .close-btn {
            margin-top: 20px;
            background: #dc3545;
        }

        .close-btn:hover {
            background: #c82333;
        }
    </style>
</head>

<body>
    <div class="container">
        @if (!empty($fileUrls) && count($fileUrls) > 0)
            <!-- Nút mở modal -->
            <button type="button" onclick="document.getElementById('fileModal').style.display='block'">
                📂 Lệnh sản xuất cũ
            </button>

            <!-- Modal -->
            <div id="fileModal" class="modal" onclick="if(event.target.id==='fileModal'){this.style.display='none'}">
                <div class="modal-content">
                    <h3 style="margin-bottom:20px;">📂 CHỌN ĐỂ TẢI</h3>

                    @php
                        $grouped = [];
                        foreach ($fileUrls as $file) {
                            preg_match('/(20\d{2})/', $file['name'], $match);
                            $year = $match[1] ?? 'Khác';
                            $grouped[$year][] = $file;
                        }
                        krsort($grouped);
                    @endphp

                    <div class="grid-container">
                        @foreach ($grouped as $year => $files)
                            <div class="grid-col">
                                <div class="year-header">{{ $year }}</div>
                                <ul>
                                    @foreach ($files as $file)
                                        <li>📄 <a href="{{ $file['url'] }}" download>{{ $file['name'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                        <div class="grid-col">
                            <div class="year-header">🌐 Link Online</div>
                            <ul>
                                <li>🌐 <a href="https://1drv.ms/x/c/780111bcbe29311c/ERLvdNC4tVJCgWblmzYCY_UBLeY-9B1rI2qOdEOXCMY7AQ?e=hlQm5q" target="_blank">2025 Từ 0 - 999</a></li>
                                <li>🌐 <a href="https://1drv.ms/x/c/780111bcbe29311c/EbaocOYzzXxLncJAR5YUxhMBjAh3JxbHoBZtyxIrazoTYg?e=GjdP8K" target="_blank">2025 Từ 1000-1982</a></li>
                                <li>🌐 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/EdrZO6-SkGBNq-aorfBftHgB1YhK_g97KsBob3_PD0dXUQ?e=0S0Fv4"
                                        target="_blank">2024 Từ 2157 - 2999</a></li>
                                <li>🌐 <a href="https://1drv.ms/x/c/780111bcbe29311c/EWY_a4S9JAVAkhSqRKME9DwBBgnRm7T1mWYYt7FRTcPKzw?e=8mumah" target="_blank">2024 Từ 3000 - 3964</a></li>
                                <li>🌐 <a href="https://1drv.ms/x/c/780111bcbe29311c/ETX7wcjH6llNgwIEmBtDEW0BdKwULN0QXS3_bAaPkAxRPw?e=8Ty57s" target="_blank">2024 Từ 4000 - 5472</a></li>
                            </ul>
                        </div>
                    </div>

                    <button class="close-btn" onclick="document.getElementById('fileModal').style.display='none'">
                        Đóng
                    </button>
                </div>
            </div>
        @endif

        <h2>Nhập tên Sheet để in</h2>

        @if (session('success'))
            <p style="color: green">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p style="color: red">{{ session('error') }}</p>
        @endif
        @if (!empty($success))
            <p style="color: green">{{ $success }}</p>
        @endif

        @if (!empty($preview))
            <h3>Hình đã in:</h3>
            <iframe src="{{ asset($preview) }}" width="100%" height="500px"></iframe>
        @endif

        <form action="{{ route('excel.print') }}" method="POST">
            @csrf
            <input type="text" id="sheet" name="sheet" placeholder="Nhập tên sheet..." required>
            <button type="submit">In</button>
        </form>
    </div>
</body>

</html>
