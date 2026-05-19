<?php
// Đặt múi giờ mặc định cho ứng dụng
date_default_timezone_set('Asia/Ho_Chi_Minh');

define('DB_HOST', 'localhost');  
define('DB_NAME', 'ql_svd'); 
define('DB_USER', 'root');         
define('DB_PASS', ''); 

define('BASE_URL', 'http://localhost/QuanLySVD/'); // URL gốc của dự án

//Gui email khi quen mat khau
define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_PORT', 587);
define('MAIL_USERNAME', 'vdpvinh24@gmail.com'); 
define('MAIL_PASSWORD', 'gjug yymy mruw xuxt'); 
define('MAIL_ENCRYPTION', 'tls');
define('MAIL_FROM_ADDRESS', 'vdpvinh24@gmail.com');
define('MAIL_FROM_NAME', 'Hệ thống đặt sân thể thao'); 

// --- Cấu hình MoMo Sandbox ---
define('MOMO_PARTNER_CODE', 'MOMOBKUN20180529');
define('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j');
define('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa');
define('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create');
define('MOMO_RETURN_URL', BASE_URL . 'checkout/momoReturn'); // Trang web nhận kết quả
define('MOMO_NOTIFY_URL', BASE_URL . 'checkout/momoNotify'); // MoMo gọi ngầm để cập nhật DB
// ------------------------------

// Thêm cấu hình hiển thị lỗi cho môi trường phát triển
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>