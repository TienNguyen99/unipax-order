import sys, win32com.client, pythoncom, traceback, os, io, time, socket

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
if len(sys.argv) < 2:
    print("Thiếu tên sheet")
    sys.exit(1)

sheet_name = sys.argv[1]

# ================= CẤU HÌNH MÁY IN THEO WORKBOOK =================
PRINTER_MAP = {
    "LINK-LENHSANXUAT": {
        "name": "KONICA MINOLTA 206",
        "ip": "192.168.1.200",
        "port": 9100,
    },
    "LENHSANXUAT-TEXENCO": {
        "name": "\\\\192.168.1.81\\Canon LBP2900",
        "ip": "192.168.1.81",
        "port": None,
    },
}

def check_printer_online(ip, port=9100, timeout=3):
    """Kiểm tra máy in có online không qua TCP"""
    try:
        sock = socket.create_connection((ip, port), timeout=timeout)
        sock.close()
        return True
    except (socket.timeout, ConnectionRefusedError, OSError):
        return False

def find_active_printer_name(excel, printer_keyword):
    """Tìm tên đầy đủ của máy in (bao gồm 'on NeXX:') từ danh sách máy in Windows"""
    # Lưu printer hiện tại
    original = excel.ActivePrinter
    # Thử quét các port phổ biến
    for port_id in range(20):
        try:
            test_name = f"{printer_keyword} on Ne{port_id:02d}:"
            excel.ActivePrinter = test_name
            # Nếu set thành công thì đây là tên đúng
            return test_name
        except:
            continue
    # Khôi phục
    try:
        excel.ActivePrinter = original
    except:
        pass
    return None

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
    source_wb_key = None  # Để xác định máy in

    # Ưu tiên 1: tìm trong LINK-LENHSANXUAT trước
    if wb_link is not None:
        try:
            ws = wb_link.Sheets(sheet_name)
            wb = wb_link
            source_wb_key = "LINK-LENHSANXUAT"
        except:
            pass

    # Ưu tiên 2: nếu chưa tìm thấy, tìm trong LENHSANXUAT-TEXENCO
    if ws is None and wb_texenco is not None:
        try:
            ws = wb_texenco.Sheets(sheet_name)
            wb = wb_texenco
            source_wb_key = "LENHSANXUAT-TEXENCO"
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

    # Xác định máy in dựa trên workbook nguồn
    printer_config = PRINTER_MAP.get(source_wb_key)
    printer_display = printer_config["name"] if printer_config else "Mặc định"
    print(f"INFO::Sheet '{sheet_name}' từ {source_wb_key} -> Máy in: {printer_display}")

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
    
    # 2. In ra máy in theo workbook nguồn
    try:
        if printer_config:
            # Lưu máy in cũ để khôi phục sau
            original_printer = None
            try:
                original_printer = excel.ActivePrinter
            except:
                pass

            # Tìm tên đầy đủ của máy in (với port "on NeXX:")
            full_printer_name = find_active_printer_name(excel, printer_config["name"])
            
            if full_printer_name:
                ws.PrintOut(Copies=1, ActivePrinter=full_printer_name)
                print(f"SUCCESS::Đang in sheet {sheet_name} trên {printer_config['name']} chờ xíu . . .")
            else:
                # Fallback: thử in trực tiếp với tên máy in
                ws.PrintOut(Copies=1, ActivePrinter=printer_config["name"])
                print(f"SUCCESS::Đang in sheet {sheet_name} trên {printer_config['name']} chờ xíu . . .")
            
            # Khôi phục máy in mặc định
            if original_printer:
                try:
                    excel.ActivePrinter = original_printer
                except:
                    pass
        else:
            # Không xác định được máy in, in mặc định
            ws.PrintOut(Copies=1)
            print(f"SUCCESS::Đang in sheet {sheet_name} (máy in mặc định) chờ xíu . . .")
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
