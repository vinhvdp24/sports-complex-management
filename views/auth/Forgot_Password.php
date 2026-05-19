<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
// Giả sử header.php chứa các link CSS của Bootstrap
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h3 class="card-title text-center fw-bold mb-4">Quên Mật Khẩu</h3>
                    <p class="text-center text-muted mb-4">Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>

                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>auth/guiEmailReset">
                        <div class="mb-3">
                            <label for="email" class="form-label">Địa chỉ Email</label>
                            <input type="email" name="email" id="email" class="form-control" required placeholder="vidu@email.com">
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary">Gửi Yêu Cầu</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <a href="<?= BASE_URL ?>auth/hienThiDangNhap" class="text-decoration-none">Quay lại trang Đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Giả sử footer.php chứa các link JS của Bootstrap
include __DIR__ . '/../layouts/footer.php'; 
?>