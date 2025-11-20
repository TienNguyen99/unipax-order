<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title>PHIẾU SẢN XUẤT CÔNG NHÂN</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://unpkg.com/html5-qrcode"></script>

  <style>
    body {
      margin:0;
      font-family: Inter, system-ui, Arial;
      background: #071028;
      color:#fff;
      text-align:center;
      min-height:100vh;
    }

    #particles-js {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      z-index: -1;
      background: #071028;
    }

    .touch-btn {
      margin:20px auto;
      padding:60px;
      width:90%;
      max-width:400px;
      font-size:2rem;
      font-weight:700;
      border:none;
      border-radius:24px;
      background:linear-gradient(135deg,#22c55e,#16a34a);
      color:#042916;
      box-shadow:0 6px 20px rgba(0,0,0,.4);
      transition:transform .2s ease;
    }
    .touch-btn:active { transform:scale(.95); }

    .overlay{
      position:fixed;
      inset:0;
      display:none;
      align-items:center;
      justify-content:center;
      background:rgba(0,0,0,.85);
      z-index:100;
      overflow-y:auto;
      padding:20px;
      opacity:0;
      transition:opacity .3s ease;
    }
    .overlay.show{ opacity:1; }

    .form-box{
      background:#0b1326;
      border-radius:16px;
      padding:20px;
      width:95%;
      max-width:480px;
      text-align:center;
      box-shadow:0 0 15px rgba(0,0,0,.5);
    }

    .close-x{
      position:absolute;
      top:14px;
      right:14px;
      color:#ccc;
      background:none;
      border:none;
      font-weight:bold;
      font-size:1.3rem;
    }

    .step { display:none; }
    .step.active { display:block; }

    .btn-step{
      font-size:1.3rem;
      font-weight:600;
      padding:16px;
      border-radius:14px;
    }

    .suggest-box{
      max-height:300px;
      overflow-y:auto;
      background:#0f1a34;
      border-radius:10px;
      margin-top:8px;
      text-align:left;
    }
    .suggest-item{
      padding:10px 14px;
      border-bottom:1px solid rgba(255,255,255,.1);
      cursor:pointer;
    }
    .suggest-item:hover{
      background:#1b2b55;
    }
  </style>
</head>
<body>

<!-- Background -->
<div id="particles-js"></div>

<!-- Import -->
<div class="container mt-3">
  <h5>Import Lệnh Sản Xuất</h5>
  <form id="importForm" enctype="multipart/form-data">
    <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" class="form-control mb-2" required>
    <button type="submit" class="btn btn-primary btn-sm w-100">Import Excel</button>
  </form>
  <div id="importResult" class="mt-2"></div>
</div>

<button id="touchBtn" class="touch-btn">NHẬP SẢN XUẤT</button>

<!-- OVERLAY -->
<div id="overlay" class="overlay">
  <div class="form-box position-relative">
    <button class="close-x" id="closeBtn">✕</button>

    <!-- STEP 1 -->
    <div id="step1" class="step active">
      <h5>1️⃣ Tìm Mã Lệnh Sản Xuất</h5>

      <input type="text" id="searchLenh" placeholder="Nhập vài ký tự mã lệnh..." class="form-control form-control-lg mt-3">
      <div id="suggestBox" class="suggest-box mt-2"></div>

      <!-- QR Scanner Button -->
      <button class="btn btn-warning w-100 mt-3" id="scanQRBtn">📷 Quét QR</button>

      <!-- QR Reader Area -->
      <div id="qrReader" style="width:100%; display:none;" class="mt-3"></div>
      <button class="btn btn-secondary w-100 mt-2" id="stopScanBtn" style="display:none;">🛑 Dừng quét</button>
    </div>

    <!-- STEP 2 -->
    <div id="step2" class="step">
      <h5>2️⃣ Chọn Công Đoạn</h5>

      <div class="mt-3">
        <h6 class="text-info text-start mb-2">Tầng Trệt</h6>
        <div class="d-grid gap-2">
          <button class="btn btn-outline-info btn-step congdoan" data-value="DỆT DÂY">DỆT DÂY</button>
          <button class="btn btn-outline-info btn-step congdoan" data-value="DỆT NHÃN">DỆT NHÃN</button>
                    <button class="btn btn-outline-info btn-step congdoan" data-value="QUẤN CUỘN">QUẤN CUỘN</button>

        </div>
      </div>
      <div class="mt-4">
        <h6 class="text-info text-start mb-2">Tầng 1</h6>
        <div class="d-grid gap-2">
                    <button class="btn btn-outline-info btn-step congdoan" data-value="IN">IN</button>
          <button class="btn btn-outline-info btn-step congdoan" data-value="ĐÚC">ĐÚC</button>
          <button class="btn btn-outline-info btn-step congdoan" data-value="CẮT">CẮT</button>


        </div>
      </div>

      <div class="mt-4">
        <h6 class="text-info text-start mb-2">QC</h6>
        <div class="d-grid gap-2">
          <button class="btn btn-outline-warning btn-step congdoan" data-value="KIỂM HÀNG">KIỂM HÀNG</button>
          <button class="btn btn-outline-info btn-step congdoan" data-value="ĐÓNG GÓI">ĐÓNG GÓI</button>
        </div>
      </div>

      <button class="btn btn-secondary mt-4 w-100" id="back1">↩ Quay lại</button>
    </div>

    <!-- STEP 3 -->
    <div id="step3" class="step">
      <h5>3️⃣ Nhập Số Lượng</h5>
      <input type="number" id="soLuongDat" placeholder="Số lượng đạt" class="form-control form-control-lg mb-2">
      <input type="number" id="soLuongLoi" placeholder="Số lượng lỗi" class="form-control form-control-lg mb-2">
      <input type="text" id="dienGiai" placeholder="Diễn giải (nếu có)" class="form-control form-control-lg mb-3">

      <div class="d-flex justify-content-between">
        <button class="btn btn-secondary" id="back2">↩ Quay lại</button>
        <button class="btn btn-success" id="confirmBtn">✅ Tiếp tục</button>
      </div>
    </div>

    <!-- STEP 4 -->
    <div id="step4" class="step">
      <h5>✅ Xác Nhận Dữ Liệu</h5>
      <div id="reviewBox" class="border p-3 mb-3 rounded text-start"></div>
      <div id="alertBox" class="mb-2"></div>
      <div class="d-flex justify-content-between">
        <button class="btn btn-secondary" id="back3">↩ Sửa</button>
        <button class="btn btn-primary" id="submitBtn">📤 Lưu</button>
      </div>
    </div>

  </div>
</div>

<!-- ORIGINAL SCRIPT + QR -->
<script>
const overlay = document.getElementById('overlay');
const nhapData = {};
const suggestBox = document.getElementById('suggestBox');
const searchLenh = document.getElementById('searchLenh');

document.getElementById('touchBtn').onclick = ()=>{
  overlay.style.display = 'flex';
  requestAnimationFrame(()=>overlay.classList.add('show'));
};

document.getElementById('closeBtn').onclick = closeOverlay;
function closeOverlay(){
  overlay.classList.remove('show');
  setTimeout(()=>overlay.style.display='none',300);
}

function showStep(id){
  document.querySelectorAll('.step').forEach(s=>s.classList.remove('active'));
  document.getElementById(id).classList.add('active');
}

/* ======================= SEARCH LỆNH ======================= */
let timer;
searchLenh.addEventListener('input', function(){
  const keyword = this.value.trim();
  clearTimeout(timer);
  if(keyword.length < 2){
    suggestBox.innerHTML = '';
    return;
  }
  timer = setTimeout(async ()=>{
    const res = await fetch(`{{ route('lenh-sx.search') }}?q=${encodeURIComponent(keyword)}`);
    const data = await res.json();
    if(data.length===0){
      suggestBox.innerHTML = "<div class='p-2 text-muted'>Không tìm thấy mã lệnh</div>";
      return;
    }
    suggestBox.innerHTML = data.map(item=>
      `<div class='suggest-item' data-value='${item.ma_lenh}'><b>${item.ma_lenh}</b> - ${item.description||''}</div>`
    ).join('');
    document.querySelectorAll('.suggest-item').forEach(it=>{
      it.onclick = ()=>{
        nhapData.lenh_sx = it.dataset.value;
        showStep('step2');
      };
    });
  }, 400);
});

/* ======================= QR CODE SCAN ======================= */
let qrScanner;
const qrDiv = document.getElementById('qrReader');
const btnScan = document.getElementById('scanQRBtn');
const btnStop = document.getElementById('stopScanBtn');

btnScan.onclick = () => {
  qrDiv.style.display = 'block';
  btnStop.style.display = 'block';

  qrScanner = new Html5Qrcode("qrReader");

  qrScanner.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 240 },
    qrData => {
      nhapData.lenh_sx = qrData;

      qrScanner.stop().then(()=>{
        qrDiv.style.display = 'none';
        btnStop.style.display = 'none';
      });

      showStep("step2");
    }
  );
};

btnStop.onclick = () => {
  qrScanner.stop().then(()=>{
    qrDiv.style.display = 'none';
    btnStop.style.display = 'none';
  });
};

/* ======================= STEP LOGIC ======================= */

document.querySelectorAll('.congdoan').forEach(btn=>{
  btn.onclick=()=>{
    nhapData.cong_doan = btn.dataset.value;
    showStep('step3');
  };
});

document.getElementById('back1').onclick=()=>showStep('step1');
document.getElementById('back2').onclick=()=>showStep('step2');
document.getElementById('back3').onclick=()=>showStep('step3');

document.getElementById('confirmBtn').onclick=()=>{
  nhapData.so_luong_dat = document.getElementById('soLuongDat').value;
  nhapData.so_luong_loi = document.getElementById('soLuongLoi').value;
  nhapData.dien_giai = document.getElementById('dienGiai').value;

  document.getElementById('reviewBox').innerHTML = `
    <b>Mã lệnh:</b> ${nhapData.lenh_sx}<br>
    <b>Công đoạn:</b> ${nhapData.cong_doan}<br>
    <b>Số lượng đạt:</b> ${nhapData.so_luong_dat}<br>
    <b>Số lượng lỗi:</b> ${nhapData.so_luong_loi || 0}<br>
    <b>Diễn giải:</b> ${nhapData.dien_giai || '-'}
  `;
  showStep('step4');
};

document.getElementById('submitBtn').onclick = async ()=>{
  const formData = new FormData();
  for(const k in nhapData) formData.append(k, nhapData[k]);
  const alertBox = document.getElementById('alertBox');
  alertBox.innerHTML = `<div class='text-info'>⏳ Đang lưu...</div>`;

  const res = await fetch('{{ route("nhap-sx.submit") }}', {
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
    body: formData
  });
  const data = await res.json();

  if(data.success){
    alertBox.innerHTML = `<div class='alert alert-success'>${data.message}</div>`;
    setTimeout(()=>{
      window.open(`/bao-cao-sx/pdf/${data.data.id}`,'_blank');
      showStep('step1');
      closeOverlay();
    },1000);
  } else {
    alertBox.innerHTML = `<div class='alert alert-danger'>Lỗi: ${data.message}</div>`;
  }
};

/* ====================== IMPORT ====================== */
document.getElementById('importForm').addEventListener('submit',async function(e){
  e.preventDefault();
  const formData=new FormData(this);
  const result=document.getElementById('importResult');
  result.innerHTML=`<div class='text-info'>⏳ Đang import...</div>`;
  const res=await fetch('{{ route("lenh-sx.import") }}',{
    method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},
    body:formData
  });
  const data=await res.json();
  if(data.success){
    result.innerHTML=`<div class='alert alert-success'>${data.message}</div>`;
    setTimeout(()=>location.reload(),1000);
  } else result.innerHTML=`<div class='alert alert-danger'>${data.message}</div>`;
});
</script>

<!-- PARTICLES -->
<script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
<script>
particlesJS("particles-js",{
  "particles":{"number":{"value":80,"density":{"enable":true,"value_area":800}},
  "color":{"value":"#00ff99"},
  "shape":{"type":"circle"},
  "opacity":{"value":0.5},
  "size":{"value":3,"random":true},
  "line_linked":{"enable":true,"distance":120,"color":"#00ff99","opacity":0.4,"width":1},
  "move":{"enable":true,"speed":1.2,"direction":"none","out_mode":"bounce"}},
  "interactivity":{"events":{"onhover":{"enable":true,"mode":"grab"},
  "onclick":{"enable":true,"mode":"repulse"}},
  "modes":{"grab":{"distance":200,"line_linked":{"opacity":0.9}},
  "repulse":{"distance":150,"duration":0.4}}},
  "retina_detect":true
});
</script>

</body>
</html>
