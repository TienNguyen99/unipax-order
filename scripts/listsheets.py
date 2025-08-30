import sys
import win32com.client
import pythoncom
import traceback
import io

sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8')
try:
    pythoncom.CoInitialize()
    # Gắn vào Excel đang chạy sẵn
    excel = win32com.client.GetActiveObject("Excel.Application")
    
    # Lấy workbook theo tên (phải đang mở sẵn trong Excel)
    wb = excel.Workbooks("Triển khai đơn hàng từ lệnh 1000 2025.xlsx")

    for sheet in wb.Sheets:
        print(sheet.Name)

    # ❌ Không đóng workbook, không quit Excel
    # để Excel vẫn mở như khi bạn in

except Exception as e:
    print("ERROR:", e)
    traceback.print_exc()
    sys.exit(1)
