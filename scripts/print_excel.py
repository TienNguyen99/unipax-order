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
        "name": "\\\\192.168.1.34\\HP LaserJet Professional M1212nf MFP",
        "ip": "192.168.1.34",
        "port": 9100,
        "is_network": True,
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

def get_all_excel_instances():
    """Lấy tất cả Excel instances đang chạy (hỗ trợ nhiều cửa sổ Excel)"""
    instances = {}  # Hwnd -> Excel Application
    
    # Cách 1: GetActiveObject - lấy instance chính
    try:
        excel = win32com.client.GetActiveObject("Excel.Application")
        instances[excel.Hwnd] = excel
    except:
        pass
    
    # Cách 2: Tìm qua ROT (Running Object Table) để lấy tất cả instances
    try:
        context = pythoncom.CreateBindCtx(0)
        rot = pythoncom.GetRunningObjectTable(0)
        enum = rot.EnumRunning()
        
        while True:
            monikers = enum.Next(1)
            if not monikers:
                break
            moniker = monikers[0]
            try:
                display_name = moniker.GetDisplayName(context, None)
                if display_name and display_name.lower().endswith(('.xlsx', '.xls', '.xlsm', '.xlsb')):
                    obj = rot.GetObject(moniker)
                    dispatch = win32com.client.Dispatch(obj.QueryInterface(pythoncom.IID_IDispatch))
                    try:
                        app = dispatch.Application
                        hwnd = app.Hwnd
                        if hwnd not in instances:
                            instances[hwnd] = app
                    except:
                        pass
            except:
                pass
    except:
        pass
    
    return list(instances.values())

try:
    pythoncom.CoInitialize()
    
    # Lấy tất cả Excel instances đang chạy
    excel_instances = get_all_excel_instances()
    
    if not excel_instances:
        print("ERROR::Không tìm thấy Excel đang chạy. Vui lòng mở Excel trước.")
        sys.exit(1)
    
    # Quét tất cả instances để tìm workbook
    wb_link = None
    wb_texenco = None
    excel_for_link = None
    excel_for_texenco = None
    
    for excel_inst in excel_instances:
        try:
            for workbook in excel_inst.Workbooks:
                name_upper = workbook.Name.upper()
                if name_upper.startswith("LINK-LENHSANXUAT") and wb_link is None:
                    wb_link = workbook
                    excel_for_link = excel_inst
                elif name_upper.startswith("LENHSANXUAT-TEXENCO") and wb_texenco is None:
                    wb_texenco = workbook
                    excel_for_texenco = excel_inst
        except:
            continue
    
    print(f"INFO::Tìm thấy {len(excel_instances)} Excel instance(s), LINK={'Có' if wb_link else 'Không'}, TEXENCO={'Có' if wb_texenco else 'Không'}")
    
    # Tìm sheet trong các workbook đã mở
    wb = None
    ws = None
    source_wb_key = None
    excel = None  # Excel instance chứa sheet cần in

    # Ưu tiên 1: tìm trong LENHSANXUAT-TEXENCO trước
    if wb_texenco is not None:
        try:
            ws = wb_texenco.Sheets(sheet_name)
            wb = wb_texenco
            source_wb_key = "LENHSANXUAT-TEXENCO"
            excel = excel_for_texenco
        except:
            pass

    # Ưu tiên 2: nếu chưa tìm thấy, tìm trong LINK-LENHSANXUAT
    if ws is None and wb_link is not None:
        try:
            ws = wb_link.Sheets(sheet_name)
            wb = wb_link
            source_wb_key = "LINK-LENHSANXUAT"
            excel = excel_for_link
        except:
            pass

    # Nếu không tìm thấy sheet ở cả 2 workbook
    if ws is None:
        if wb_link is None and wb_texenco is None:
            print("ERROR::Vui lòng mở file LINK-LENHSANXUAT.xlsx hoặc LENHSANXUAT-TEXENCO.xlsx trong Excel trước khi in")
        else:
            opened = []
            not_opened = []
            if wb_link: opened.append(wb_link.Name)
            else: not_opened.append("LINK-LENHSANXUAT.xlsx")
            if wb_texenco: opened.append(wb_texenco.Name)
            else: not_opened.append("LENHSANXUAT-TEXENCO.xlsx")
            
            msg = f"Không tìm thấy sheet '{sheet_name}' trong {', '.join(opened)}"
            if not_opened:
                msg += f". Thử mở thêm {', '.join(not_opened)}?"
            print(f"ERROR::{msg}")
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

            # Xác định tên máy in đầy đủ
            if printer_config.get("is_network"):
                # Network printer: thử trực tiếp UNC path với các port
                full_printer_name = None
                for port_id in range(20):
                    try:
                        test_name = f"{printer_config['name']} on Ne{port_id:02d}:"
                        excel.ActivePrinter = test_name
                        full_printer_name = test_name
                        break
                    except:
                        continue
                
                if not full_printer_name:
                    # Fallback: thử không có port
                    full_printer_name = printer_config["name"]
            else:
                # Local printer: tìm qua tên
                full_printer_name = find_active_printer_name(excel, printer_config["name"])
                if not full_printer_name:
                    full_printer_name = printer_config["name"]
            
            print(f"INFO::Sử dụng printer: {full_printer_name}")
            ws.PrintOut(Copies=1, ActivePrinter=full_printer_name)
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
