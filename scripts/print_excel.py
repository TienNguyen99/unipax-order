import sys, win32com.client, pythoncom, traceback, os, io, time

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
if len(sys.argv) < 2:
    print("Thiếu tên sheet")
    sys.exit(1)

sheet_name = sys.argv[1]

try:
    pythoncom.CoInitialize()
    
    # Thử kết nối với Excel đang chạy, nếu không được thì mở mới
    try:
        excel = win32com.client.GetActiveObject("Excel.Application")
    except:
        # Excel chưa chạy, mở Excel mới
        excel = win32com.client.Dispatch("Excel.Application")
        excel.Visible = True
    
    # Thử tìm workbook đã mở (ưu tiên LINK-LENHSANXUAT, fallback LENHSANXUAT-TEXENCO)
    wb_link = None
    wb_texenco = None
    for workbook in excel.Workbooks:
        name_upper = workbook.Name.upper()
        if name_upper.startswith("LINK-LENHSANXUAT"):
            wb_link = workbook
        elif name_upper.startswith("LENHSANXUAT-TEXENCO"):
            wb_texenco = workbook
    
    # Tìm sheet trong các workbook đã mở
    wb = None
    ws = None

    # Ưu tiên 1: tìm trong LINK-LENHSANXUAT trước
    if wb_link is not None:
        try:
            ws = wb_link.Sheets(sheet_name)
            wb = wb_link
        except:
            pass

    # Ưu tiên 2: nếu chưa tìm thấy, tìm trong LENHSANXUAT-TEXENCO
    if ws is None and wb_texenco is not None:
        try:
            ws = wb_texenco.Sheets(sheet_name)
            wb = wb_texenco
        except:
            pass

    # Nếu không tìm thấy sheet ở cả 2 workbook
    if ws is None:
        if wb_link is None and wb_texenco is None:
            print("ERROR::Vui lòng mở file LINK-LENHSANXUAT.xlsx hoặc LENHSANXUAT-TEXENCO.xlsx trong Excel trước khi in")
        else:
            opened = []
            if wb_link: opened.append(wb_link.Name)
            if wb_texenco: opened.append(wb_texenco.Name)
            print(f"ERROR::Không tìm thấy sheet '{sheet_name}' trong {', '.join(opened)}")
        sys.exit(1)

    # Đảm bảo Excel sẵn sàng
    excel.ScreenUpdating = False
    excel.DisplayAlerts = False
    
    # QUAN TRỌNG: Activate sheet trước khi thao tác
    ws.Activate()
    time.sleep(0.3)  # Đợi Excel cập nhật
    
    # 1. Xuất ra PDF trước (để có preview)
    export_dir = os.path.join(os.getcwd(), "public", "preview")
    os.makedirs(export_dir, exist_ok=True)
    pdf_file = os.path.join(export_dir, f"{sheet_name}.pdf")
    
    ws.ExportAsFixedFormat(0, pdf_file)  # 0 = PDF
    print(f"PREVIEW::{pdf_file}")
    
    # 2. Thử in ra máy in
    try:
        # Kiểm tra xem có máy in không
        if excel.ActivePrinter:
            # In sheet hiện tại (đã activate ở trên)
            ws.PrintOut(Copies=1)
            print(f"SUCCESS::Đang in sheet {sheet_name} chờ xíu . . .")
        else:
            print(f"SUCCESS::Đã tạo PDF cho sheet {sheet_name} (không có máy in)")
    except Exception as print_error:
        # Nếu in không được, vẫn thành công vì đã có PDF
        print(f"WARNING::Không thể in trực tiếp ({print_error})")
        print(f"SUCCESS::Đã tạo PDF cho sheet {sheet_name}")
    
    excel.DisplayAlerts = True
    excel.ScreenUpdating = True

except Exception as e:
    print(f"ERROR::Lỗi khi xử lý: {e}")
    traceback.print_exc()
    sys.exit(1)
