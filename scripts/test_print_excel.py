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

# 3. Kiểm tra kết nối Excel
try:
    pythoncom.CoInitialize()
    excel = win32com.client.GetActiveObject("Excel.Application")
    print(f"✓ Excel đang chạy: {excel.Version}")
    
    # Kiểm tra workbook
    if excel.Workbooks.Count > 0:
        print(f"✓ Số workbook đang mở: {excel.Workbooks.Count}")
        for i, wb in enumerate(excel.Workbooks, 1):
            print(f"  [{i}] {wb.Name}")
            
        # Tìm workbook LINK-LENHSANXUAT.xlsx
        target_wb = None
        for wb in excel.Workbooks:
            if wb.Name == "LINK-LENHSANXUAT.xlsx":
                target_wb = wb
                break
        
        if target_wb:
            print(f"✓ Tìm thấy workbook: LINK-LENHSANXUAT.xlsx")
            print(f"  Số sheet: {target_wb.Sheets.Count}")
            print("  Danh sách sheet:")
            for sheet in target_wb.Sheets:
                print(f"    - {sheet.Name}")
        else:
            print("✗ KHÔNG tìm thấy workbook: LINK-LENHSANXUAT.xlsx")
            print("  Vui lòng mở file này trong Excel trước khi chạy print_excel.py")
    else:
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
