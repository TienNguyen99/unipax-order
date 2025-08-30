import sys, win32com.client, pythoncom, traceback, os, io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
if len(sys.argv) < 2:
    print("Thiếu tên sheet")
    sys.exit(1)

sheet_name = sys.argv[1]

try:
    pythoncom.CoInitialize()
    excel = win32com.client.GetActiveObject("Excel.Application")
    wb = excel.Workbooks("Triển khai đơn hàng từ lệnh 1000 2025.xlsx")
    ws = wb.Sheets(sheet_name)

    # 1. Gửi lệnh in
    ws.PrintOut(From=1, To=1)

    # 2. Xuất ra PDF để preview
    export_dir = os.path.join(os.getcwd(), "public", "preview")
    os.makedirs(export_dir, exist_ok=True)

    pdf_file = os.path.join(export_dir, f"{sheet_name}.pdf")
    ws.ExportAsFixedFormat(0, pdf_file)  # 0 = PDF

    print(f"PREVIEW::{pdf_file}")
    print(f"SUCCESS::Đang in sheet {sheet_name} chờ xíu . . .")

except Exception as e:
    print("Lỗi:", e)
    traceback.print_exc()
    sys.exit(1)
