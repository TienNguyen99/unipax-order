const express = require('express');
const axios = require('axios');
const printer = require('pdf-to-printer');
const fs = require('fs');
const path = require('path');
const cors = require('cors');

const app = express();
app.use(cors());
app.use(express.json());

const PORT = 3333;
const TEMP_DIR = path.join(__dirname, 'temp');
const API_KEY = 'IN_LBP2900_2025';

// ================= CẤU HÌNH 3 KHU VỰC =================
const PRINTERS_CONFIG = {
    'khu_vuc_1': {
        name: '\\\\192.168.1.81\\Canon LBP2900',
        location: 'Khu vực 1',
        ip: '192.168.1.81'
    },
    'khu_vuc_2': {
        name: '\\\\192.168.1.82\\Canon LBP2900',
        location: 'Khu vực 2',
        ip: '192.168.1.82'
    },
    'khu_vuc_3': {
        name: '\\\\192.168.1.83\\Canon LBP2900',
        location: 'Khu vực 3',
        ip: '192.168.1.83'
    }
};

// Default printer (khu vực 1) nếu không chỉ định
const DEFAULT_PRINTER = 'khu_vuc_1';
const FALLBACK_PRINTER = PRINTERS_CONFIG[DEFAULT_PRINTER].name;

if (!fs.existsSync(TEMP_DIR)) fs.mkdirSync(TEMP_DIR);

// ================= IN PDF =================
app.post('/print', async (req, res) => {
    try {
        // bảo mật
        if (req.headers['x-api-key'] !== API_KEY) {
            return res.status(401).json({ success: false, message: 'Unauthorized' });
        }

        const { pdf_url, khu_vuc = DEFAULT_PRINTER } = req.body;

        // Kiểm tra pdf_url
        if (!pdf_url) {
            return res.status(400).json({ success: false, message: 'Thiếu pdf_url' });
        }

        // Kiểm tra khu_vuc hợp lệ
        if (!PRINTERS_CONFIG[khu_vuc]) {
            return res.status(400).json({ 
                success: false, 
                message: `Khu vực không hợp lệ. Các khu vực hợp lệ: ${Object.keys(PRINTERS_CONFIG).join(', ')}` 
            });
        }

        const printerConfig = PRINTERS_CONFIG[khu_vuc];

        console.log(`📤 In document tới ${printerConfig.location} (${printerConfig.name})`);

        // tải PDF
        const response = await axios.get(pdf_url, {
            responseType: 'arraybuffer',
            timeout: 30000
        });

        const filePath = path.join(TEMP_DIR, `sx_${khu_vuc}_${Date.now()}.pdf`);
        fs.writeFileSync(filePath, response.data);

        // in ra máy in theo khu vực
        await printer.print(filePath, {
            printer: printerConfig.name
        });

        // xóa file sau 15s
        setTimeout(() => {
            if (fs.existsSync(filePath)) {
                fs.unlinkSync(filePath);
                console.log(`🗑️ Deleted ${filePath}`);
            }
        }, 15000);

        res.json({ 
            success: true, 
            message: `🖨️ In ${printerConfig.location} OK`,
            khu_vuc,
            printer_name: printerConfig.name
        });

    } catch (err) {
        console.error('PRINT ERROR:', err.message);
        res.status(500).json({ 
            success: false, 
            message: `Lỗi in: ${err.message}` 
        });
    }
});

// ================= LIỆT KÊ MÁY IN (theo khu vực) =================
app.get('/printers', async (req, res) => {
    try {
        const printers = await printer.getPrinters();
        
        // Trả về cấu hình khu vực + danh sách máy in
        res.json({ 
            success: true, 
            printers,
            khu_vuc_config: PRINTERS_CONFIG,
            default_printer: DEFAULT_PRINTER
        });
    } catch (err) {
        res.status(500).json({ success: false, message: err.message });
    }
});

// ================= LẤY THÔNG TIN KHU VỰC =================
app.get('/khu-vuc', (req, res) => {
    const khuVucList = Object.entries(PRINTERS_CONFIG).map(([key, value]) => ({
        id: key,
        name: value.location,
        printer: value.name
    }));

    res.json({ 
        success: true, 
        khu_vuc: khuVucList,
        default: DEFAULT_PRINTER
    });
});

// ================= TEST =================
app.get('/', (req, res) => {
    res.send('LBP2900 Printer Service (3 Khu Vực) running...');
});

app.listen(PORT, () => {
    console.log(`🖨️ Printer Service http://localhost:${PORT}`);
    console.log('📍 Các khu vực được cấu hình:');
    Object.entries(PRINTERS_CONFIG).forEach(([key, value]) => {
        console.log(`   - ${key}: ${value.location} (${value.name})`);
    });
});
