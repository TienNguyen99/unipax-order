<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no" />
  <title>Nhập SX - Mobile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{margin:0;font-family:Inter,system-ui,Arial;background:linear-gradient(180deg,#071028 0%,#071a2b 100%);color:#fff;text-align:center;}
    .touch-btn{margin:20px auto;padding:60px;width:90%;max-width:400px;font-size:2rem;font-weight:700;border:none;border-radius:24px;
      background:linear-gradient(135deg,#22c55e,#16a34a);color:#042916;box-shadow:0 6px 20px rgba(0,0,0,.4);}
    .overlay{position:fixed;inset:0;display:none;align-items:center;justify-content:center;background:rgba(0,0,0,.8);z-index:100;}
    .form-box{background:#0b1326;border-radius:16px;padding:20px;width:95%;max-width:480px;text-align:left;}
    label{font-weight:600;margin-top:6px;}
    .close-x{position:absolute;top:14px;right:14px;color:#ccc;background:none;border:none;font-weight:bold;}
  </style>
</head>
<body>

  <!-- Nút Import -->
  <div class="container mt-3">
    <h5>Import Lệnh Sản Xuất</h5>
    <form id="importForm" enctype="multipart/form-data">
      <input type="file" id="fileInput" name="file" accept=".xlsx,.xls" class="form-control mb-2" required>
      <button type="submit" class="btn btn-primary btn-sm w-100">Import Excel</button>
    </form>
    <div id="importResult" class="mt-2"></div>
  </div>

  <!-- Nút nhập SX -->
  <button id="touchBtn" class="touch-btn">NHẬP SẢN XUẤT</button>

  <!-- Overlay form -->
  <div id="overlay" class="overlay">
    <div class="form-box position-relative">
      <button class="close-x" id="closeBtn">✕</button>
      <h5 class="text-center mb-3">Nhập Dữ Liệu Sản Xuất</h5>

      <form id="nhapForm" class="row g-2">
        <div class="col-12">
          <label>Mã lệnh</label>
          <select name="lenh_sx" class="form-select" required>
            <option value="">-- Chọn mã lệnh --</option>
            @foreach($lenhSXs as $lenh)
              <option value="{{ $lenh->ma_lenh }}">{{ $lenh->ma_lenh }} - {{ $lenh->description }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-6">
          <label>Công đoạn</label>
          <select name="cong_doan" class="form-select" required>
            <option value="">--</option>
            <option value="1">1</option><option value="2">2</option>
            <option value="3">3</option><option value="4">4</option>
          </select>
        </div>

        <div class="col-6">
          <label>Số lượng đạt</label>
          <input type="number" name="so_luong_dat" class="form-control" required>
        </div>

        <div class="col-6">
          <label>Số lượng lỗi</label>
          <input type="number" name="so_luong_loi" class="form-control">
        </div>

        <div class="col-12">
          <label>Diễn giải</label>
          <input type="text" name="dien_giai" class="form-control">
        </div>

        <div id="alertBox" class="col-12"></div>
        <div class="col-12">
          <button class="btn btn-success w-100 mt-2">Lưu</button>
        </div>
      </form>
    </div>
  </div>

<script>
const overlay=document.getElementById('overlay');
document.getElementById('touchBtn').onclick=()=>overlay.style.display='flex';
document.getElementById('closeBtn').onclick=()=>overlay.style.display='none';

// Gửi form nhập SX
document.getElementById('nhapForm').addEventListener('submit',async function(e){
  e.preventDefault();
  const res=await fetch('{{ route("nhap-sx.submit") }}',{method:'POST',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:new FormData(this)});
  const data=await res.json();
  const alertBox=document.getElementById('alertBox');
  if(data.success){alertBox.innerHTML=`<div class='alert alert-success'>${data.message}</div>`;this.reset();}
  else{alertBox.innerHTML=`<div class='alert alert-danger'>Lỗi: ${data.message}</div>`;}
});

// Import Excel
document.getElementById('importForm').addEventListener('submit',async function(e){
  e.preventDefault();
  const formData=new FormData(this);
  const result=document.getElementById('importResult');
  result.innerHTML=`<div class='text-info'>⏳ Đang import...</div>`;
  const res=await fetch('{{ route("lenh-sx.import") }}',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:formData});
  const data=await res.json();
  if(data.success){result.innerHTML=`<div class='alert alert-success'>${data.message}</div>`;setTimeout(()=>location.reload(),1000);}
  else result.innerHTML=`<div class='alert alert-danger'>${data.message}</div>`;
});
</script>
</body>
</html>
