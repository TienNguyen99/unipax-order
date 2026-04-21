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
            overflow-y: auto;
        }

        body.theme-noel .modal {
            background-color: rgba(200, 230, 255, 0.6);
        }

        body.theme-tet .modal {
            background-color: rgba(255, 230, 200, 0.6);
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            margin: 15% auto;
            padding: 30px 35px;
            border-radius: 16px;
            width: auto;
            max-width: 520px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            color: #333;
            overflow: visible;
            text-align: center;
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
            overflow: visible;
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

        /* ==== Dropdown theo năm ==== */
        .year-dropdown {
            position: relative;
            display: inline-block;
            flex: 1 1 0;
            min-width: 140px;
        }

        .year-dropdown-btn {
            padding: 14px 24px;
            border-radius: 14px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: 0.5px;
            color: #fff;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
        }

        .year-dropdown-btn::after {
            content: '▾';
            font-size: 14px;
            transition: transform 0.3s ease;
        }

        .year-dropdown:hover .year-dropdown-btn::after {
            transform: rotate(180deg);
        }

        body.theme-noel .year-dropdown-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        body.theme-tet .year-dropdown-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.4);
        }

        .year-dropdown-btn:hover {
            transform: translateY(-3px);
            opacity: 0.95;
        }

        body.theme-noel .year-dropdown-btn:hover {
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        body.theme-tet .year-dropdown-btn:hover {
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.5);
        }

        .year-dropdown-content {
            visibility: hidden;
            opacity: 0;
            position: absolute;
            left: 0;
            top: 100%;
            min-width: 240px;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
            z-index: 10;
            padding: 10px 0;
            padding-top: 6px;
            transition: visibility 0s linear 0.35s, opacity 0.25s ease;
        }

        @keyframes dropdownFade {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        body.theme-noel .year-dropdown-content {
            border: 1px solid #c3e0ef;
        }

        body.theme-tet .year-dropdown-content {
            border: 1px solid #ffcccb;
        }

        .year-dropdown:hover .year-dropdown-content {
            visibility: visible;
            opacity: 1;
            transition: visibility 0s linear 0s, opacity 0.25s ease;
        }

        .year-dropdown-content a {
            display: block;
            padding: 8px 18px;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        body.theme-noel .year-dropdown-content a {
            color: #0077b6;
        }

        body.theme-tet .year-dropdown-content a {
            color: #d63031;
        }

        .year-dropdown-content a:hover {
            background: rgba(0, 0, 0, 0.04);
            padding-left: 24px;
        }

        body.theme-noel .year-dropdown-content a:hover {
            color: #00a6c9;
        }

        body.theme-tet .year-dropdown-content a:hover {
            color: #ff6b6b;
        }

        .online-links-wrapper {
            display: flex;
            flex-wrap: nowrap;
            justify-content: center;
            gap: 12px;
            margin-top: 15px;
        }
    </style>
</head>

<body class="theme-tet">
    <!-- Nút chuyển theme -->
    <button class="theme-toggle" onclick="toggleTheme()">
        <span id="theme-icon">❄️</span>
        <span id="theme-text">Chuyển sang Tết</span>
    </button>

    <div id="particles-js"></div>

    <!-- Modal nằm ngoài container -->
    <div id="fileModal" class="modal" onclick="if(event.target.id==='fileModal'){this.style.display='none'}">
        <div class="modal-content">
            <h3 style="margin-bottom:20px;">
                Chọn để tải
            </h3>

            <div class="online-links-wrapper">
                <!-- 2026 -->
                <div class="year-dropdown">
                    <button type="button" class="year-dropdown-btn">📅 2026</button>
                    <div class="year-dropdown-content">
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/IQAHHBAQl_LnRaafwG68J_tsAf6ao5Q6f01YeY0V5pGjv3E?e=Q4558A"
                            target="_blank">🧩 Từ 1 - 499</a>
                    </div>
                </div>
                <!-- 2025 -->
                <div class="year-dropdown">
                    <button type="button" class="year-dropdown-btn">📅 2025</button>
                    <div class="year-dropdown-content">
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/ERLvdNC4tVJCgWblmzYCY_UBLeY-9B1rI2qOdEOXCMY7AQ?e=hlQm5q"
                            target="_blank">🧩 Từ 0 - 999</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/EbaocOYzzXxLncJAR5YUxhMBjAh3JxbHoBZtyxIrazoTYg?e=GjdP8K"
                            target="_blank">🧩 Từ 1000 - 1982</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/EfaqTeXH8EtNmzpWkLCmRdMBzaj7bfF2tKq92YeBE4PXGA?e=t4kP4v"
                            target="_blank">🧩 Từ 2017 - 2999</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/IQCn3GprQPWoTI8RSA2EEEX9AR4TB1CMqGQCi_P-uKCY3j8?e=lyskep"
                            target="_blank">🧩 Từ 3000 - 3497</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/IQBm_Alq6pkKQJUyzGvuG8XsAR2zSCfvsqmw6v01L3RAATw?e=Phdhdi"
                            target="_blank">🧩 Từ 3512 - 3999</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/IQCgdOW962jvQ60c6JnM3XexAXiCtePywcNh_Lotbbdubyw?e=l11zyj"
                            target="_blank">🧩 Từ 4000 - 4700</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/IQA4Pt5G8INQRan_HOwyuYJNAd34IvLDkIIMHo-80l7Nx2A?e=J2CW21"
                            target="_blank">🧩 Từ 4700 - 5077</a>
                    </div>
                </div>
                <!-- 2024 -->
                <div class="year-dropdown">
                    <button type="button" class="year-dropdown-btn">📅 2024</button>
                    <div class="year-dropdown-content">
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/EdrZO6-SkGBNq-aorfBftHgB1YhK_g97KsBob3_PD0dXUQ?e=0S0Fv4"
                            target="_blank">🧩 Từ 2157 - 2999</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/EWY_a4S9JAVAkhSqRKME9DwBBgnRm7T1mWYYt7FRTcPKzw?e=8mumah"
                            target="_blank">🧩 Từ 3000 - 3964</a>
                        <a href="https://1drv.ms/x/c/780111bcbe29311c/ETX7wcjH6llNgwIEmBtDEW0BdKwULN0QXS3_bAaPkAxRPw?e=8Ty57s"
                            target="_blank">🧩 Từ 4000 - 5472</a>
                    </div>
                </div>
            </div>

            <button class="close-btn" onclick="document.getElementById('fileModal').style.display='none'">
                Đóng
            </button>
        </div>
    </div>

    <div class="container">
        <button type="button" onclick="document.getElementById('fileModal').style.display='block'">
            Lệnh sản xuất cũ
        </button>

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
                        src: "{{ asset('lixivn2.png') }}",

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
