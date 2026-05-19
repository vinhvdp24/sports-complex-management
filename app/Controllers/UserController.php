<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DatSanModel;
use App\Models\DichVuModel;
use App\Models\OrderModel;
use App\Models\DanhGiaModel;

class UserController {
    private $userModel;
    private $datSanModel;
    private $orderModel;
    private $dichVuModel;
    private $danhGiaModel;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
        $this->datSanModel = new DatSanModel();
        $this->orderModel = new OrderModel();
        $this->dichVuModel = new DichVuModel();
        $this->danhGiaModel = new DanhGiaModel();
    }

    public function dashboard() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }
        $pageTitle = 'Tổng quan';
        require_once __DIR__ . '/../../views/user/dashboard.php';
    }

    public function showProfile() {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            // Handle case where user_id is not in session, maybe redirect to login
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        $user = $this->userModel->getUserByMaKH($userId); // Fetch full user data

        if (!$user) {
            // Handle case where user is not found in DB
            $_SESSION['error_message'] = "Không tìm thấy thông tin người dùng.";
            header('Location: ' . BASE_URL . 'home/index'); // Or show an error page
            exit;
        }

        // Data for the view
        $pageTitle = 'Thông tin cá nhân';
        // Pass the full user data to the view
        $data = [
            'pageTitle' => $pageTitle,
            'user' => $user // All user data from database
        ];
        
        // Extract data for view, assuming view uses direct variables
        extract($data); // Giúp các biến $pageTitle và $user có sẵn trong view được nhúng
        // Tải giao diện
        require_once __DIR__ . '/../../views/user/indexUser.php';
    }

    public function bookingHistory() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        $maKH = $_SESSION['user_id'];
        $lichSuDatSan = $this->datSanModel->getLichSuDatSanByMaKH($maKH);

        $pageTitle = 'Lịch sử đặt sân';
        $data = [
            'pageTitle' => $pageTitle,
            'lichSuDatSan' => $lichSuDatSan
        ];
        extract($data);
        require_once __DIR__ . '/../../views/user/booking_history.php';
    }

    public function bookingDetails() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }
    
        $maDatSan = $_GET['id'] ?? null;
        if (!$maDatSan) {
            $_SESSION['error_message'] = "Yêu cầu không hợp lệ. Thiếu mã đặt sân.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }
    
        $maKH = $_SESSION['user_id'];
        
        $booking = $this->datSanModel->getBookingDetailsById($maDatSan, $maKH);
    
        if (!$booking) {
            $_SESSION['error_message'] = "Không tìm thấy lịch đặt sân hoặc bạn không có quyền truy cập.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }
    
        $services = $this->dichVuModel->getServicesByBookingId($maDatSan);
    
        $pageTitle = 'Chi Tiết Đặt Sân #' . $booking['MaDatSan'];
        $data = [
            'pageTitle' => $pageTitle,
            'booking' => $booking,
            'services' => $services
        ];
        
        extract($data);
        require_once __DIR__ . '/../../views/user/booking_details.php';
    }

    public function invoiceHistory() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }
        
        $maKH = $_SESSION['user_id'];
        $lichSuHoaDon = $this->orderModel->getOrdersByCustomerId($maKH);

        $pageTitle = 'Lịch sử hóa đơn';
        $data = [
            'pageTitle' => $pageTitle,
            'lichSuHoaDon' => $lichSuHoaDon
        ];
        extract($data);
        require_once __DIR__ . '/../../views/user/invoice_history.php';
    }

    public function orderDetails($orderId) {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Fetch the main order details, ensuring it belongs to the logged-in user
        $order = $this->orderModel->getOrderById($orderId, $userId);

        if (!$order) {
            $_SESSION['error_message'] = "Không tìm thấy đơn hàng hoặc bạn không có quyền truy cập.";
            header('Location: ' . BASE_URL . 'user/invoice-history');
            exit;
        }

        // Fetch the items associated with the order
        $orderItems = $this->orderModel->getOrderItemsByOrderId($orderId);

        // Prepare data for the view
        $pageTitle = 'Chi Tiết Đơn Hàng #' . $order['id'];
        $data = [
            'pageTitle' => $pageTitle,
            'order' => $order,
            'orderItems' => $orderItems
        ];
        
        // Nạp giao diện và truyền dữ liệu
        extract($data);
        require_once __DIR__ . '/../../views/user/order_details.php';
    }

    public function cancelBooking() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maDatSan = $_POST['MaDatSan'] ?? null;
            $maKH = $_SESSION['user_id'];

            if ($maDatSan) {
                $booking = $this->datSanModel->getBookingDetailsById($maDatSan, $maKH);

                if ($booking) {
                    // Set the timezone to ensure correct comparison
                    date_default_timezone_set('Asia/Ho_Chi_Minh');

                    // Combine date and time from the booking to create a full DateTime object
                    $thoiGianDatSan = new \DateTime($booking['NgayDat'] . ' ' . $booking['GioBatDau']);
                    $thoiGianHienTai = new \DateTime();

                    // Allow cancellation only if the booking time is in the future
                    if ($thoiGianDatSan > $thoiGianHienTai) {
                        if ($this->datSanModel->huyLichDat($maDatSan, $maKH)) {
                            $_SESSION['success_message'] = "Hủy lịch đặt sân thành công.";
                        } else {
                            $_SESSION['error_message'] = "Lỗi: Không thể hủy lịch đặt sân. Vui lòng thử lại.";
                        }
                    } else {
                        $_SESSION['error_message'] = "Đã quá thời gian hủy cho phép. Chỉ có thể hủy các lịch đặt sân trong tương lai.";
                    }
                } else {
                    $_SESSION['error_message'] = "Không tìm thấy lịch đặt sân hoặc bạn không có quyền hủy.";
                }
            } else {
                $_SESSION['error_message'] = "Yêu cầu không hợp lệ.";
            }
        }
        
        header('Location: ' . BASE_URL . 'user/bookingHistory');
        exit;
    }

    public function review() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }
    
        $maDatSan = $_GET['booking_id'] ?? null;
        $maKH = $_SESSION['user_id'];
    
        if (!$maDatSan) {
            $_SESSION['error_message'] = "Yêu cầu không hợp lệ.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }
    
        $booking = $this->datSanModel->getBookingDetailsById($maDatSan, $maKH);
    
        // Kiểm tra xem đơn đặt có tồn tại và thuộc về người dùng không
        if (!$booking) {
            $_SESSION['error_message'] = "Không tìm thấy lịch đặt sân hoặc bạn không có quyền truy cập.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }

        if (stripos($booking['TrangThai'] ?? '', 'hủy') !== false) {
            $_SESSION['error_message'] = "Bạn không thể đánh giá lịch đặt sân đã bị hủy.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }
    
        // Kiểm tra xem đơn đặt đã thực sự hoàn thành chưa
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $thoiGianKetThuc = new \DateTime($booking['NgayDat'] . ' ' . $booking['GioKetThuc']);
        $thoiGianHienTai = new \DateTime();
    
        if ($thoiGianKetThuc >= $thoiGianHienTai) {
            $_SESSION['error_message'] = "Bạn chỉ có thể đánh giá sau khi lịch đặt đã kết thúc.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }
        
        // Kiểm tra xem đã được đánh giá chưa
        if ($this->danhGiaModel->hasReviewForBooking($maDatSan)) {
            $_SESSION['error_message'] = "Bạn đã đánh giá lịch đặt này rồi.";
            header('Location: ' . BASE_URL . 'user/bookingHistory');
            exit;
        }
    
        $pageTitle = 'Đánh giá lịch đặt sân';
        $data = ['pageTitle' => $pageTitle, 'booking' => $booking];
        extract($data);
    
        require_once __DIR__ . '/../../views/user/add_review.php';
    }

    public function submitReview() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maDatSan = $_POST['MaDatSan'] ?? null;
            $maSan = $_POST['MaSan'] ?? null;
            $diem = $_POST['diem'] ?? null;
            $noiDung = $_POST['noi_dung'] ?? '';
            $maKH = $_SESSION['user_id'];
    
            // Basic validation
            if (!$maDatSan || !$maSan || !$diem || !$maKH) {
                $_SESSION['error_message'] = "Dữ liệu không hợp lệ. Vui lòng thử lại.";
                header('Location: ' . BASE_URL . 'user/bookingHistory');
                exit;
            }
            
            // Server-side validation again before submitting
            $booking = $this->datSanModel->getBookingDetailsById($maDatSan, $maKH);
            if (!$booking) {
                 $_SESSION['error_message'] = "Lỗi: Lịch đặt không tồn tại.";
                 header('Location: ' . BASE_URL . 'user/bookingHistory');
                 exit;
            }
            if ($this->danhGiaModel->hasReviewForBooking($maDatSan)) {
                $_SESSION['error_message'] = "Bạn đã đánh giá lịch đặt này rồi.";
                header('Location: ' . BASE_URL . 'user/bookingHistory');
                exit;
            }
    
            if ($this->danhGiaModel->addReview($maKH, $maSan, $maDatSan, $diem, $noiDung)) {
                $_SESSION['success_message'] = "Cảm ơn bạn đã gửi đánh giá!";
            } else {
                // If the model didn't set a specific error message, set a generic one.
                if (!isset($_SESSION['error_message'])) {
                    $_SESSION['error_message'] = "Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại.";
                }
            }
        }
    
        header('Location: ' . BASE_URL . 'user/bookingHistory');
        exit;
    }

    public function updatePassword() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maKH = $_SESSION['user_id'];
            $oldPassword = $_POST['old_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            if ($newPassword !== $confirmPassword) {
                $_SESSION['error_message'] = "Mật khẩu mới không khớp.";
                header('Location: ' . BASE_URL . 'user/showProfile');
                exit;
            }

            if ($this->userModel->checkPassword($maKH, $oldPassword)) {
                $user = $this->userModel->getUserByMaKH($maKH);
                $maTK = $user['MaTK'];
                if ($this->userModel->updatePassword($maTK, $newPassword)) {
                    $_SESSION['success_message'] = "Cập nhật mật khẩu thành công.";
                } else {
                    $_SESSION['error_message'] = "Lỗi khi cập nhật mật khẩu.";
                }
            } else {
                $_SESSION['error_message'] = "Mật khẩu cũ không đúng.";
            }
        }

        header('Location: ' . BASE_URL . 'user/showProfile');
        exit;
    }

    public function updateProfile() {
        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $maKH = $_SESSION['user_id'];
            $data = [
                'HoTen' => $_POST['ho_ten'],
                'SDT' => $_POST['sdt'],
                'Email' => $_POST['email'],
                'DiaChi' => $_POST['dia_chi']
            ];

            if ($this->userModel->updateProfile($maKH, $data)) {
                $_SESSION['success_message'] = "Cập nhật thông tin cá nhân thành công.";
            } else {
                $_SESSION['error_message'] = "Lỗi khi cập nhật thông tin cá nhân.";
            }
        }

        header('Location: ' . BASE_URL . 'user/showProfile');
        exit;
    }
}
?>
