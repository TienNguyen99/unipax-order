@extends('san-xuat.layouts.app')

@section('title', 'Dashboard')
@section('page-title', '📊 Dashboard Tổng Quan')

@push('styles')
<style>
.section-title {
    font-size: 15px; font-weight: 600; margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.chart-box { position: relative; height: 240px; }
@media(max-width:900px){ .grid-2 { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')

<!-- STAT CARDS -->
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(99,102,241,.15);">📋</div>
        <div>
            <div class="stat-value">{{ $stats['tong_ps'] }}</div>
            <div class="stat-label">Tổng Phiếu PS</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(148,163,184,.15);">⏳</div>
        <div>
            <div class="stat-value" style="color:#94a3b8">{{ $stats['chua_sx'] }}</div>
            <div class="stat-label">Chưa Sản Xuất</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(234,179,8,.15);">⚙️</div>
        <div>
            <div class="stat-value" style="color:var(--yellow)">{{ $stats['dang_sx'] }}</div>
            <div class="stat-label">Đang Sản Xuất</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,.15);">✅</div>
        <div>
            <div class="stat-value" style="color:var(--green)">{{ $stats['hoan_thanh'] }}</div>
            <div class="stat-label">Hoàn Thành</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(249,115,22,.15);">🚚</div>
        <div>
            <div class="stat-value" style="color:var(--orange)">{{ $stats['cho_xuat_kho'] }}</div>
            <div class="stat-label">Chờ Xuất Kho</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,.15);">📦</div>
        <div>
            <div class="stat-value" style="color:var(--blue)">{{ $stats['da_xuat_kho'] }}</div>
            <div class="stat-label">Đã Xuất Kho</div>
        </div>
    </div>
</div>

<!-- QC SUMMARY -->
<div class="card" style="margin-bottom:20px;">
    <div class="section-title"><i class="fa fa-microscope" style="color:var(--accent2)"></i> Tổng Hợp QC Toàn Bộ</div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
        @php
        $qcItems = [
            ['label'=>'Makhác Đạt','val'=>$qcTong->tong_makhac_dat ?? 0,'color'=>'var(--green)'],
            ['label'=>'Makhác Lỗi','val'=>$qcTong->tong_makhac_loi ?? 0,'color'=>'var(--red)'],
            ['label'=>'Front Đạt', 'val'=>$qcTong->tong_front_dat ?? 0, 'color'=>'var(--green)'],
            ['label'=>'Front Lỗi', 'val'=>$qcTong->tong_front_loi ?? 0, 'color'=>'var(--red)'],
            ['label'=>'Back Đạt',  'val'=>$qcTong->tong_back_dat  ?? 0, 'color'=>'var(--green)'],
            ['label'=>'Back Lỗi',  'val'=>$qcTong->tong_back_loi  ?? 0, 'color'=>'var(--red)'],
        ];
        @endphp
        @foreach($qcItems as $qi)
        <div style="background:var(--surface2);border:1px solid var(--border);border-radius:10px;padding:14px 16px;text-align:center;">
            <div style="font-size:22px;font-weight:700;color:{{ $qi['color'] }}">{{ number_format($qi['val']) }}</div>
            <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $qi['label'] }}</div>
        </div>
        @endforeach
    </div>
</div>

<!-- CHARTS + RECENT TABLE -->
<div class="grid-2" style="margin-bottom:20px;">
    <!-- Donut chart trạng thái -->
    <div class="card">
        <div class="section-title"><i class="fa fa-chart-donut" style="color:var(--accent2)"></i> Trạng Thái Sản Xuất</div>
        <div class="chart-box">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    <!-- Bar chart theo tháng chốt -->
    <div class="card">
        <div class="section-title"><i class="fa fa-chart-bar" style="color:var(--accent2)"></i> PS Theo Tháng Chốt</div>
        <div class="chart-box">
            <canvas id="thangChart"></canvas>
        </div>
    </div>
</div>

<!-- TABLES GRID -->
<div class="grid-2">
    <!-- UPCOMING EXPORTS -->
    <div class="card">
        <div class="section-title"><i class="fa fa-truck-fast" style="color:var(--orange)"></i> Sắp Đến Hạn Xuất (Export Date)</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Mã Hàng</th>
                        <th>Phiếu PS</th>
                        <th>Vị Trí</th>
                        <th>Ngày Xuất Hạn</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($upcomingExports as $ps)
                    @php
                        // Cảnh báo nếu quá hạn hoặc sát ngày
                        $isUrgent = false;
                        try {
                            $today = \Carbon\Carbon::today();
                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $ps->export_date)) {
                                $exp = \Carbon\Carbon::parse($ps->export_date);
                                if ($exp->isPast()) $isUrgent = true;
                                elseif ($exp->diffInDays($today) <= 3) $isUrgent = 'soon';
                            }
                        } catch(\Exception $e) {}
                    @endphp
                    <tr style="{{ $isUrgent === true ? 'background:rgba(239,68,68,.05)' : ($isUrgent === 'soon' ? 'background:rgba(234,179,8,.05)' : '') }}">
                        <td><strong>{{ $ps->ma_hang }}</strong></td>
                        <td style="font-size:12px;color:var(--muted)">{{ $ps->phieu_ps }}</td>
                        <td><span class="badge" style="background:var(--surface2);border:1px solid var(--border)">{{ $ps->vi_tri ?? '—' }}</span></td>
                        <td>
                            @if($isUrgent === true)
                                <span style="color:var(--red);font-weight:600;"><i class="fa fa-circle-exclamation"></i> {{ $ps->export_date }}</span>
                            @elseif($isUrgent === 'soon')
                                <span style="color:var(--yellow);font-weight:600;">{{ $ps->export_date }}</span>
                            @else
                                <span style="color:var(--blue)">{{ $ps->export_date }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:24px;">Không có hàng chờ xuất</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- RECENT PS -->
    <div class="card">
        <div class="section-title"><i class="fa fa-clock-rotate-left" style="color:var(--accent2)"></i> Phiếu PS Mới Nhập</div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Phiếu PS</th>
                        <th>Mã Hàng</th>
                        <th>SL Nhận</th>
                        <th>Trạng Thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPs as $ps)
                    <tr>
                        <td><a href="{{ route('san-xuat.ps.index', ['search'=>$ps->phieu_ps]) }}" style="color:var(--accent2);text-decoration:none;">{{ $ps->phieu_ps }}</a></td>
                        <td>{{ $ps->ma_hang }}</td>
                        <td>{{ $ps->so_luong_nhan }}</td>
                        <td>
                            @if($ps->trang_thai_sx === 'chua_sx')
                                <span class="badge badge-chua">⏳</span>
                            @elseif($ps->trang_thai_sx === 'dang_sx')
                                <span class="badge badge-dang">⚙️</span>
                            @else
                                <span class="badge badge-hoan">✅</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--muted);padding:24px;">Chưa có dữ liệu</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;">
            <a href="{{ route('san-xuat.ps.index') }}" class="btn btn-ghost btn-sm">
                Xem tất cả <i class="fa fa-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const chartDefaults = {
    plugins: { legend: { labels: { color: '#94a3b8', font: { family: 'Inter', size: 12 } } } }
};

// Donut – Trạng thái
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Chưa SX', 'Đang SX', 'Hoàn Thành'],
        datasets: [{
            data: [{{ $stats['chua_sx'] }}, {{ $stats['dang_sx'] }}, {{ $stats['hoan_thanh'] }}],
            backgroundColor: ['rgba(148,163,184,.6)', 'rgba(234,179,8,.6)', 'rgba(34,197,94,.6)'],
            borderColor:     ['#94a3b8', '#eab308', '#22c55e'],
            borderWidth: 1.5,
        }]
    },
    options: {
        ...chartDefaults,
        cutout: '65%',
        responsive: true,
        maintainAspectRatio: false,
    }
});

// Bar – Theo tháng chốt
@php
    $thangLabels  = $byThang->pluck('thang_chot')->unique()->values();
    $thangChua    = [];
    $thangDang    = [];
    $thangHoan    = [];
    foreach ($thangLabels as $t) {
        $thangChua[] = $byThang->where('thang_chot', $t)->where('trang_thai_sx', 'chua_sx')->sum('total');
        $thangDang[] = $byThang->where('thang_chot', $t)->where('trang_thai_sx', 'dang_sx')->sum('total');
        $thangHoan[] = $byThang->where('thang_chot', $t)->where('trang_thai_sx', 'hoan_thanh')->sum('total');
    }
@endphp
new Chart(document.getElementById('thangChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($thangLabels) !!},
        datasets: [
            { label: 'Chưa SX',     data: {!! json_encode($thangChua) !!}, backgroundColor: 'rgba(148,163,184,.5)', borderRadius: 4 },
            { label: 'Đang SX',     data: {!! json_encode($thangDang) !!}, backgroundColor: 'rgba(234,179,8,.5)',   borderRadius: 4 },
            { label: 'Hoàn Thành',  data: {!! json_encode($thangHoan) !!}, backgroundColor: 'rgba(34,197,94,.5)',   borderRadius: 4 },
        ]
    },
    options: {
        ...chartDefaults,
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: { stacked: true, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,.04)' } },
            y: { stacked: true, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255,255,255,.04)' } },
        }
    }
});
</script>
@endpush
