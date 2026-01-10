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
            color: #333;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow-x: hidden;
            transition: background 0.5s ease;
        }

        /* Theme Noel */
        body.theme-noel {
            background: radial-gradient(circle at top right, #e8faff, #ffffff);
        }

        /* Theme Tết */
        body.theme-tet {
            background: radial-gradient(circle at top right, #fff5e6, #ffe6e6);
        }

        /* Hiệu ứng particles */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            transition: background 0.5s ease;
        }

        /* Background Noel */
        body.theme-noel #particles-js {
            background: linear-gradient(180deg, #cfd8e3, #aebbc9);
        }

        /* Background Tết */
        body.theme-tet #particles-js {
            background: url('https://sf-static.upanhlaylink.com/img/image_20251230abac5cfeffdde7c9f6f22f25ebe2c493.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Nút chuyển theme */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1001;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        body.theme-noel .theme-toggle {
            background: linear-gradient(45deg, #7bc8f6, #a3e3d1);
            color: #fff;
        }

        body.theme-tet .theme-toggle {
            background: linear-gradient(45deg, #ff6b6b, #ffd93d);
            color: #fff;
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
            transition: border-color 0.5s ease;
        }

        body.theme-noel .container {
            border: 1px solid rgba(173, 216, 230, 0.4);
        }

        body.theme-tet .container {
            border: 1px solid rgba(255, 107, 107, 0.4);
        }

        h2 {
            margin-bottom: 18px;
            font-size: 26px;
            font-weight: 700;
            transition: all 0.5s ease;
        }

        body.theme-noel h2 {
            background: linear-gradient(90deg, #7bc8f6, #a3e3d1, #d4bdf7);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.theme-tet h2 {
            color: #d63031;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        input[type="text"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border-radius: 8px;
            font-size: 14px;
            background-color: rgba(255, 255, 255, 0.7);
            color: #333;
            transition: box-shadow 0.3s ease, border-color 0.3s ease;
        }

        body.theme-noel input[type="text"] {
            border: 1px solid #a3e3d1;
        }

        body.theme-tet input[type="text"] {
            border: 1px solid #ffd93d;
        }

        input[type="text"]:focus {
            outline: none;
        }

        body.theme-noel input[type="text"]:focus {
            box-shadow: 0 0 10px rgba(123, 200, 246, 0.5);
            border-color: #7bc8f6;
        }

        body.theme-tet input[type="text"]:focus {
            box-shadow: 0 0 10px rgba(255, 107, 107, 0.5);
            border-color: #ff6b6b;
        }

        button {
            color: #fff;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        body.theme-noel button {
            background: linear-gradient(45deg, #7bc8f6, #a3e3d1);
            box-shadow: 0 3px 12px rgba(123, 200, 246, 0.3);
        }

        body.theme-tet button {
            background: linear-gradient(45deg, #ff6b6b, #ffd93d);
            box-shadow: 0 3px 12px rgba(255, 107, 107, 0.3);
        }

        button:hover {
            transform: translateY(-1px);
            opacity: 0.95;
        }

        body.theme-noel button:hover {
            box-shadow: 0 5px 16px rgba(123, 200, 246, 0.4);
        }

        body.theme-tet button:hover {
            box-shadow: 0 5px 16px rgba(255, 107, 107, 0.4);
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
            backdrop-filter: blur(6px);
        }

        body.theme-noel .modal {
            background-color: rgba(200, 230, 255, 0.6);
        }

        body.theme-tet .modal {
            background-color: rgba(255, 230, 200, 0.6);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            margin: 4% auto;
            padding: 25px 30px;
            border-radius: 16px;
            width: 85%;
            max-width: 1100px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .modal-content h3 {
            color: #333;
        }

        body.theme-noel .modal-content h3 {
            color: #3a7ca5;
        }

        body.theme-tet .modal-content h3 {
            color: #d63031;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .grid-col {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 15px;
            max-height: 350px;
            overflow-y: auto;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        body.theme-noel .grid-col {
            border: 1px solid #c3e0ef;
        }

        body.theme-tet .grid-col {
            border: 1px solid #ffcccb;
        }

        .year-header {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 10px;
            padding-bottom: 5px;
        }

        body.theme-noel .year-header {
            border-bottom: 2px solid #7bc8f6;
            color: #3a7ca5;
        }

        body.theme-tet .year-header {
            border-bottom: 2px solid #ff6b6b;
            color: #d63031;
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
            font-size: 14px;
            transition: color 0.2s ease;
        }

        body.theme-noel .grid-col a {
            color: #0077b6;
        }

        body.theme-tet .grid-col a {
            color: #d63031;
        }

        body.theme-noel .grid-col a:hover {
            color: #00a6c9;
        }

        body.theme-tet .grid-col a:hover {
            color: #ff6b6b;
        }

        .grid-col a:hover {
            text-decoration: underline;
        }

        .close-btn {
            margin-top: 25px;
        }

        body.theme-noel .close-btn {
            background: linear-gradient(45deg, #caa0f6, #7bc8f6);
            box-shadow: 0 3px 12px rgba(123, 200, 246, 0.3);
        }

        body.theme-tet .close-btn {
            background: linear-gradient(45deg, #ff6b6b, #ffd93d);
            box-shadow: 0 3px 12px rgba(255, 107, 107, 0.3);
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

<body class="theme-noel">
    <!-- Nút chuyển theme -->
    <button class="theme-toggle" onclick="toggleTheme()">
        <span id="theme-icon">❄️</span>
        <span id="theme-text">Chuyển sang Tết</span>
    </button>

    <div id="particles-js"></div>

    <div class="container">
        @if (!empty($fileUrls) && count($fileUrls) > 0)
            <button type="button" onclick="document.getElementById('fileModal').style.display='block'">
                Lệnh sản xuất cũ
            </button>

            <div id="fileModal" class="modal" onclick="if(event.target.id==='fileModal'){this.style.display='none'}">
                <div class="modal-content">
                    <h3 style="margin-bottom:20px; text-align:center;">
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
                                        href="https://1drv.ms/x/c/780111bcbe29311c/IQBm_Alq6pkKQJUyzGvuG8XsAR2zSCfvsqmw6v01L3RAATw?e=Phdhdi"
                                        target="_blank">2025 Từ 3512 - 3999</a></li>
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
            <h3 style="color:#333; font-weight:600;">Hình đã in:</h3>
            <iframe src="{{ asset($preview) }}" width="100%" height="500px"></iframe>
        @endif

        <form action="{{ route('excel.print') }}" method="POST">
            @csrf
            <input type="text" id="sheet" name="sheet" placeholder="🔹 Nhập tên sheet..." required>
            <button type="submit">In</button>
        </form>
    </div>

    <!-- Hiệu ứng particles -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        let currentTheme = 'noel';
        let particlesInstance;

        // Kiểm tra và khôi phục theme đã lưu
        const savedTheme = localStorage.getItem('selectedTheme');
        if (savedTheme) {
            currentTheme = savedTheme;
            document.body.classList.remove('theme-noel', 'theme-tet');
            document.body.classList.add(`theme-${currentTheme}`);

            if (currentTheme === 'tet') {
                document.getElementById('theme-icon').textContent = '🧧';
                document.getElementById('theme-text').textContent = 'Chuyển sang Noel';
            }
        }

        // Cấu hình theme Noel (tuyết)
        const noelConfig = {
            particles: {
                number: {
                    value: 260,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: "#ffffff"
                },
                shape: {
                    type: "polygon",
                    polygon: {
                        nb_sides: 6
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
                line_linked: {
                    enable: false
                },
                move: {
                    direction: "bottom",
                    speed: 0.8,
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false
                }
            },
            interactivity: {
                events: {
                    onhover: {
                        enable: true,
                        mode: "repulse"
                    },
                    onclick: {
                        enable: false
                    }
                }
            },
            retina_detect: true
        };

        // Cấu hình theme Tết (lì xì/phong bì đỏ)
        const tetConfig = {
            particles: {
                number: {
                    value: 15,
                    density: {
                        enable: true,
                        value_area: 800
                    }
                },
                color: {
                    value: "#ff0000"
                },
                shape: {
                    type: "image",
                    image: {
                        src: "https://sf-static.upanhlaylink.com/img/image_20251230e1692fd1b1a91de40bb70aca4fe2c4cb.jpg",
                        width: 100,
                        height: 100
                    }
                },
                opacity: {
                    value: 0.8,
                    random: true,
                    anim: {
                        enable: true,
                        speed: 0.5,
                        opacity_min: 0.4
                    }
                },
                size: {
                    value: 30,
                    random: true,
                    anim: {
                        enable: true,
                        speed: 1,
                        size_min: 20
                    }
                },
                line_linked: {
                    enable: false
                },
                move: {
                    direction: "bottom-right",
                    speed: 0.6,
                    random: true,
                    straight: false,
                    out_mode: "out",
                    bounce: false,
                    attract: {
                        enable: false
                    }
                }
            },
            interactivity: {
                events: {
                    onhover: {
                        enable: true,
                        mode: "bubble"
                    },
                    onclick: {
                        enable: true,
                        mode: "push"
                    }
                },
                modes: {
                    bubble: {
                        distance: 150,
                        size: 40,
                        duration: 2
                    },
                    push: {
                        particles_nb: 4
                    }
                }
            },
            retina_detect: true
        };

        // Khởi tạo particles dựa trên theme hiện tại
        if (currentTheme === 'noel') {
            particlesJS("particles-js", noelConfig);
        } else {
            particlesJS("particles-js", tetConfig);
        }

        // Hàm chuyển theme
        function toggleTheme() {
            const body = document.body;
            const themeIcon = document.getElementById('theme-icon');
            const themeText = document.getElementById('theme-text');

            if (currentTheme === 'noel') {
                body.classList.remove('theme-noel');
                body.classList.add('theme-tet');
                themeIcon.textContent = '🧧';
                themeText.textContent = 'Chuyển sang Noel';
                currentTheme = 'tet';

                // Lưu theme vào localStorage
                localStorage.setItem('selectedTheme', 'tet');

                // Reinitialize particles với config Tết
                pJSDom[0].pJS.particles.array = [];
                particlesJS("particles-js", tetConfig);
            } else {
                body.classList.remove('theme-tet');
                body.classList.add('theme-noel');
                themeIcon.textContent = '❄️';
                themeText.textContent = 'Chuyển sang Tết';
                currentTheme = 'noel';

                // Lưu theme vào localStorage
                localStorage.setItem('selectedTheme', 'noel');

                // Reinitialize particles với config Noel
                pJSDom[0].pJS.particles.array = [];
                particlesJS("particles-js", noelConfig);
            }
        }
    </script>
</body>

</html>
