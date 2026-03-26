@extends('san-xuat.layouts.app')

@section('title', 'Nhập QC & Xuất Kho')
@section('page-title', '✍️ Nhập QC → Lưu Phiếu Xuất Kho')

@push('styles')
<style>
/* ── STEPS INDICATOR ── */
.steps {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 24px;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 14px 20px;
    overflow-x: auto;
}
.step {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.step-num {
    width: 30px; height: 30px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; font-weight: 700;
    background: var(--surface2);
    color: var(--muted);
    border: 2px solid var(--border);
    transition: all .25s;
}
.step-label { font-size: 13px; color: var(--muted); font-weight: 500; }
.step.active .step-num  { background: var(--accent); border-color: var(--accent); color: #fff; }
.step.active .step-label { color: var(--text); }
.step.done .step-num    { background: rgba(34,197,94,.2); border-color: var(--green); color: var(--green); }
.step.done .step-label  { color: var(--green); }
.step-sep {
    flex: 1; min-width: 24px;
    height: 1px;
    background: var(--border);
    margin: 0 10px;
}

/* ── SEARCH ZONE ── */
.search-zone {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 18px 20px;
    margin-bottom: 16px;
}
.search-input-wrap {
    display: flex; gap: 10px; align-items: center;
}
.search-input-wrap input {
    flex: 1;
    font-size: 16px;
    padding: 10px 16px;
}
.suggestions-wrap {
    display: flex; gap: 8px; flex-wrap: wrap; margin-top: 12px;
}
.sug-chip {
    padding: 4px 12px;
    border-radius: 99px;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--muted);
    font-size: 12px;
    cursor: pointer;
    transition: all .15s;
}
.sug-chip:hover { background: rgba(99,102,241,.2); border-color: var(--accent); color: var(--accent2); }

/* ── RESULT TABLE ── */
.result-count { font-size: 12px; color: var(--muted); margin-bottom: 10px; }

/* ── Input table ── */
.qc-input {
    width: 70px;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 6px;
    padding: 4px 6px;
    font-size: 13px;
    text-align: center;
}
.qc-input:focus { outline: none; border-color: var(--accent); }
input[type=number]::-webkit-inner-spin-button { -webkit-appearance: none; }

/* ── CART / PHIEU STAGING ── */
.staging-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}
.staging-header {
    background: var(--surface2);
    padding: 12px 18px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.staging-header .title { font-size: 14px; font-weight: 600; }
.staging-table th, .staging-table td { padding: 8px 10px; }
.staging-empty {
    text-align: center; padding: 32px;
    color: var(--muted); font-size: 13px;
}
.staging-footer {
    padding: 14px 18px;
    border-top: 1px solid var(--border);
    display: flex; gap: 10px; align-items: center; justify-content: space-between;
}

/* ── TWO COLUMN LAYOUT ── */
.layout-2col {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 16px;
    align-items: start;
}
@media (max-width: 1100px) {
    .layout-2col { grid-template-columns: 1fr; }
}

/* ── GLOW on add ── */
@keyframes rowGlow {
    from { background: rgba(99,102,241,.3); }
    to   { background: transparent; }
}
.row-glow { animation: rowGlow 1s ease-out; }

/* ── Modal ── */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,.6);
    z-index: 900;
    backdrop-filter: blur(4px);
}
.modal-overlay.open { display: flex; align-items: center; justify-content: center; }
.modal-box {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    width: 560px;
    max-width: 95vw;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,.5);
    animation: slideUp .25s;
}
@keyframes slideUp { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
.modal-head {
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; justify-content: space-between;
}
.modal-head .title { font-size: 15px; font-weight: 600; }
.modal-body-inner { padding: 20px 22px; }
.modal-foot {
    padding: 14px 22px;
    border-top: 1px solid var(--border);
    display: flex; justify-content: flex-end; gap: 10px;
}
.close-btn {
    background: none; border: none; color: var(--muted);
    font-size: 18px; cursor: pointer;
}
.close-btn:hover { color: var(--text); }
.info-pill {
    display: inline-flex; align-items: center; gap: 6px;
    background: var(--surface2);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: 5px 12px;
    font-size: 12px;
    color: var(--muted);
    margin-bottom: 4px;
}
.info-pill strong { color: var(--text); }
.input-pair { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px; }
.field-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; font-weight: 600; text-transform: uppercase; }
.modal-input {
    width: 100%;
    background: var(--surface2);
    border: 1px solid var(--border);
    color: var(--text);
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    font-family: inherit;
}
.modal-input:focus { outline: none; border-color: var(--accent); }
.type-badge {
    display: inline-block;
    padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;
}
.type-makhac { background: rgba(99,102,241,.15); color: #818cf8; }
.type-front   { background: rgba(59,130,246,.15);  color: #60a5fa; }
.type-back    { background: rgba(249,115,22,.15);  color: #fb923c; }
</style>
@endpush

@section('content')

<!-- STEPS -->
<div class="steps">
    <div class="step active" id="step1-ind">
        <div class="step-num">1</div>
        <div class="step-label">Tìm Phiếu PS</div>
    </div>
    <div class="step-sep"></div>
    <div class="step" id="step2-ind">
        <div class="step-num">2</div>
        <div class="step-label">Nhập Đạt / Lỗi</div>
    </div>
    <div class="step-sep"></div>
    <div class="step" id="step3-ind">
        <div class="step-num">3</div>
        <div class="step-label">Lưu Phiếu Xuất Kho</div>
    </div>
    <div class="step-sep"></div>
    <div class="step" id="step4-ind">
        <div class="step-num">4</div>
        <div class="step-label">In Excel</div>
    </div>
</div>

<div class="layout-2col">

    <!-- LEFT: Search + Results -->
    <div>
        <!-- STEP 1: Search -->
        <div class="search-zone">
            <div style="font-size:12px;color:var(--muted);margin-bottom:10px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;">
                Bước 1 — Tìm phiếu PS
            </div>
            <div class="search-input-wrap">
                <input type="text" id="searchInput" class="form-control"
                       placeholder="🔍 Nhập mã PS, mã hàng, mã lệnh, vị trí..."
                       autocomplete="off" autofocus>
                <button class="btn btn-primary" onclick="doSearch()">Tìm</button>
            </div>
            <div class="suggestions-wrap" id="suggestions"></div>
        </div>

        <!-- STEP 2: Results + QC input -->
        <div id="resultsWrap" style="display:none;">
            <div class="result-count" id="resultCount"></div>
            <div class="table-wrap" style="margin-bottom:10px;">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="background:var(--surface2);padding:9px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);text-transform:uppercase;white-space:nowrap;">Phiếu PS</th>
                            <th style="background:var(--surface2);padding:9px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);text-transform:uppercase;">Mã Hàng</th>
                            <th style="background:var(--surface2);padding:9px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);">KT</th>
                            <th style="background:var(--surface2);padding:9px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);text-align:right;">SL ĐH</th>
                            <th style="background:var(--surface2);padding:9px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);">Kiểu QC</th>
                            <th style="background:var(--surface2);padding:9px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);text-align:center;">Thêm</th>
                        </tr>
                    </thead>
                    <tbody id="resultsTbody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT: Staging cart + Save -->
    <div>
        <div class="staging-panel" id="stagingPanel">
            <div class="staging-header">
                <div class="title">📋 Phiếu Xuất Kho <span id="cartCount" style="color:var(--accent2)">0 mục</span></div>
                <button class="btn btn-ghost btn-sm" onclick="clearCart()" title="Xóa tất cả">🗑</button>
            </div>

            <div id="cartEmpty" class="staging-empty">
                <div style="font-size:28px;margin-bottom:6px;">📭</div>
                Chưa có phiếu nào.<br>Tìm và thêm từ bên trái.
            </div>

            <div id="cartTableWrap" style="display:none;overflow-x:auto;">
                <table class="staging-table" style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="background:var(--surface2);padding:8px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);">PS</th>
                            <th style="background:var(--surface2);padding:8px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);">Đạt</th>
                            <th style="background:var(--surface2);padding:8px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);">Lỗi</th>
                            <th style="background:var(--surface2);padding:8px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);text-align:center;">✏️</th>
                            <th style="background:var(--surface2);padding:8px 10px;border-bottom:1px solid var(--border);font-size:11px;color:var(--muted);text-align:center;">🗑</th>
                        </tr>
                    </thead>
                    <tbody id="cartTbody"></tbody>
                </table>
            </div>

            <div class="staging-footer">
                <div>
                    <div style="font-size:11px;color:var(--muted);margin-bottom:6px;">Ghi chú phiếu</div>
                    <input type="text" id="ghiChuPhieu" class="form-control" placeholder="Ghi chú (tùy chọn)" style="width:210px;">
                </div>
                <div style="display:flex;gap:8px;flex-direction:column;align-items:flex-end;">
                    <button class="btn btn-primary" id="btnSaveCart" onclick="saveCart()" style="width:140px;">
                        <i class="fa fa-floppy-disk"></i> Lưu Phiếu
                    </button>
                    <a id="btnPrintExcel" href="#" class="btn btn-ghost btn-sm" style="display:none;width:140px;text-align:center;">
                        <i class="fa fa-file-excel"></i> In Excel
                    </a>
                </div>
            </div>

            <!-- Saved phieu info -->
            <div id="savedInfo" style="display:none;padding:14px 18px;border-top:1px solid var(--border);background:rgba(34,197,94,.07);">
                <div style="color:var(--green);font-size:13px;font-weight:600;margin-bottom:6px;">✅ Phiếu đã lưu thành công!</div>
                <div id="savedMaPhieu" style="font-size:12px;color:var(--muted);margin-bottom:8px;"></div>
                <a id="btnViewPhieu" href="#" class="btn btn-ghost btn-sm">
                    <i class="fa fa-eye"></i> Xem phiếu
                </a>
            </div>
        </div>
    </div>

</div>

<!-- QC ENTRY MODAL -->
<div class="modal-overlay" id="qcModal">
    <div class="modal-box">
        <div class="modal-head">
            <div class="title">✍️ Nhập QC — <span id="mPhieuPs"></span></div>
            <button class="close-btn" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-body-inner">
            <input type="hidden" id="mId">
            <input type="hidden" id="mMaHang">
            <input type="hidden" id="mQcType">

            <!-- Info pills -->
            <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:16px;">
                <div class="info-pill"><span>Mã Hàng:</span> <strong id="mInfoMaHang"></strong></div>
                <div class="info-pill"><span>Mã Lệnh:</span> <strong id="mInfoMaLenh"></strong></div>
                <div class="info-pill"><span>Kích Thước:</span> <strong id="mInfoKT"></strong></div>
                <div class="info-pill"><span>SL Đơn Hàng:</span> <strong id="mInfoSL"></strong></div>
                <div class="info-pill"><span>Nơi Giao:</span> <strong id="mInfoNoiGiao"></strong></div>
            </div>

            <div id="mFieldsMakhac" style="display:none">
                <div style="font-size:11px;color:var(--muted);margin-bottom:10px;font-weight:600;text-transform:uppercase;">
                    Mã Khác <span class="type-badge type-makhac">makhac</span>
                </div>
                <div class="input-pair">
                    <div>
                        <div class="field-label">Makhác Đạt</div>
                        <input type="number" id="mMakhacDat" class="modal-input" placeholder="0" min="0">
                    </div>
                    <div>
                        <div class="field-label" style="color:var(--red)">Makhác Lỗi</div>
                        <input type="number" id="mMakhacLoi" class="modal-input" placeholder="0" min="0">
                    </div>
                </div>
            </div>

            <div id="mFieldsFront" style="display:none">
                <div style="font-size:11px;color:var(--muted);margin-bottom:10px;font-weight:600;text-transform:uppercase;">
                    Front <span class="type-badge type-front">front</span>
                </div>
                <div class="input-pair">
                    <div>
                        <div class="field-label">Front Đạt</div>
                        <input type="number" id="mFrontDat" class="modal-input" placeholder="0" min="0">
                    </div>
                    <div>
                        <div class="field-label" style="color:var(--red)">Front Lỗi</div>
                        <input type="number" id="mFrontLoi" class="modal-input" placeholder="0" min="0">
                    </div>
                </div>
            </div>

            <div id="mFieldsBack" style="display:none">
                <div style="font-size:11px;color:var(--muted);margin-bottom:10px;font-weight:600;text-transform:uppercase;">
                    Back <span class="type-badge type-back">back</span>
                </div>
                <div class="input-pair">
                    <div>
                        <div class="field-label">Back Đạt</div>
                        <input type="number" id="mBackDat" class="modal-input" placeholder="0" min="0">
                    </div>
                    <div>
                        <div class="field-label" style="color:var(--red)">Back Lỗi</div>
                        <input type="number" id="mBackLoi" class="modal-input" placeholder="0" min="0">
                    </div>
                </div>
            </div>

            <div>
                <div class="field-label">Ghi Chú</div>
                <textarea id="mGhiChu" class="modal-input" rows="2" placeholder="Ghi chú (tùy chọn)"></textarea>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn btn-ghost" onclick="closeModal()">Hủy</button>
            <button class="btn btn-primary" onclick="addToCart()">
                <i class="fa fa-plus"></i> Thêm vào phiếu
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const SEARCH_URL   = '{{ route("phieu-ve-entry.search") }}';
const SAVE_CART_URL = '{{ route("phieu-ve-entry.save-cart") }}';
const PRINT_BASE   = '{{ url("/phieu-xuat-kho") }}';

// Mã hàng đặc biệt (chỉ nhập Front/Back, không nhập Makhac)
const SPECIAL_MA_HANG = [
    'S2315CA1028','S2315CA1028U','S2515CA02GFU','S2615CA1028U',
    'S2662LHAU350','S2662LHAU351','S2662LHAU362',
    'SMSUB2S26LEN05','SMSUS25RUWFCAP'
];

let cart = {};   // { phieu_id: { ...data } }
let currentModalId = null;

// ── SEARCH ──
const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('keydown', e => { if (e.key === 'Enter') doSearch(); });
searchInput.addEventListener('input',   () => { if (searchInput.value.length >= 2) doSearch(false); });

async function doSearch(saveRecent = true) {
    const val = searchInput.value.trim();
    if (!val) return;

    const res = await post(SEARCH_URL, { phieu_ps: val });

    if (!res.success || !res.data || !res.data.length) {
        document.getElementById('resultsWrap').style.display = 'none';
        showToast('Không tìm thấy: ' + val, 'error');
        return;
    }

    if (saveRecent) saveRecent_(val);
    renderResults(res.data);
    setStep(2);
}

function renderResults(data) {
    const tbody = document.getElementById('resultsTbody');
    tbody.innerHTML = '';

    data.forEach(ps => {
        const isSpecial = SPECIAL_MA_HANG.includes(ps.ma_hang);
        const qcLabel = isSpecial
            ? '<span class="type-badge type-front">Front</span> <span class="type-badge type-back">Back</span>'
            : '<span class="type-badge type-makhac">Makhác</span>';

        const inCart = cart[ps.id] !== undefined;

        const tr = document.createElement('tr');
        tr.id = 'res-row-' + ps.id;
        tr.style.cssText = 'border-bottom:1px solid var(--border);transition:background .1s;';
        tr.innerHTML = `
            <td style="padding:8px 10px;"><strong style="color:var(--accent2)">${ps.phieu_ps ?? '—'}</strong></td>
            <td style="padding:8px 10px;font-size:12px;">${ps.ma_hang ?? '—'}</td>
            <td style="padding:8px 10px;font-size:12px;white-space:nowrap;">${ps.kich_thuoc ?? '—'}</td>
            <td style="padding:8px 10px;text-align:right;font-size:12px;">${ps.so_luong_donhang ?? '—'}</td>
            <td style="padding:8px 10px;">${qcLabel}</td>
            <td style="padding:8px 10px;text-align:center;">
                ${inCart
                    ? `<span class="badge badge-hoan" id="add-btn-${ps.id}">✅ Đã thêm</span>`
                    : `<button class="btn btn-green btn-sm" id="add-btn-${ps.id}" onclick='openModal(${JSON.stringify(ps)})'>
                        <i class="fa fa-plus"></i>
                       </button>`
                }
            </td>
        `;
        tbody.appendChild(tr);
    });

    document.getElementById('resultCount').textContent = `Tìm thấy ${data.length} phiếu`;
    document.getElementById('resultsWrap').style.display = 'block';
}

// ── MODAL ──
function openModal(ps) {
    currentModalId = ps.id;
    const isSpecial = SPECIAL_MA_HANG.includes(ps.ma_hang);

    document.getElementById('mId').value = ps.id;
    document.getElementById('mMaHang').value = ps.ma_hang;
    document.getElementById('mQcType').value = isSpecial ? 'special' : 'normal';

    document.getElementById('mPhieuPs').textContent = ps.phieu_ps;
    document.getElementById('mInfoMaHang').textContent  = ps.ma_hang ?? '—';
    document.getElementById('mInfoMaLenh').textContent  = ps.ma_lenh ?? '—';
    document.getElementById('mInfoKT').textContent      = ps.kich_thuoc ?? '—';
    document.getElementById('mInfoSL').textContent      = ps.so_luong_donhang ?? '—';
    document.getElementById('mInfoNoiGiao').textContent = ps.noi_giao ?? '—';

    // Show/hide fields
    document.getElementById('mFieldsMakhac').style.display = isSpecial ? 'none' : 'block';
    document.getElementById('mFieldsFront').style.display  = isSpecial ? 'block' : 'none';
    document.getElementById('mFieldsBack').style.display   = isSpecial ? 'block' : 'none';

    // Pre-fill existing values
    document.getElementById('mMakhacDat').value = ps.makhac_dat || '';
    document.getElementById('mMakhacLoi').value = ps.makhac_loi || '';
    document.getElementById('mFrontDat').value  = ps.front_dat  || '';
    document.getElementById('mFrontLoi').value  = ps.front_loi  || '';
    document.getElementById('mBackDat').value   = ps.back_dat   || '';
    document.getElementById('mBackLoi').value   = ps.back_loi   || '';
    document.getElementById('mGhiChu').value    = ps.ghi_chu    || '';

    document.getElementById('qcModal').classList.add('open');

    // Focus first visible input
    setTimeout(() => {
        const f = isSpecial ? document.getElementById('mFrontDat') : document.getElementById('mMakhacDat');
        if (f) f.focus();
    }, 80);
}

function closeModal() {
    document.getElementById('qcModal').classList.remove('open');
    currentModalId = null;
}

// Close modal on overlay click
document.getElementById('qcModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

function addToCart() {
    const id     = document.getElementById('mId').value;
    const maHang = document.getElementById('mMaHang').value;
    const type   = document.getElementById('mQcType').value;
    const ps_label = document.getElementById('mPhieuPs').textContent;
    const soLuong  = document.getElementById('mInfoSL').textContent;

    let dat, loi, datKey, loiKey;
    if (type === 'special') {
        datKey = 'front_dat'; loiKey = 'front_loi';
        dat = document.getElementById('mFrontDat').value || 0;
        loi = document.getElementById('mFrontLoi').value || 0;
    } else {
        datKey = 'makhac_dat'; loiKey = 'makhac_loi';
        dat = document.getElementById('mMakhacDat').value || 0;
        loi = document.getElementById('mMakhacLoi').value || 0;
    }

    const entry = {
        id,
        phieu_ps: ps_label,
        ma_hang: maHang,
        so_luong_donhang: soLuong,
        qc_type: type,
        dat_label: type === 'special' ? 'Front' : 'Makhác',
        dat_val: dat,
        loi_val: loi,
        ghi_chu: document.getElementById('mGhiChu').value,
        // All QC fields
        makhac_dat: type !== 'special' ? (document.getElementById('mMakhacDat').value || null) : null,
        makhac_loi: type !== 'special' ? (document.getElementById('mMakhacLoi').value || null) : null,
        front_dat:  type === 'special' ? (document.getElementById('mFrontDat').value || null) : null,
        front_loi:  type === 'special' ? (document.getElementById('mFrontLoi').value || null) : null,
        back_dat:   type === 'special' ? (document.getElementById('mBackDat').value || null) : null,
        back_loi:   type === 'special' ? (document.getElementById('mBackLoi').value || null) : null,
    };

    cart[id] = entry;
    renderCart();
    closeModal();
    setStep(3);

    // Update add button in results
    const btn = document.getElementById('add-btn-' + id);
    if (btn) btn.outerHTML = `<span class="badge badge-hoan" id="add-btn-${id}">✅ Đã thêm</span>`;

    showToast('✅ Đã thêm: ' + ps_label);
}

// ── CART ──
function renderCart() {
    const tbody = document.getElementById('cartTbody');
    const ids   = Object.keys(cart);
    document.getElementById('cartCount').textContent = ids.length + ' mục';

    if (!ids.length) {
        document.getElementById('cartEmpty').style.display = 'block';
        document.getElementById('cartTableWrap').style.display = 'none';
        return;
    }

    document.getElementById('cartEmpty').style.display = 'none';
    document.getElementById('cartTableWrap').style.display = 'block';

    tbody.innerHTML = '';
    ids.forEach(id => {
        const e = cart[id];
        const loiColor = parseFloat(e.loi_val) > 0 ? 'color:var(--red)' : '';
        const tr = document.createElement('tr');
        tr.style.borderBottom = '1px solid var(--border)';
        tr.innerHTML = `
            <td style="padding:7px 10px;font-size:12px;"><strong style="color:var(--accent2)">${e.phieu_ps}</strong><br><span style="color:var(--muted);font-size:11px;">${e.ma_hang}</span></td>
            <td style="padding:7px 10px;text-align:center;font-size:13px;color:var(--green);font-weight:600;">${e.dat_val}</td>
            <td style="padding:7px 10px;text-align:center;font-size:13px;font-weight:600;${loiColor}">${e.loi_val}</td>
            <td style="padding:7px 10px;text-align:center;">
                <button class="btn btn-ghost btn-sm" onclick="editCartItem('${id}')" title="Sửa">✏️</button>
            </td>
            <td style="padding:7px 10px;text-align:center;">
                <button class="btn btn-ghost btn-sm" onclick="removeFromCart('${id}')" title="Xóa" style="color:var(--red)">✕</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

function editCartItem(id) {
    // Re-open modal with existing data
    const e = cart[id];
    const isSpecial = e.qc_type === 'special';
    const ps_fake = {
        id, ma_hang: e.ma_hang, phieu_ps: e.phieu_ps,
        ma_lenh: '', kich_thuoc: '', so_luong_donhang: e.so_luong_donhang, noi_giao: '',
        makhac_dat: e.makhac_dat, makhac_loi: e.makhac_loi,
        front_dat: e.front_dat, front_loi: e.front_loi,
        back_dat: e.back_dat, back_loi: e.back_loi, ghi_chu: e.ghi_chu,
    };
    openModal(ps_fake);
}

function removeFromCart(id) {
    delete cart[id];
    renderCart();
    // Restore add button
    const btn = document.getElementById('add-btn-' + id);
    if (btn) btn.outerHTML = `<button class="btn btn-green btn-sm" id="add-btn-${id}">+ Thêm</button>`;
    if (!Object.keys(cart).length) setStep(2);
}

function clearCart() {
    if (!Object.keys(cart).length) return;
    if (!confirm('Xóa tất cả mục trong phiếu?')) return;
    cart = {};
    renderCart();
    setStep(2);
    document.getElementById('savedInfo').style.display = 'none';
    document.getElementById('btnPrintExcel').style.display = 'none';
}

// ── SAVE CART ──
async function saveCart() {
    const ids = Object.keys(cart);
    if (!ids.length) { showToast('Chưa có phiếu nào', 'error'); return; }

    // Build session-like cart payload using existing route (add-to-cart then save-cart)
    // We use the existing PhieuVeEntryController flow:
    // 1. POST each phieu to add-to-cart endpoint
    // 2. POST save-cart

    const btn = document.getElementById('btnSaveCart');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang lưu...';

    try {
        // Step A: Reset cart on server by adding each item
        const addUrl  = '{{ route("phieu-ve-entry.add-to-cart") }}';
        const saveUrl = SAVE_CART_URL;

        // Clear server session cart first by using save-cart with empty data shouldn't work
        // Instead POST each phieu_id to session
        for (const id of ids) {
            await post(addUrl, { phieu_id: parseInt(id) });
        }

        // Step B: Update each item's QC data in session
        const updateUrl = '{{ route("phieu-ve-entry.update-cart-item") }}';
        for (const id of ids) {
            const e = cart[id];
            await post(updateUrl, {
                phieu_id:    parseInt(id),
                makhac_dat:  e.makhac_dat,
                makhac_loi:  e.makhac_loi,
                front_dat:   e.front_dat,
                front_loi:   e.front_loi,
                back_dat:    e.back_dat,
                back_loi:    e.back_loi,
                ghi_chu:     e.ghi_chu,
            });
        }

        // Step C: Save cart → create PXK
        const res = await post(saveUrl, {
            ghi_chu_phieu: document.getElementById('ghiChuPhieu').value,
        });

        if (res.success) {
            showToast(res.message, 'success');
            setStep(4);

            // Show saved info + print button
            const phieuId = res.phieu_xuat_kho_id;
            const maPhieu = res.ma_phieu;
            document.getElementById('savedInfo').style.display = 'block';
            document.getElementById('savedMaPhieu').textContent = 'Mã phiếu: ' + maPhieu;
            document.getElementById('btnViewPhieu').href = PRINT_BASE + '/' + phieuId;

            const printBtn = document.getElementById('btnPrintExcel');
            printBtn.href = PRINT_BASE + '/' + phieuId + '/print';
            printBtn.style.display = 'block';

            cart = {};
            renderCart();
        } else {
            showToast(res.message || 'Lỗi khi lưu', 'error');
        }
    } catch(e) {
        showToast('Lỗi kết nối: ' + e.message, 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-floppy-disk"></i> Lưu Phiếu';
    }
}

// ── STEPS INDICATOR ──
function setStep(n) {
    for (let i = 1; i <= 4; i++) {
        const el = document.getElementById('step' + i + '-ind');
        if (!el) continue;
        el.classList.remove('active', 'done');
        if (i < n) el.classList.add('done');
        if (i === n) el.classList.add('active');
    }
}

// ── RECENT SEARCHES ──
function saveRecent_(q) {
    let r = JSON.parse(localStorage.getItem('sx_recent') || '[]');
    r = [q, ...r.filter(x => x !== q)].slice(0, 8);
    localStorage.setItem('sx_recent', JSON.stringify(r));
    renderSuggestions(r);
}

function renderSuggestions(list) {
    const wrap = document.getElementById('suggestions');
    wrap.innerHTML = list.length
        ? list.map(q => `<span class="sug-chip" onclick="searchChip('${q}')">${q}</span>`).join('')
        : '';
}

function searchChip(q) {
    searchInput.value = q;
    doSearch();
}

// Load recent on mount
renderSuggestions(JSON.parse(localStorage.getItem('sx_recent') || '[]'));
</script>
@endpush
