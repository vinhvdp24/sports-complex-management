<?php
namespace App\Controllers;
use App\Models\UserModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../vendor/autoload.php'; 


class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new UserModel();
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ===============================================
    //                  ĐĂNG KÝ
    // ===============================================

    /**
     * Hiển thị Form Đăng ký
     */
    public function hienThiDangKy() { // <--- ĐÃ ĐỔI TÊN
        $error = $_SESSION['register_error'] ?? null;
        unset($_SESSION['register_error']);
        
        require_once __DIR__ . '/../../views/auth/Register.php';
    }

    /**
     * Xử lý dữ liệu từ Form Đăng ký
     */
    public function xuLyDangKy() { // <--- ĐÃ ĐỔI TÊN
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }

        // Lấy và lọc dữ liệu (tên biến giữ nguyên)
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $passwordConfirm = trim($_POST['password_confirm'] ?? '');
        $hoTen = trim($_POST['ho_ten'] ?? '');
        $sdt = trim($_POST['sdt'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $diaChi = trim($_POST['dia_chi'] ?? '');

        // Kiểm tra tính hợp lệ dữ liệu
        if (empty($username) || empty($password) || empty($hoTen) || empty($email) || empty($sdt) || empty($diaChi)) {
            $_SESSION['register_error'] = "Vui lòng điền đầy đủ các trường bắt buộc (bao gồm email, số điện thoại, địa chỉ).";
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }

        if ($password !== $passwordConfirm) {
            $_SESSION['register_error'] = "Xác nhận mật khẩu không khớp.";
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }
        if ($this->userModel->getUserByUsername($username)) {
            $_SESSION['register_error'] = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }
        
        if ($this->userModel->getUserByEmail($email)) {
            $_SESSION['register_error'] = "Email đã tồn tại trong hệ thống. Vui lòng chọn email khác hoặc đăng nhập.";
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }

        // Tạo mã OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Lưu thông tin vào Session tạm
        $_SESSION['register_temp_data'] = [
            'username' => $username,
            'password' => $password,
            'hoTen' => $hoTen,
            'sdt' => $sdt,
            'email' => $email,
            'diaChi' => $diaChi,
            'otp' => $otp,
            'otp_expires' => time() + (5 * 60) // 5 phút
        ];

        // Gửi email OTP
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->SMTPDebug  = 0;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->Subject = "Mã xác nhận Đăng ký tài khoản";
            $mail->Body = "Chào bạn,\n\nMã xác nhận OTP để đăng ký tài khoản của bạn là: $otp\nMã này có hiệu lực trong 5 phút.\n\nTrân trọng,\nĐội ngũ hỗ trợ.";

            $mail->send();
            header("Location: " . BASE_URL . "auth/hienThiXacNhanOTP");
            exit;
        } catch (Exception $e) {
            unset($_SESSION['register_temp_data']);
            $_SESSION['register_error'] = "Không thể gửi email OTP. Vui lòng kiểm tra lại email. Lỗi: " . $mail->ErrorInfo;
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }
    }

    // ===============================================
    //                  XÁC NHẬN OTP
    // ===============================================

    public function hienThiXacNhanOTP() {
        if (!isset($_SESSION['register_temp_data'])) {
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }
        
        $error = $_SESSION['otp_error'] ?? null;
        unset($_SESSION['otp_error']);
        
        $success = $_SESSION['otp_success'] ?? null;
        unset($_SESSION['otp_success']);

        $email = $_SESSION['register_temp_data']['email'];
        
        require_once __DIR__ . '/../../views/auth/VerifyOTP.php';
    }

    public function xuLyXacNhanOTP() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['register_temp_data'])) {
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }

        $otpInput = trim($_POST['otp'] ?? '');
        $tempData = $_SESSION['register_temp_data'];

        if (empty($otpInput)) {
            $_SESSION['otp_error'] = "Vui lòng nhập mã OTP.";
            header("Location: " . BASE_URL . "auth/hienThiXacNhanOTP");
            exit;
        }

        if (time() > $tempData['otp_expires']) {
            $_SESSION['otp_error'] = "Mã OTP đã hết hạn. Vui lòng yêu cầu gửi lại mã.";
            header("Location: " . BASE_URL . "auth/hienThiXacNhanOTP");
            exit;
        }

        if ($otpInput !== (string)$tempData['otp']) {
            $_SESSION['otp_error'] = "Mã OTP không chính xác.";
            header("Location: " . BASE_URL . "auth/hienThiXacNhanOTP");
            exit;
        }

        // OTP hợp lệ, tiến hành đăng ký
        $result = $this->userModel->registerUser(
            $tempData['username'], 
            $tempData['password'], 
            $tempData['hoTen'], 
            $tempData['sdt'], 
            $tempData['email'],
            $tempData['diaChi']
        );

        if ($result) {
            unset($_SESSION['register_temp_data']); // Xóa session tạm
            $_SESSION['success_message'] = "Đăng ký thành công! Vui lòng đăng nhập.";
            header("Location: " . BASE_URL . "auth/hienThiDangNhap");
        } else {
            $_SESSION['otp_error'] = "Có lỗi xảy ra khi lưu vào cơ sở dữ liệu. Vui lòng thử lại sau.";
            header("Location: " . BASE_URL . "auth/hienThiXacNhanOTP");
        }
        exit;
    }

    public function guiLaiOTP() {
        if (!isset($_SESSION['register_temp_data'])) {
            header("Location: " . BASE_URL . "auth/hienThiDangKy");
            exit;
        }

        $email = $_SESSION['register_temp_data']['email'];
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        // Cập nhật lại session
        $_SESSION['register_temp_data']['otp'] = $otp;
        $_SESSION['register_temp_data']['otp_expires'] = time() + (5 * 60);

        // Gửi lại email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->SMTPDebug  = 0;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->Subject = "Mã xác nhận Đăng ký tài khoản (Gửi lại)";
            $mail->Body = "Chào bạn,\n\nMã xác nhận OTP mới để đăng ký tài khoản của bạn là: $otp\nMã này có hiệu lực trong 5 phút.\n\nTrân trọng,\nĐội ngũ hỗ trợ.";

            $mail->send();
            $_SESSION['otp_success'] = "Đã gửi lại mã OTP. Vui lòng kiểm tra email.";
        } catch (Exception $e) {
            $_SESSION['otp_error'] = "Không thể gửi lại email OTP. Lỗi: " . $mail->ErrorInfo;
        }
        
        header("Location: " . BASE_URL . "auth/hienThiXacNhanOTP");
        exit;
    }
    // ===============================================
    //                  ĐĂNG NHẬP
    // ===============================================

    /**
     * Hiển thị Form Đăng nhập
     */
    public function hienThiDangNhap() { // <--- ĐÃ ĐỔI TÊN
        if ($this->isLoggedIn()) {
             header("Location: " . BASE_URL . "home/index");
             exit;
        }
        $error = $_SESSION['login_error'] ?? null;
        unset($_SESSION['login_error']);
        
        $success = $_SESSION['success_message'] ?? null;
        unset($_SESSION['success_message']); 

        require_once __DIR__ . '/../../views/auth/Login.php';
    }
    
    /**
     * Xử lý dữ liệu từ Form Đăng nhập
     */
    public function xuLyDangNhap() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "auth/hienThiDangNhap");
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $_SESSION['login_error'] = "Vui lòng nhập Tên đăng nhập/Email và Mật khẩu.";
            header("Location: " . BASE_URL . "auth/hienThiDangNhap");
            exit;
        }

        $user = $this->userModel->getUserByUsernameOrEmail($username);
        $passwordIsValid = false;

        if ($user) {
            $storedPassword = $user['MatKhau'];

            // Kiểm tra xem mật khẩu đã được băm hay chưa. Mật khẩu băm bởi PASSWORD_BCRYPT dài 60 ký tự.
            if (strlen($storedPassword) === 60) {
                // Nếu đã băm, dùng password_verify
                if (password_verify($password, $storedPassword)) {
                    $passwordIsValid = true;
                }
            } else {
                // Nếu là mật khẩu cũ (dạng thô), so sánh trực tiếp
                if ($password === $storedPassword) {
                    $passwordIsValid = true;
                    // **QUAN TRỌNG**: Nâng cấp mật khẩu thô lên dạng băm để các lần đăng nhập sau được an toàn
                    $this->userModel->updatePassword($user['MaTK'], $password);
                }
            }
        }

        if ($passwordIsValid) {
            // Đăng nhập thành công: Tạo Session
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id'] = $user['MaKH'];
            $_SESSION['username'] = $user['TenDangNhap'];
            $_SESSION['user_role'] = $user['LoaiTK'];
            $_SESSION['ho_ten'] = $user['HoTen'];

            header("Location: " . BASE_URL . "home/index");
            exit;
        } else {
            $_SESSION['login_error'] = "Tên đăng nhập, Email hoặc mật khẩu không chính xác.";
            header("Location: " . BASE_URL . "auth/hienThiDangNhap");
            exit;
        }
    }

   
    public function dangXuat() { // <--- ĐÃ ĐỔI TÊN
        session_unset();
        session_destroy();
        header("Location: " . BASE_URL . "auth/hienThiDangNhap");
        exit;
    }
    
   
    private function isLoggedIn() {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }

    //CHUC NANG QUEN MAT KHAU 
    public function hienThiQuenMatKhau() {
        // Biến session sẽ được đọc và unset trực tiếp trong view Forgot_Password.php
        
        // Cần đảm bảo file Forget_Password.php nằm ở views/auth/
        // Sử dụng require_once để tải View, giống như hienThiDangNhap
         require_once __DIR__ . '/../../views/auth/Forgot_Password.php';
    }

    // =======================
    // 2️⃣ XỬ LÝ GỬI EMAIL RESET
    // =======================
public function guiEmailReset() {
    $email = trim($_POST['email'] ?? '');

    if (!$email) {
        $_SESSION['error'] = "Vui lòng nhập email.";
        header("Location: " . BASE_URL . "auth/hienThiQuenMatKhau");
        exit;
    }

    $user = $this->userModel->getUserByEmail($email);
    if (!$user) {
        $_SESSION['error'] = "Email không tồn tại trong hệ thống.";
        header("Location: " . BASE_URL . "auth/hienThiQuenMatKhau");
        exit;
    }

    // Tạo token ngẫu nhiên và lưu vào DB
    $token = bin2hex(random_bytes(32)); // 64 ký tự
    $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // hết hạn 1h
    $this->userModel->savePasswordResetToken($user['MaKH'], $token, $expiry);

    $resetLink = BASE_URL . "reset-password?token=" . $token;
    $subject = "Đặt lại mật khẩu";
    $message = "Click vào link để đặt lại mật khẩu: " . $resetLink;

    
    $mail = new PHPMailer(true);
    try {
        // Cấu hình SMTP từ config.php
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        // Tắt chế độ debug (0 = off). Đặt là 2 để xem chi tiết lỗi nếu cần.
        $mail->SMTPDebug  = 0; 

        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($email);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        $_SESSION['success_message'] = "Đã gửi email đặt lại mật khẩu. Vui lòng kiểm tra hòm thư.";
    } catch (Exception $e) {
        $_SESSION['error'] = "Email không gửi được. Lỗi: " . $mail->ErrorInfo;
 }

    header("Location: " . BASE_URL . "auth/hienThiDangNhap");
    exit;
}


    public function hienThiDatLaiMatKhau() {
        $token = $_GET['token'] ?? null;
        if (!$token || !$this->userModel->validateResetToken($token)) {
            echo "Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
            exit;
        }

        require_once __DIR__ . '/../../views/auth/Reset_Password.php';
    }

    // =======================
    // 4️⃣ XỬ LÝ ĐẶT LẠI MẬT KHẨU
    // =======================
    public function datLaiMatKhau() {
        $token = $_POST['token'] ?? null;
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        if (!$token || !$this->userModel->validateResetToken($token)) {
            echo "Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
            exit;
        }

        if (!$password || $password !== $passwordConfirm) {
            $_SESSION['error'] = "Mật khẩu không hợp lệ hoặc không trùng khớp.";
            header("Location: " . BASE_URL . "reset-password?token=" . $token);
            exit;
        }

        // Lấy user theo token
        $user = $this->userModel->getUserByResetToken($token);
        if (!$user) {
            echo "Lỗi hệ thống, vui lòng thử lại.";
            exit;
        }

        // Cập nhật mật khẩu mới
        $this->userModel->updatePassword($user['MaTK'], $password);

        // Xóa token
        $this->userModel->deleteResetToken($token);

        $_SESSION['success_message'] = "Đặt lại mật khẩu thành công. Vui lòng đăng nhập.";
        header("Location: " . BASE_URL . "auth/hienThiDangNhap");
        exit;
    }
}
?>