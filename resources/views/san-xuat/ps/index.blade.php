@extends('san-xuat.layouts.app')

@section('title', 'Quản Lý Phiếu PS')
@section('page-title', '📋 Quản Lý Phiếu PS')

@push('styles')
<style>
.status-select {
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 6px;
    padding: 4px 8px;
    font-size: 12px;
    cursor: pointer;
    font-family: inherit;
}
.status-select:focus { outline: none; border-color: var(--accent); }
td small { display:block; color: var(--muted); font-size: 11px; }
</style>
@endpush

@section('content')

<!-- FILTER BAR -->
<form method="GET" action="{{ route('san-xuat.ps.index') }}" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="🔍 Tìm PS / mã hàng / mã lệnh..." value="{{ request('search') }}" style="width:260px;">
    <select name="trang_thai_sx" class="form-control">
        <option value="">Tất cả trạng thái</option>
        <option value="chua_sx"    {{ request('trang_thai_sx')==='chua_sx'    ? 'selected' : '' }}>⏳ Chưa SX</option>
        <option value="dang_sx"    {{ request('trang_thai_sx')==='dang_sx'    ? 'selected' : '' }}>⚙️ Đang SX</option>
        <option value="hoan_thanh" {{ request('trang_thai_sx')==='hoan_thanh' ? 'selected' : '' }}>✅ Hoàn Thành</option>
    </select>
    <select name="thang_chot" class="form-control">
        <option value="">Tất cả tháng</option>
        @foreach($thangChotList as $t)
        <option value="{{ $t }}" {{ request('thang_chot')===$t ? 'selected' : '' }}>{{ $t }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc</button>
    @if(request()->hasAny(['search','trang_thai_sx','thang_chot']))
        <a href="{{ route('san-xuat.ps.index') }}" class="btn btn-ghost">Xóa lọc</a>
    @endif
    <a href="{{ route('san-xuat.ps.export', request()->all()) }}" class="btn btn-green">
        <i class="fa fa-file-excel"></i> Xuất Excel
    </a>
    <div style="flex:1"></div>
    <span style="color:var(--muted);font-size:12px;">{{ $psList->total() }} phiếu</span>
</form>

<!-- TABLE -->
<div class="table-wrap" style="margin-bottom:16px;">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Export Date</th>
                <th>Phiếu PS</th>
                <th>Mã Hàng</th>
                <th>Mã Lệnh</th>
                <th>Kích Thước</th>
                <th>Màu Vải</th>
                <th>Màu Logo</th>
                <th>SL ĐH</th>
                <th>SL Nhãn</th>
                <th>Nơi Giao</th>
                <th>Vị Trí</th>
                <th>Tháng Chốt</th>
                <th>Gia Công</th>
                <th>Ng. Nhận Panel</th>
                <th>Ng. Xuất Kho</th>
                <th>Trạng Thái SX</th>
            </tr>
        </thead>
        <tbody>
            @forelse($psList as $ps)
            <tr id="row-{{ $ps->id }}">
                <td style="color:var(--muted)">{{ $loop->iteration + ($psList->currentPage()-1)*$psList->perPage() }}</td>
                <td style="white-space:nowrap;font-size:12px;color:var(--muted)">{{ $ps->export_date }}</td>
                <td><strong style="color:var(--accent2)">{{ $ps->phieu_ps }}</strong><small>{{ $ps->so_phieu }}</small></td>
                <td>{{ $ps->ma_hang }}</td>
                <td>{{ $ps->ma_lenh }}</td>
                <td>{{ $ps->kich_thuoc }}</td>
                <td>{{ $ps->mau_vai }}</td>
                <td>{{ $ps->mau_logo }}</td>
                <td style="text-align:right;">{{ $ps->so_luong_donhang }}</td>
                <td style="text-align:right;">{{ $ps->so_luong_nhan }}</td>
                <td>{{ $ps->noi_giao }}</td>
                <td>{{ $ps->vi_tri }}</td>
                <td>{{ $ps->thang_chot }}</td>
                <td>{{ $ps->gia_cong }}</td>
                <td>{{ $ps->ngay_nhan_panel }}</td>
                <td>
                    @if($ps->ngay_xuat_kho)
                        <span class="badge badge-blue">📦 {{ $ps->ngay_xuat_kho }}</span>
                    @else
                        <span style="color:var(--muted)">—</span>
                    @endif
                </td>
                <td>
                    @if($ps->ngay_xuat_kho)
                        {{-- Đã có ngày xuất kho → mặc định hoàn thành, không cho đổi --}}
                        <span class="badge badge-hoan">✅ Hoàn Thành</span>
                    @else
                        <select class="status-select"
                                data-id="{{ $ps->id }}"
                                onchange="updateStatus(this)">
                            <option value="chua_sx"    {{ $ps->trang_thai_sx === 'chua_sx'    ? 'selected' : '' }}>⏳ Chưa SX</option>
                            <option value="dang_sx"    {{ $ps->trang_thai_sx === 'dang_sx'    ? 'selected' : '' }}>⚙️ Đang SX</option>
                            <option value="hoan_thanh" {{ $ps->trang_thai_sx === 'hoan_thanh' ? 'selected' : '' }}>✅ Hoàn Thành</option>
                        </select>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="17" style="text-align:center;color:var(--muted);padding:48px;">
                    <div style="font-size:32px;margin-bottom:8px;">📭</div>
                    Không tìm thấy phiếu PS nào
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="pagination">
    {{ $psList->links() }}
</div>

@endsection

@push('scripts')
<script>
async function updateStatus(sel) {
    const id  = sel.dataset.id;
    const val = sel.value;
    sel.disabled = true;
    try {
        const res = await post(`/san-xuat/ps/${id}/status`, { trang_thai_sx: val });
        if (res.success) {
            showToast(res.message, 'success');
        } else {
            showToast(res.message || 'Lỗi cập nhật', 'error');
        }
    } catch (e) {
        showToast('Lỗi kết nối server', 'error');
    } finally {
        sel.disabled = false;
    }
}
</script>
@endpush
