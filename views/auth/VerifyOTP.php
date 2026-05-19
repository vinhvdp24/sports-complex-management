<?php 
// Đảm bảo BASE_URL được định nghĩa
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}

$pageTitle = 'Xác Nhận OTP'; 

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
        letter-spacing: 5px;
        text-align: center;
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
</style>

<div class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5">
                <div class="auth-card">
                    <div class="auth-header">
                        <i class="fas fa-envelope-open-text fs-1 mb-3"></i>
                        <h3>XÁC NHẬN EMAIL</h3>
                        <p class="mb-0 mt-2 opacity-75">Nhập mã OTP đã được gửi đến email của bạn</p>
                    </div>
                    
                    <div class="auth-body">
                        <div class="text-center mb-4">
                            <p class="text-muted mb-1">Mã xác nhận 6 số đã được gửi tới:</p>
                            <h6 class="fw-bold"><?php echo htmlspecialchars($email ?? ''); ?></h6>
                        </div>

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

                        <form action="<?php echo BASE_URL; ?>auth/xuLyXacNhanOTP" method="POST">
                            <div class="mb-4">
                                <label for="otp" class="form-label fw-bold text-secondary small text-uppercase">Mã OTP</label>
                                <div class="input-icon-wrap">
                                    <i class="fas fa-key input-icon"></i>
                                    <input type="text" class="form-control form-control-modern" id="otp" name="otp" placeholder="XXXXXX" maxlength="6" required>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-auth"><i class="fas fa-check-circle me-2"></i>Xác nhận & Đăng ký</button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="auth-footer">
                        <p class="mb-0 text-muted">Không nhận được email? 
                            <a href="<?php echo BASE_URL; ?>auth/guiLaiOTP">Gửi lại mã</a>
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
