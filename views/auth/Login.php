<?php 
// Đảm bảo BASE_URL được định nghĩa
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}


$pageTitle = 'Đăng Nhập Hệ Thống'; 

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
        box-shadow: 0 8px 15px rgba(118, 75, 162, 0.3);
        color: #fff;
    }
    .auth-footer {
        background-color: #f8fafc;
        border-top: 1px solid #edf2f7;
        padding: 20px;
        text-align: center;
    }
    .auth-footer a {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.2s;
    }
    .auth-footer a:hover {
        color: #764ba2;
        text-decoration: underline;
    }
    .toggle-password {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #a0aec0;
        z-index: 10;
        background: none;
        border: none;
        padding: 0;
    }
    .toggle-password:focus {
        outline: none;
    }
</style>

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <i class="fas fa-user-circle fs-1 mb-3"></i>
                        <h3>ĐĂNG NHẬP</h3>
                        <p class="mb-0 mt-2 opacity-75">Chào mừng bạn quay trở lại</p>
                    </div>
                    
                    <div class="auth-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm" role="alert">
                                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <form action="<?php echo BASE_URL; ?>auth/xuLyDangNhap" method="POST">
                            
                            <div class="mb-4">
                                <label for="username" class="form-label fw-bold text-secondary small text-uppercase">Tên đăng nhập hoặc Email</label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-user input-icon"></i>
                                    <input type="text" class="form-control form-control-modern" id="username" name="username" placeholder="Nhập tên đăng nhập hoặc email của bạn" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label for="password" class="form-label fw-bold text-secondary small text-uppercase mb-0">Mật khẩu</label>
                                    <a href="<?= BASE_URL ?>forgot-password" class="text-decoration-none small" style="color: #667eea;">Quên mật khẩu?</a>
                                </div>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-lock input-icon"></i>
                                    <input type="password" class="form-control form-control-modern" id="password" name="password" placeholder="Nhập mật khẩu" required style="padding-right: 45px;">
                                    <button class="toggle-password" type="button" id="togglePassword">
                                        <!-- Eye icon will be inserted here by script -->
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid mt-5">
                                <button type="submit" class="btn btn-auth"><i class="fas fa-sign-in-alt me-2"></i>Đăng Nhập Ngay</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="auth-footer">
                        <p class="mb-0 text-muted">Chưa có tài khoản? 
                            <a href="<?php echo BASE_URL; ?>auth/hienThiDangKy">Đăng ký ngay</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');

    const eyeIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>`;
    const eyeSlashIcon = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16"><path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/><path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/></svg>`;

    // Set initial icon
    togglePassword.innerHTML = eyeIcon;

    togglePassword.addEventListener('click', function() {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Toggle the icon
        this.innerHTML = type === 'password' ? eyeIcon : eyeSlashIcon;
    });
});
</script>
