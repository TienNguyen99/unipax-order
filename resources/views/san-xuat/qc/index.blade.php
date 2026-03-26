@extends('san-xuat.layouts.app')

@section('title', 'QC Đạt / Lỗi')
@section('page-title', '🔬 QC Kiểm Tra Đạt / Lỗi')

@push('styles')
<style>
.qc-cell { text-align: right; font-weight: 600; }
.loi-positive { color: var(--red); }
.dat-positive  { color: var(--green); }
.tong-row { font-size: 12px; }
.highlight-loi { background: rgba(239,68,68,.05) !important; }
</style>
@endpush

@section('content')

<!-- TỔNG HỢP -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:12px;margin-bottom:20px;">
    @php
    $sumCards = [
        ['label'=>'Makhác Đạt','val'=>$tongHop->tong_makhac_dat,'color'=>'var(--green)','icon'=>'✅'],
        ['label'=>'Makhác Lỗi','val'=>$tongHop->tong_makhac_loi,'color'=>'var(--red)',  'icon'=>'❌'],
        ['label'=>'Front Đạt', 'val'=>$tongHop->tong_front_dat, 'color'=>'var(--green)','icon'=>'✅'],
        ['label'=>'Front Lỗi', 'val'=>$tongHop->tong_front_loi, 'color'=>'var(--red)',  'icon'=>'❌'],
        ['label'=>'Back Đạt',  'val'=>$tongHop->tong_back_dat,  'color'=>'var(--green)','icon'=>'✅'],
        ['label'=>'Back Lỗi',  'val'=>$tongHop->tong_back_loi,  'color'=>'var(--red)',  'icon'=>'❌'],
    ];
    @endphp
    @foreach($sumCards as $c)
    <div class="card" style="text-align:center;padding:14px 12px;">
        <div style="font-size:20px;font-weight:700;color:{{ $c['color'] }}">{{ number_format($c['val']) }}</div>
        <div style="font-size:11px;color:var(--muted);margin-top:3px;">{{ $c['icon'] }} {{ $c['label'] }}</div>
    </div>
    @endforeach
</div>

<!-- FILTER -->
<form method="GET" action="{{ route('san-xuat.qc.index') }}" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="🔍 Tìm phiếu PS / mã hàng..." value="{{ request('search') }}" style="width:240px;">
    <input type="date" name="tu_ngay"  class="form-control" value="{{ request('tu_ngay') }}"  title="Từ ngày">
    <input type="date" name="den_ngay" class="form-control" value="{{ request('den_ngay') }}" title="Đến ngày">
    <label style="display:flex;align-items:center;gap:6px;color:var(--muted);cursor:pointer;">
        <input type="checkbox" name="co_loi" value="1" {{ request('co_loi') ? 'checked' : '' }}>
        <span style="font-size:13px;">Chỉ có lỗi</span>
    </label>
    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc</button>
    @if(request()->hasAny(['search','tu_ngay','den_ngay','co_loi']))
        <a href="{{ route('san-xuat.qc.index') }}" class="btn btn-ghost">Xóa lọc</a>
    @endif
    <div style="flex:1"></div>
    <span style="color:var(--muted);font-size:12px;">{{ $qcList->total() }} phiếu</span>
</form>

<!-- TABLE -->
<div class="table-wrap" style="margin-bottom:16px;">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Phiếu PS</th>
                <th>Mã Hàng</th>
                <th>Kích Thước</th>
                <th>SL ĐH</th>
                <th>SL Nhãn</th>
                <th>Makhác Đạt</th>
                <th>Makhác Lỗi</th>
                <th>Front Đạt</th>
                <th>Front Lỗi</th>
                <th>Back Đạt</th>
                <th>Back Lỗi</th>
                <th>Trạng Thái</th>
                <th>Ghi Chú</th>
            </tr>
        </thead>
        <tbody>
            @forelse($qcList as $ps)
            @php
            $hasLoi = ($ps->makhac_loi > 0 || $ps->front_loi > 0 || $ps->back_loi > 0);
            @endphp
            <tr class="{{ $hasLoi ? 'highlight-loi' : '' }}">
                <td style="color:var(--muted)">{{ $loop->iteration + ($qcList->currentPage()-1)*$qcList->perPage() }}</td>
                <td><strong style="color:var(--accent2)">{{ $ps->phieu_ps }}</strong></td>
                <td>{{ $ps->ma_hang }}</td>
                <td>{{ $ps->kich_thuoc }}</td>
                <td class="qc-cell">{{ $ps->so_luong_donhang }}</td>
                <td class="qc-cell">{{ $ps->so_luong_nhan }}</td>
                <!-- Makhác -->
                <td class="qc-cell dat-positive">{{ $ps->makhac_dat ?: '—' }}</td>
                <td class="qc-cell {{ $ps->makhac_loi > 0 ? 'loi-positive' : '' }}">{{ $ps->makhac_loi ?: '—' }}</td>
                <!-- Front -->
                <td class="qc-cell dat-positive">{{ $ps->front_dat ?: '—' }}</td>
                <td class="qc-cell {{ $ps->front_loi > 0 ? 'loi-positive' : '' }}">{{ $ps->front_loi ?: '—' }}</td>
                <!-- Back -->
                <td class="qc-cell dat-positive">{{ $ps->back_dat ?: '—' }}</td>
                <td class="qc-cell {{ $ps->back_loi > 0 ? 'loi-positive' : '' }}">{{ $ps->back_loi ?: '—' }}</td>
                <td>
                    @if($ps->trang_thai_sx === 'chua_sx')
                        <span class="badge badge-chua">⏳ Chưa SX</span>
                    @elseif($ps->trang_thai_sx === 'dang_sx')
                        <span class="badge badge-dang">⚙️ Đang SX</span>
                    @else
                        <span class="badge badge-hoan">✅ Xong</span>
                    @endif
                </td>
                <td style="max-width:180px;white-space:normal;font-size:12px;color:var(--muted)">{{ $ps->ghi_chu }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="14" style="text-align:center;color:var(--muted);padding:48px;">
                    <div style="font-size:32px;margin-bottom:8px;">✅</div>
                    Không có dữ liệu QC
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="pagination">
    {{ $qcList->links() }}
</div>

@endsection
