<?php
return [
    'allowed_office_ips' => [
        '14.191.112.123', // << THAY BẰNG IP TĨNH CỦA VĂN PHÒNG BẠN
        '127.0.0.1',
        '192.168.65.1',   // Cho phép debug ở local
        '192.168.23.161',
        '192.168.23.1',
        '192.168.1.88',
        '192.168.1.5',
        '192.168.1.94'
    ],
    // --- THÊM DÒNG NÀY ---
    'allowed_office_ssids' => [
        'PhongTro', // << THAY BẰNG TÊN WI-FI CỦA BẠN
        'BichCam', // Thêm các tên Wi-Fi khác nếu có
        'VKU-SinhVien',
        'helu',
        'Candles Cafe',
        '<redacted>',
        'NGUYEN THUC',
        'Mai Cafe',
    ],
];
