import sys, io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

print("=== KIỂM TRA MODULES ===")

# 1. Kiểm tra win32com
try:
    import win32com.client
    print("✓ win32com.client: OK")
except ImportError as e:
    print(f"✗ win32com.client: KHÔNG CÀI - {e}")
    sys.exit(1)

# 2. Kiểm tra pythoncom
try:
    import pythoncom
    print("✓ pythoncom: OK")
except ImportError as e:
    print(f"✗ pythoncom: KHÔNG CÀI - {e}")
    sys.exit(1)

# 3. Kiểm tra kết nối Excel (quét tất cả instances)
try:
    pythoncom.CoInitialize()
    
    # Lấy tất cả Excel instances qua ROT
    instances = []
    
    # Cách 1: GetActiveObject
    try:
        excel = win32com.client.GetActiveObject("Excel.Application")
        instances.append(excel)
    except:
        pass
    
    # Cách 2: ROT
    try:
        context = pythoncom.CreateBindCtx(0)
        rot = pythoncom.GetRunningObjectTable(0)
        enum = rot.EnumRunning()
        while True:
            try:
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
                            is_new = True
                            for existing in instances:
                                try:
                                    if existing.Hwnd == app.Hwnd:
                                        is_new = False
                                        break
                                except:
                                    pass
                            if is_new:
                                instances.append(app)
                        except:
                            pass
                except:
                    pass
            except:
                break
    except:
        pass
    
    print(f"✓ Tìm thấy {len(instances)} Excel instance(s)")
    
    wb_count = 0
    for idx, inst in enumerate(instances, 1):
        try:
            print(f"\n--- Excel Instance {idx} (v{inst.Version}) ---")
            for wb in inst.Workbooks:
                wb_count += 1
                print(f"  [{wb_count}] {wb.Name} ({wb.Sheets.Count} sheets)")
        except Exception as e:
            print(f"  Lỗi đọc instance {idx}: {e}")
    
    if wb_count == 0:
        print("✗ Không có workbook nào đang mở")
        
except Exception as e:
    print(f"✗ Excel không chạy hoặc lỗi: {e}")
    print("  Vui lòng mở Excel trước khi chạy script")

print("\n=== KẾT LUẬN ===")
print("Tất cả modules cần thiết đã được cài đặt.")
print("Để print_excel.py hoạt động, cần:")
print("1. Mở Excel")
print("2. Mở file LINK-LENHSANXUAT.xlsx")
print("3. Chạy: python scripts/print_excel.py <tên_sheet>")
