import sys, win32com.client, pythoncom, io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')

try:
    pythoncom.CoInitialize()
    excel = win32com.client.GetActiveObject("Excel.Application")
    
    print("=== THÔNG TIN MÁY IN ===")
    
    # Kiểm tra máy in hiện tại
    try:
        printer = excel.ActivePrinter
        if printer:
            print(f"✓ Máy in mặc định: {printer}")
        else:
            print("✗ Không có máy in mặc định")
    except Exception as e:
        print(f"✗ Lỗi khi lấy thông tin máy in: {e}")
    
    # Kiểm tra application
    print(f"\n=== THÔNG TIN EXCEL ===")
    print(f"Version: {excel.Version}")
    print(f"Visible: {excel.Visible}")
    print(f"ScreenUpdating: {excel.ScreenUpdating}")
    print(f"DisplayAlerts: {excel.DisplayAlerts}")
    
    wb = excel.Workbooks("LINK-LENHSANXUAT.xlsx")
    print(f"\n=== WORKBOOK ===")
    print(f"Name: {wb.Name}")
    print(f"Sheets: {wb.Sheets.Count}")
    
except Exception as e:
    print(f"Lỗi: {e}")
    import traceback
    traceback.print_exc()
