<?php 
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}
require_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    .profile-wrapper {
        background: #f8f9fa;
        min-height: 80vh;
        padding-top: 40px;
        padding-bottom: 60px;
    }
    .page-title {
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }
    .page-title::after {
        content: '';
        position: absolute;
        width: 40%;
        height: 4px;
        background: linear-gradient(45deg, #e74c3c, #c0392b);
        bottom: -10px;
        left: 0;
        border-radius: 2px;
    }
    .profile-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .profile-header {
        background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);
        padding: 40px 20px;
        text-align: center;
        color: #fff;
    }
    .avatar-circle {
        width: 100px;
        height: 100px;
        background: #fff;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: #ff9a9e;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin-bottom: 15px;
    }
    .info-item {
        padding: 15px 0;
        border-bottom: 1px solid #f1f2f6;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #636e72;
        font-size: 0.9rem;
    }
    .info-value {
        font-weight: 700;
        color: #2d3436;
    }
    .form-control-modern {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #dfe4ea;
        background-color: #f8f9fa;
        transition: all 0.3s;
    }
    .form-control-modern:focus {
        border-color: #ff9a9e;
        box-shadow: 0 0 0 0.2rem rgba(255, 154, 158, 0.25);
        background-color: #fff;
    }
    .btn-modern {
        border-radius: 10px;
        padding: 12px 25px;
        font-weight: 600;
        transition: all 0.3s;
    }
</style>

<div class="profile-wrapper">
    <div class="container">
        <h2 class="page-title"><i class="fas fa-user-circle text-danger me-2"></i><?php echo $pageTitle; ?></h2>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-3 shadow-sm mt-4" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show border-0 rounded-3 shadow-sm mt-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row mt-4">
            <!-- Cột trái: Hiển thị thông tin -->
            <div class="col-lg-4 mb-4">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>
                        <h4 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($user['HoTen']); ?></h4>
                        <p class="mb-0 text-dark opacity-75">Thành viên hệ thống</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-id-badge me-2"></i>Mã Khách Hàng</div>
                            <div class="info-value mt-1"><?php echo htmlspecialchars($user['MaKH']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-user-tag me-2"></i>Tên đăng nhập</div>
                            <div class="info-value mt-1"><?php echo htmlspecialchars($user['TenDangNhap']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-envelope me-2"></i>Email</div>
                            <div class="info-value mt-1"><?php echo htmlspecialchars($user['Email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-phone-alt me-2"></i>Số điện thoại</div>
                            <div class="info-value mt-1"><?php echo htmlspecialchars($user['SDT']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ</div>
                            <div class="info-value mt-1"><?php echo htmlspecialchars($user['DiaChi']); ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Form cập nhật -->
            <div class="col-lg-8">
                <div class="profile-card">
                    <div class="card-body p-5">
                        
                        <ul class="nav nav-pills mb-4" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active rounded-pill px-4 fw-bold" id="pills-info-tab" data-bs-toggle="pill" data-bs-target="#pills-info" type="button" role="tab" aria-controls="pills-info" aria-selected="true"><i class="fas fa-edit me-2"></i>Cập nhật thông tin</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link rounded-pill px-4 fw-bold" id="pills-pass-tab" data-bs-toggle="pill" data-bs-target="#pills-pass" type="button" role="tab" aria-controls="pills-pass" aria-selected="false"><i class="fas fa-key me-2"></i>Đổi mật khẩu</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content" id="pills-tabContent">
                            <!-- Tab Thông tin -->
                            <div class="tab-pane fade show active" id="pills-info" role="tabpanel" aria-labelledby="pills-info-tab">
                                <form action="<?php echo BASE_URL; ?>user/updateProfile" method="post">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="ho_ten" class="form-label fw-bold text-muted">Họ và tên</label>
                                            <input type="text" class="form-control form-control-modern" id="ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($user['HoTen']); ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="sdt" class="form-label fw-bold text-muted">Số điện thoại</label>
                                            <input type="text" class="form-control form-control-modern" id="sdt" name="sdt" value="<?php echo htmlspecialchars($user['SDT']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-bold text-muted">Email</label>
                                        <input type="email" class="form-control form-control-modern" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="dia_chi" class="form-label fw-bold text-muted">Địa chỉ</label>
                                        <input type="text" class="form-control form-control-modern" id="dia_chi" name="dia_chi" value="<?php echo htmlspecialchars($user['DiaChi']); ?>">
                                    </div>
                                    <button type="submit" class="btn btn-success btn-modern w-100"><i class="fas fa-save me-2"></i>Lưu Thay Đổi Thông Tin</button>
                                </form>
                            </div>
                            
                            <!-- Tab Mật khẩu -->
                            <div class="tab-pane fade" id="pills-pass" role="tabpanel" aria-labelledby="pills-pass-tab">
                                <form action="<?php echo BASE_URL; ?>user/updatePassword" method="post">
                                    <div class="mb-3">
                                        <label for="old_password" class="form-label fw-bold text-muted">Mật khẩu cũ</label>
                                        <input type="password" class="form-control form-control-modern" id="old_password" name="old_password" required placeholder="Nhập mật khẩu hiện tại">
                                    </div>
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label fw-bold text-muted">Mật khẩu mới</label>
                                        <input type="password" class="form-control form-control-modern" id="new_password" name="new_password" required placeholder="Nhập mật khẩu mới">
                                    </div>
                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label fw-bold text-muted">Xác nhận mật khẩu mới</label>
                                        <input type="password" class="form-control form-control-modern" id="confirm_password" name="confirm_password" required placeholder="Nhập lại mật khẩu mới">
                                    </div>
                                    <button type="submit" class="btn btn-danger btn-modern w-100"><i class="fas fa-shield-alt me-2"></i>Đổi Mật Khẩu</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
