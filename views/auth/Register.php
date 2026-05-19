<?php 
// Đảm bảo BASE_URL được định nghĩa
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}

// $error được truyền từ AuthController (qua Session)
$pageTitle = 'Đăng Ký Tài Khoản Mới'; 

// Nhúng Header (Bao gồm Navbar responsive)
require_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    .auth-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        background: #f8f9fa;
        padding: 40px 0;
    }
    .auth-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        overflow: hidden;
        background: #fff;
    }
    .auth-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        padding: 40px 20px;
        text-align: center;
        color: #fff;
    }
    .auth-header h3 {
        font-weight: 800;
        margin-bottom: 0;
        letter-spacing: 1px;
    }
    .auth-body {
        padding: 40px;
    }
    .form-control-modern {
        border-radius: 10px;
        padding: 12px 20px 12px 45px;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.3s;
        font-size: 0.95rem;
    }
    .form-control-modern:focus {
        border-color: #11998e;
        box-shadow: 0 0 0 0.2rem rgba(17, 153, 142, 0.25);
        background-color: #fff;
    }
    .input-icon-wrap {
        position: relative;
    }
    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
        font-size: 1.1rem;
        z-index: 10;
    }
    .btn-auth {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        border-radius: 10px;
        padding: 12px;
        font-weight: 700;
        font-size: 1.05rem;
        letter-spacing: 0.5px;
        color: #fff;
        transition: all 0.3s ease;
    }
    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(56, 239, 125, 0.3);
        color: #fff;
    }
    .auth-footer {
        background-color: #f8fafc;
        border-top: 1px solid #edf2f7;
        padding: 20px;
        text-align: center;
    }
    .auth-footer a {
        color: #11998e;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    .auth-footer a:hover {
        color: #0f857b;
        text-decoration: underline;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 1.5rem;
        position: relative;
        padding-left: 15px;
    }
    .section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #11998e;
        border-radius: 2px;
    }
</style>

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9 col-lg-7">
                <div class="auth-card">
                    <div class="auth-header">
                        <i class="fas fa-user-plus fs-1 mb-3"></i>
                        <h3>ĐĂNG KÝ TÀI KHOẢN</h3>
                        <p class="mb-0 mt-2 opacity-75">Tham gia cùng hàng ngàn thành viên khác</p>
                    </div>
                    
                    <div class="auth-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo BASE_URL; ?>auth/xuLyDangKy" method="POST">
                            
                            <h5 class="section-title">Thông tin đăng nhập</h5>

                            <div class="mb-4">
                                <label for="username" class="form-label fw-bold text-secondary small text-uppercase">Tên đăng nhập <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control form-control-modern" id="username" name="username" placeholder="Nhập tên đăng nhập viết liền không dấu" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="password" class="form-label fw-bold text-secondary small text-uppercase">Mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-lock input-icon"></i>
                                        <input type="password" class="form-control form-control-modern" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="password_confirm" class="form-label fw-bold text-secondary small text-uppercase">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-shield-alt input-icon"></i>
                                        <input type="password" class="form-control form-control-modern" id="password_confirm" name="password_confirm" placeholder="Nhập lại mật khẩu" required>
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4" style="border-top-color: #e2e8f0;">
                            
                            <h5 class="section-title">Thông tin liên hệ</h5>

                            <div class="mb-4">
                                <label for="ho_ten" class="form-label fw-bold text-secondary small text-uppercase">Họ và Tên <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-id-card input-icon"></i>
                                    <input type="text" class="form-control form-control-modern" id="ho_ten" name="ho_ten" placeholder="Ví dụ: Nguyễn Văn A" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label for="email" class="form-label fw-bold text-secondary small text-uppercase">Email <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" class="form-control form-control-modern" id="email" name="email" placeholder="Ví dụ: email@gmail.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="sdt" class="form-label fw-bold text-secondary small text-uppercase">Số điện thoại <span class="text-danger">*</span></label>
                                    <div class="input-icon-wrap">
                                        <i class="fas fa-phone-alt input-icon"></i>
                                        <input type="text" class="form-control form-control-modern" id="sdt" name="sdt" placeholder="Ví dụ: 0912345678" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="dia_chi" class="form-label fw-bold text-secondary small text-uppercase">Địa chỉ <span class="text-danger">*</span></label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-map-marker-alt input-icon"></i>
                                    <input type="text" class="form-control form-control-modern" id="dia_chi" name="dia_chi" placeholder="Nhập địa chỉ của bạn" required>
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-auth"><i class="fas fa-rocket me-2"></i>Tạo Tài Khoản</button>
                            </div>
                            <div class="text-center mt-3">
                                <small class="text-muted"><i class="fas fa-info-circle me-1"></i>Mã xác nhận OTP sẽ được gửi đến email của bạn sau khi nhấn Tạo Tài Khoản.</small>
                            </div>
                            
                        </form>
                    </div>
                    
                    <div class="auth-footer">
                        <p class="mb-0 text-muted">Đã có tài khoản? 
                            <a href="<?php echo BASE_URL; ?>auth/hienThiDangNhap">Đăng nhập ngay</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
require_once __DIR__ . '/../layouts/footer.php'; 
?>