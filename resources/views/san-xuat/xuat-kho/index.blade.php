@extends('san-xuat.layouts.app')

@section('title', 'Xuất Kho')
@section('page-title', '🚚 Xuất Kho')

@push('styles')
<style>
.xk-actions { display: flex; gap: 6px; align-items: center; }
.confirmed-row { opacity: .6; }
</style>
@endpush

@section('content')

<!-- FILTER -->
<form method="GET" action="{{ route('san-xuat.xuat-kho.index') }}" class="filter-bar">
    <input type="text" name="search" class="form-control" placeholder="🔍 Tìm PS / mã hàng / nơi giao..." value="{{ request('search') }}" style="width:260px;">
    <select name="trang_thai_xk" class="form-control">
        <option value="chua"  {{ request('trang_thai_xk','chua')==='chua' ? 'selected' : '' }}>📦 Chờ Xuất Kho</option>
        <option value="da_xk" {{ request('trang_thai_xk')==='da_xk'       ? 'selected' : '' }}>✅ Đã Xuất Kho</option>
    </select>
    <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Lọc</button>
    @if(request()->hasAny(['search','trang_thai_xk']))
        <a href="{{ route('san-xuat.xuat-kho.index') }}" class="btn btn-ghost">Xóa lọc</a>
    @endif
    <div style="flex:1"></div>
    <span style="color:var(--muted);font-size:12px;">{{ $xuatKhoList->total() }} phiếu</span>
</form>

<!-- BULK ACTION -->
<div style="display:flex;align-items:center;gap:10px;margin-bottom:14px;">
    <input type="date" id="bulkDate" class="form-control" value="{{ now()->format('Y-m-d') }}" style="width:160px;">
    <button class="btn btn-green" onclick="confirmBulk()">
        <i class="fa fa-check-double"></i> Xác nhận đã chọn
    </button>
    <button class="btn btn-ghost btn-sm" onclick="toggleAll()">Chọn tất cả</button>
</div>

<!-- TABLE -->
<div class="table-wrap" style="margin-bottom:16px;">
    <table>
        <thead>
            <tr>
                <th style="width:36px;"><input type="checkbox" id="chkAll" onchange="toggleAllCheck(this)"></th>
                <th>#</th>
                <th>Phiếu PS</th>
                <th>Mã Hàng</th>
                <th>Kích Thước</th>
                <th>Màu Vải</th>
                <th>SL Đơn Hàng</th>
                <th>SL Nhãn</th>
                <th>Nơi Giao</th>
                <th>Gia Công</th>
                <th>Tháng Chốt</th>
                <th>Ngày Xuất Kho</th>
                <th>Thao Tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($xuatKhoList as $ps)
            @php $xuatRoi = !empty($ps->ngay_xuat_kho); @endphp
            <tr class="{{ $xuatRoi ? 'confirmed-row' : '' }}" id="row-xk-{{ $ps->id }}">
                <td>
                    @if(!$xuatRoi)
                    <input type="checkbox" class="xk-chk" value="{{ $ps->id }}">
                    @endif
                </td>
                <td style="color:var(--muted)">{{ $loop->iteration + ($xuatKhoList->currentPage()-1)*$xuatKhoList->perPage() }}</td>
                <td><strong style="color:var(--accent2)">{{ $ps->phieu_ps }}</strong></td>
                <td>{{ $ps->ma_hang }}</td>
                <td>{{ $ps->kich_thuoc }}</td>
                <td>{{ $ps->mau_vai }}</td>
                <td style="text-align:right;">{{ $ps->so_luong_donhang }}</td>
                <td style="text-align:right;">{{ $ps->so_luong_nhan }}</td>
                <td>{{ $ps->noi_giao }}</td>
                <td>{{ $ps->gia_cong }}</td>
                <td>{{ $ps->thang_chot }}</td>
                <td id="ngay-{{ $ps->id }}">
                    @if($xuatRoi)
                        <span class="badge badge-blue">📦 {{ $ps->ngay_xuat_kho }}</span>
                    @else
                        <span style="color:var(--muted)">—</span>
                    @endif
                </td>
                <td>
                    @if(!$xuatRoi)
                    <div class="xk-actions">
                        <input type="date" class="form-control" style="width:140px;" id="date-{{ $ps->id }}" value="{{ now()->format('Y-m-d') }}">
                        <button class="btn btn-green btn-sm" onclick="confirmXuatKho({{ $ps->id }})">
                            <i class="fa fa-check"></i> Xác nhận
                        </button>
                    </div>
                    @else
                        <span style="color:var(--green);font-size:12px;"><i class="fa fa-check-circle"></i> Đã xuất</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" style="text-align:center;color:var(--muted);padding:48px;">
                    <div style="font-size:32px;margin-bottom:8px;">🎉</div>
                    @if(request('trang_thai_xk') === 'da_xk')
                        Chưa có phiếu nào được xuất kho
                    @else
                        Không có phiếu chờ xuất kho
                    @endif
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- PAGINATION -->
<div class="pagination">
    {{ $xuatKhoList->links() }}
</div>

@endsection

@push('scripts')
<script>
function toggleAllCheck(master) {
    document.querySelectorAll('.xk-chk').forEach(c => c.checked = master.checked);
}

function toggleAll() {
    const chks = document.querySelectorAll('.xk-chk');
    const anyUnchecked = [...chks].some(c => !c.checked);
    chks.forEach(c => c.checked = anyUnchecked);
    document.getElementById('chkAll').checked = anyUnchecked;
}

async function confirmXuatKho(id) {
    const ngay = document.getElementById('date-' + id)?.value || '{{ now()->format("Y-m-d") }}';
    const btn  = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

    try {
        const res = await post(`/san-xuat/xuat-kho/${id}`, { ngay_xuat_kho: ngay });
        if (res.success) {
            showToast(res.message, 'success');
            // Update UI
            const row  = document.getElementById('row-xk-' + id);
            const cell = document.getElementById('ngay-' + id);
            cell.innerHTML = `<span class="badge badge-blue">📦 ${ngay}</span>`;
            // Remove action cell controls
            row.querySelector('.xk-actions').innerHTML = `<span style="color:var(--green);font-size:12px;"><i class="fa fa-check-circle"></i> Đã xuất</span>`;
            row.classList.add('confirmed-row');
        } else {
            showToast(res.message || 'Lỗi', 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-check"></i> Xác nhận';
        }
    } catch(e) {
        showToast('Lỗi kết nối', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-check"></i> Xác nhận';
    }
}

async function confirmBulk() {
    const ids  = [...document.querySelectorAll('.xk-chk:checked')].map(c => c.value);
    const ngay = document.getElementById('bulkDate').value;

    if (!ids.length) { showToast('Chưa chọn phiếu nào', 'error'); return; }
    if (!confirm(`Xác nhận xuất kho ${ids.length} phiếu vào ngày ${ngay}?`)) return;

    let successCount = 0;
    for (const id of ids) {
        try {
            const res = await post(`/san-xuat/xuat-kho/${id}`, { ngay_xuat_kho: ngay });
            if (res.success) {
                successCount++;
                const row  = document.getElementById('row-xk-' + id);
                const cell = document.getElementById('ngay-' + id);
                if (cell) cell.innerHTML = `<span class="badge badge-blue">📦 ${ngay}</span>`;
                if (row) {
                    const act = row.querySelector('.xk-actions');
                    if (act) act.innerHTML = `<span style="color:var(--green);font-size:12px;"><i class="fa fa-check-circle"></i> Đã xuất</span>`;
                    row.classList.add('confirmed-row');
                }
            }
        } catch(e) {}
    }
    showToast(`Đã xuất kho ${successCount}/${ids.length} phiếu`, 'success');
}
</script>
@endpush
