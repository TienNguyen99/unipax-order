<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>In lệnh sản xuất</title>

    <!-- Font: Inter (hiện đại, dễ đọc) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top right, #e8faff, #ffffff);
            color: #333;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
        }

        /* Hiệu ứng nơron */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            
            /* background: radial-gradient(circle at top left, #e8faff 0%, #f9f9ff 100%); */
            background: linear-gradient(180deg, #cfd8e3, #aebbc9);
        }

        .container {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(12px);
            padding: 35px 45px;
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            text-align: center;
            width: 420px;
            border: 1px solid rgba(173, 216, 230, 0.4);
        }

        h2 {
            margin-bottom: 18px;
            font-size: 26px;
            background: linear-gradient(90deg, #7bc8f6, #a3e3d1, #d4bdf7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #a3e3d1;
            border-radius: 8px;
            font-size: 14px;
            background-color: rgba(255, 255, 255, 0.7);
            color: #333;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }

        input[type="text"]:focus {
            outline: none;
            box-shadow: 0 0 10px rgba(123, 200, 246, 0.5);
            border-color: #7bc8f6;
        }

        button {
            background: linear-gradient(45deg, #7bc8f6, #a3e3d1);
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
            transition: all 0.3s ease;
            box-shadow: 0 3px 12px rgba(123, 200, 246, 0.3);
        }

        button:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 16px rgba(123, 200, 246, 0.4);
            opacity: 0.95;
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
            background-color: rgba(200, 230, 255, 0.6);
            backdrop-filter: blur(6px);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.9);
            margin: 4% auto;
            padding: 25px 30px;
            border-radius: 16px;
            width: 85%;
            max-width: 1100px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid-col {
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #c3e0ef;
            border-radius: 12px;
            padding: 15px;
            max-height: 350px;
            overflow-y: auto;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .year-header {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 10px;
            border-bottom: 2px solid #7bc8f6;
            padding-bottom: 5px;
            color: #3a7ca5;
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
            color: #0077b6;
            font-size: 14px;
            transition: color 0.2s ease;
        }

        .grid-col a:hover {
            color: #00a6c9;
            text-decoration: underline;
        }

        .close-btn {
            margin-top: 25px;
            background: linear-gradient(45deg, #caa0f6, #7bc8f6);
            color: #fff;
            box-shadow: 0 3px 12px rgba(123, 200, 246, 0.3);
        }

        .close-btn:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        iframe {
            border-radius: 10px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body>
    <div id="particles-js"></div>

    <div class="container">
        @if (!empty($fileUrls) && count($fileUrls) > 0)
            <button type="button" onclick="document.getElementById('fileModal').style.display='block'">
                Lệnh sản xuất cũ
            </button>

            <div id="fileModal" class="modal" onclick="if(event.target.id==='fileModal'){this.style.display='none'}">
                <div class="modal-content">
                    <h3 style="margin-bottom:20px; color:#3a7ca5; text-align:center;">
                        Chọn để tải
                    </h3>

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
                                <div class="year-header">📁 {{ $year }}</div>
                                <ul>
                                    @foreach ($files as $file)
                                        <li>• <a href="{{ $file['url'] }}" download>{{ $file['name'] }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                        <div class="grid-col">
                            <div class="year-header">🌐 Link Online</div>
                            <ul>
                                <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/ERLvdNC4tVJCgWblmzYCY_UBLeY-9B1rI2qOdEOXCMY7AQ?e=hlQm5q"
                                        target="_blank">2025 Từ 0 - 999</a></li>
                                <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/EbaocOYzzXxLncJAR5YUxhMBjAh3JxbHoBZtyxIrazoTYg?e=GjdP8K"
                                        target="_blank">2025 Từ 1000-1982</a></li>
                                <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/EfaqTeXH8EtNmzpWkLCmRdMBzaj7bfF2tKq92YeBE4PXGA?e=t4kP4v"
                                        target="_blank">2025 Từ 2017-2999</a></li>
                                        <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/IQCn3GprQPWoTI8RSA2EEEX9AR4TB1CMqGQCi_P-uKCY3j8?e=lyskep"
                                        target="_blank">2025 Từ 3000-3497</a></li>
                                <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/EdrZO6-SkGBNq-aorfBftHgB1YhK_g97KsBob3_PD0dXUQ?e=0S0Fv4"
                                        target="_blank">2024 Từ 2157 - 2999</a></li>
                                        
                                <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/EWY_a4S9JAVAkhSqRKME9DwBBgnRm7T1mWYYt7FRTcPKzw?e=8mumah"
                                        target="_blank">2024 Từ 3000 - 3964</a></li>
                                <li>🧩 <a
                                        href="https://1drv.ms/x/c/780111bcbe29311c/ETX7wcjH6llNgwIEmBtDEW0BdKwULN0QXS3_bAaPkAxRPw?e=8Ty57s"
                                        target="_blank">2024 Từ 4000 - 5472</a></li>
                            </ul>
                        </div>
                    </div>

                    <button class="close-btn" onclick="document.getElementById('fileModal').style.display='none'">
                        Đóng
                    </button>
                </div>
            </div>
        @endif

        <h2>NHẬP TÊN SHEET CẦN IN</h2>

        @if (session('success'))
            <p style="color: #009688;">{{ session('success') }}</p>
        @endif
        @if (session('error'))
            <p style="color: #e57373;">{{ session('error') }}</p>
        @endif
        @if (!empty($success))
            <p style="color: #009688;">{{ $success }}</p>
        @endif

        @if (!empty($preview))
            <h3 style="color:#3a7ca5;">Hình đã in:</h3>
            <iframe src="{{ asset($preview) }}" width="100%" height="500px"></iframe>
        @endif

        <form action="{{ route('excel.print') }}" method="POST">
            @csrf
            <input type="text" id="sheet" name="sheet" placeholder="🔹 Nhập tên sheet..." required>
            <button type="submit">In</button>
        </form>
    </div>

    <!-- Hiệu ứng nơron -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script>
particlesJS("particles-js", {
    particles: {
        number: {
            value: 260,  // ❄️ Tuyết dày hơn
            density: { enable: true, value_area: 800 }
        },
        color: { value: "#ffffff" },
        shape: {
            type: "polygon",
            polygon: {
                nb_sides: 6  // ❄️ Bông tuyết 6 cạnh
            }
        },
        opacity: {
            value: 0.9,
            random: true
        },
        size: {
            value: 5,
            random: true
        },
        line_linked: { enable: false },

        move: {
            direction: "bottom",
            speed: 0.8,   // ❄️ Rơi chậm – mềm
            random: true,
            straight: false,  // ❄️ Cho phép lắc trái/phải khi rơi
            out_mode: "out",
            bounce: false
        }
    },

    interactivity: {
        events: {
            onhover: { enable: true, mode: "repulse" },
            onclick: { enable: false}
        }
    },

    retina_detect: true
});
</script>
</body>

</html>
