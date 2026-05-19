<?php
// Bắt đầu session để lưu trữ thông tin đăng nhập, giỏ hàng, v.v.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Tải file cấu hình chính (chứa thông tin DB, BASE_URL, v.v.)
require_once __DIR__ . '/../config.php';

// Tải file autoloader của Composer để sử dụng các thư viện bên ngoài (ví dụ: PHPMailer)
require_once __DIR__ . '/../vendor/autoload.php';

// Tải file autoloader của ứng dụng để tự động nạp các class (Controllers, Models)
require_once __DIR__ . '/../app/Core/Autoloader.php';

use App\Controllers\AdminController;
use App\Controllers\DatsanController;
use App\Core\Router;


$router = new Router();

$router->get('auth/hienThiDangNhap', 'AuthController@hienThiDangNhap');
$router->post('auth/xuLyDangNhap', 'AuthController@xuLyDangNhap');
$router->post('/auth/dangXuat', 'AuthController@dangXuat');

$router->get('dang-xuat', [AuthController::class, 'dangXuat']);
$router->get('dat-san', [DatsanController::class, 'chonSan']);
$router->post('/kiem-tra-san', [DatsanController::class, 'kiemTraVaTinhGia']);
$router->post('luu-tam-dat-san', [DatsanController::class, 'luuTamDatSan']);
$router->post('luu-tam-dich-vu', [DatsanController::class, 'luuTamDichVu']);
$router->get('xac-nhan-thanh-toan', [DatsanController::class, 'xacNhanThanhToan']);
$router->post('hoan-tat-dat-san', [DatsanController::class, 'hoanTatDatSan']);
$router->get('thanh-cong', [DatsanController::class, 'thanhCong']);
$router->get('loi-thanh-toan', [DatsanController::class, 'loiThanhToan']);
$router->get('chon-dich-vu', [DatsanController::class, 'chonDichVu']);

$router->get('admin-dashboard', [AdminController::class, 'hienThiDashboard']);
$router->get('admin-san', [AdminController::class, 'quanLySan']);

$router->get('form-them-san', [AdminController::class, 'hienThiThemSan']);

$router->post('admin-san-them', [AdminController::class, 'xuLyThemSan']);
$router->post('admin-san-xoa', [AdminController::class, 'xuLyXoaSan']);

$router->get('admin-san-sua', [AdminController::class, 'hienThiSuaSan']);
$router->post('admin-san-capnhat', [AdminController::class, 'xuLySuaSan']);


$router->get('forgot-password', [AuthController::class, 'hienThiQuenMatKhau']); // biểu mẫu
$router->post('forgot-password', [AuthController::class, 'guiEmailReset']);       // xử lý
     // Xử lý gửi mail

$router->get('reset-password', [AuthController::class, 'hienThiDatLaiMatKhau']); // Biểu mẫu đặt lại mật khẩu (kèm token)
$router->post('reset-password', [AuthController::class, 'datLaiMatKhau']);       // Xử lý đặt lại mật khẩu

$router->get('/admin/datsan-theo-ngay', 'AdminController@hienThiDatSanTheoNgay');
$router->post('/admin/datsan-theo-ngay', 'AdminController@xuLyDatSanTheoNgay');
$router->get('/admin/khachhang', 'AdminController@quanLyKhachHang');
$router->post('/admin/khachhang', 'AdminController@quanLyKhachHang');
$router->get('/admin/nhaphang', 'AdminController@nhapHang');
$router->post('/admin/nhaphang', 'AdminController@xuLyNhapHang');

$router->get('admin/kho-hang', 'AdminController@khoHang');
$router->get('admin/kho-hang/them', 'AdminController@themSanPham');
$router->post('admin/kho-hang/them', 'AdminController@xuLyThemSanPham');
$router->get('admin/kho-hang/sua', 'AdminController@suaSanPham');
$router->post('admin/kho-hang/sua', 'AdminController@xuLySuaSanPham');
$router->post('admin/kho-hang/xoa', 'AdminController@xuLyXoaSanPham');

$router->get('admin/don-hang', 'AdminController@quanLyDonHang');
$router->post('admin/don-hang/cap-nhat', 'AdminController@xuLyCapNhatTrangThaiDonHang');

$router->get('store', 'StoreController@index');

$router->post('cart/add', 'CartController@add');
$router->get('cart', 'CartController@index');
$router->get('cart/count', 'CartController@getCartCount');
$router->post('cart/update', 'CartController@update');
$router->post('cart/remove', 'CartController@remove');

$router->get('checkout', 'CheckoutController@index');
$router->post('checkout/place-order', 'CheckoutController@placeOrder');
$router->get('checkout/success', 'CheckoutController@success');

$router->get('user/profile', 'UserController@showProfile');
$router->get('user/booking-history', 'UserController@bookingHistory');
$router->get('user/invoice-history', 'UserController@invoiceHistory');
$router->get('admin/revenue', 'AdminController@hienThiDoanhThu');

$router->dieuHuong(); 

?>