<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sản Xuất') – UniPax</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --bg:        #0f1117;
            --surface:   #1a1d27;
            --surface2:  #222638;
            --border:    #2e3347;
            --accent:    #6366f1;
            --accent2:   #818cf8;
            --green:     #22c55e;
            --yellow:    #eab308;
            --red:       #ef4444;
            --blue:      #3b82f6;
            --orange:    #f97316;
            --text:      #e2e8f0;
            --muted:     #94a3b8;
            --sidebar-w: 240px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            display: flex;
            min-height: 100vh;
            font-size: 14px;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
            transition: transform .3s;
        }
        .sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-logo .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--accent), var(--blue));
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px;
        }
        .sidebar-logo .logo-text {
            font-size: 15px; font-weight: 700;
            line-height: 1.2;
        }
        .sidebar-logo .logo-text span { color: var(--accent2); }
        .sidebar-nav { flex: 1; padding: 12px 0; overflow-y: auto; }
        .nav-section {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: .08em;
            color: var(--muted);
            padding: 12px 20px 6px;
            text-transform: uppercase;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 20px;
            color: var(--muted);
            text-decoration: none;
            border-radius: 0;
            transition: background .15s, color .15s;
            border-left: 3px solid transparent;
            font-size: 13.5px;
        }
        .nav-link:hover {
            background: var(--surface2);
            color: var(--text);
        }
        .nav-link.active {
            background: rgba(99,102,241,.12);
            color: var(--accent2);
            border-left-color: var(--accent);
        }
        .nav-link .icon { width: 18px; text-align: center; }
        .sidebar-footer {
            padding: 14px 20px;
            border-top: 1px solid var(--border);
            font-size: 12px;
            color: var(--muted);
        }

        /* ── MAIN ── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .topbar {
            height: 56px;
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
        }
        .topbar-title { font-size: 16px; font-weight: 600; }
        .topbar-actions { display: flex; align-items: center; gap: 12px; }
        .content { padding: 24px; flex: 1; }

        /* ── CARDS ── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 20px;
        }
        .card-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
            margin-bottom: 4px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        /* ── STAT CARDS ── */
        .stat-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px,1fr)); gap: 14px; margin-bottom: 24px; }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: border-color .2s;
        }
        .stat-card:hover { border-color: var(--accent); }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .stat-value { font-size: 26px; font-weight: 700; line-height: 1; }
        .stat-label { font-size: 12px; color: var(--muted); margin-top: 3px; }

        /* ── TABLE ── */
        .table-wrap { overflow-x: auto; border-radius: 12px; border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: var(--surface2);
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
            border-bottom: 1px solid var(--border);
        }
        tbody tr { border-bottom: 1px solid var(--border); transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: rgba(255,255,255,.03); }
        tbody td { padding: 9px 12px; vertical-align: middle; font-size: 13px; }

        /* ── BADGE ── */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 9px;
            border-radius: 99px;
            font-size: 11px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-chua   { background: rgba(148,163,184,.15); color: #94a3b8; }
        .badge-dang   { background: rgba(234,179,8,.15);   color: #eab308; }
        .badge-hoan   { background: rgba(34,197,94,.15);   color: #22c55e; }
        .badge-red    { background: rgba(239,68,68,.15);   color: #ef4444; }
        .badge-blue   { background: rgba(59,130,246,.15);  color: #3b82f6; }

        /* ── BUTTON ── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 8px; font-size: 13px;
            font-weight: 500; cursor: pointer; border: none;
            text-decoration: none; transition: all .15s;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #5254cc; }
        .btn-ghost {
            background: transparent; color: var(--muted);
            border: 1px solid var(--border);
        }
        .btn-ghost:hover { background: var(--surface2); color: var(--text); }
        .btn-green  { background: rgba(34,197,94,.15); color: var(--green); border: 1px solid rgba(34,197,94,.3); }
        .btn-green:hover { background: rgba(34,197,94,.25); }
        .btn-yellow { background: rgba(234,179,8,.15); color: var(--yellow); border: 1px solid rgba(234,179,8,.3); }
        .btn-yellow:hover { background: rgba(234,179,8,.25); }
        .btn-sm { padding: 4px 10px; font-size: 12px; }

        /* ── FORM ── */
        .form-control {
            background: var(--surface2);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
            padding: 7px 12px;
            font-size: 13px;
            outline: none;
            font-family: inherit;
        }
        .form-control:focus { border-color: var(--accent); }
        .filter-bar {
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
            margin-bottom: 16px;
        }
        select.form-control { cursor: pointer; }

        /* ── ALERT ── */
        .alert {
            padding: 12px 16px; border-radius: 8px; margin-bottom: 16px;
            font-size: 13px;
        }
        .alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.3); color: var(--green); }
        .alert-error   { background: rgba(239,68,68,.1);  border: 1px solid rgba(239,68,68,.3);  color: var(--red); }
        .alert-info    { background: rgba(99,102,241,.1); border: 1px solid rgba(99,102,241,.3); color: var(--accent2); }

        /* ── PAGINATION ── */
        .pagination { display: flex; gap: 6px; align-items: center; margin-top: 16px; flex-wrap: wrap; }
        .pagination a, .pagination span {
            padding: 5px 11px; border-radius: 6px; font-size: 13px;
            border: 1px solid var(--border); color: var(--muted);
            background: var(--surface); text-decoration: none;
        }
        .pagination a:hover { border-color: var(--accent); color: var(--accent2); }
        .pagination .active span {
            background: var(--accent); border-color: var(--accent); color: #fff;
        }

        /* ── TOAST ── */
        #toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            background: var(--surface2); border: 1px solid var(--border);
            border-radius: 10px; padding: 12px 18px;
            font-size: 13px; display: none;
            box-shadow: 0 8px 30px rgba(0,0,0,.4);
        }
        #toast.show { display: block; animation: slideIn .25s; }
        @keyframes slideIn { from { transform: translateY(10px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🏭</div>
        <div class="logo-text">Uni<span>Pax</span><br><small style="font-weight:400;color:var(--muted);font-size:11px;">Sản Xuất</small></div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section">Tổng quan</div>
        <a href="{{ route('san-xuat.dashboard') }}" class="nav-link {{ request()->routeIs('san-xuat.dashboard') ? 'active' : '' }}">
            <span class="icon"><i class="fa fa-chart-pie"></i></span> Dashboard
        </a>

        <div class="nav-section">Quản lý</div>
        <a href="{{ route('san-xuat.ps.index') }}" class="nav-link {{ request()->routeIs('san-xuat.ps.*') ? 'active' : '' }}">
            <span class="icon"><i class="fa fa-layer-group"></i></span> Phiếu PS
        </a>
        <a href="{{ route('san-xuat.qc.index') }}" class="nav-link {{ request()->routeIs('san-xuat.qc.index') ? 'active' : '' }}">
            <span class="icon"><i class="fa fa-microscope"></i></span> QC Đạt / Lỗi
        </a>
        <a href="{{ route('san-xuat.qc.nhap') }}" class="nav-link {{ request()->routeIs('san-xuat.qc.nhap') ? 'active' : '' }}">
            <span class="icon"><i class="fa fa-pen-to-square"></i></span> Nhập QC → XK
        </a>
        <a href="{{ route('san-xuat.xuat-kho.index') }}" class="nav-link {{ request()->routeIs('san-xuat.xuat-kho.*') ? 'active' : '' }}">
            <span class="icon"><i class="fa fa-truck-fast"></i></span> Xuất Kho
        </a>

        <div class="nav-section">Khác</div>
        <a href="{{ route('phieu-xuat-kho.list') }}" class="nav-link">
            <span class="icon"><i class="fa fa-file-invoice"></i></span> Phiếu XK
        </a>
        <a href="{{ route('phieu-ve-entry.show') }}" class="nav-link">
            <span class="icon"><i class="fa fa-edit"></i></span> Nhập QC
        </a>
    </nav>

    <div class="sidebar-footer">
        <i class="fa fa-circle" style="color:var(--green);font-size:8px;"></i>
        UniPax In Gia Công v2
    </div>
</aside>

<!-- MAIN -->
<div class="main">
    <header class="topbar">
        <div class="topbar-title">@yield('page-title', 'Quản lý Sản Xuất')</div>
        <div class="topbar-actions">
            <span style="color:var(--muted);font-size:12px;">{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </header>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error"><i class="fa fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info"><i class="fa fa-info-circle"></i> {{ session('info') }}</div>
        @endif

        @yield('content')
    </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

<script>
function showToast(msg, type = 'success') {
    const t = document.getElementById('toast');
    t.style.borderColor = type === 'success' ? 'rgba(34,197,94,.4)' : 'rgba(239,68,68,.4)';
    t.style.color = type === 'success' ? '#22c55e' : '#ef4444';
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}
// CSRF helper for AJAX
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
function post(url, data) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        },
        body: JSON.stringify(data),
    }).then(r => r.json());
}
</script>
@stack('scripts')
</body>
</html>
