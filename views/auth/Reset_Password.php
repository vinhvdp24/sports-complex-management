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
                    <h3 class="card-title text-center fw-bold mb-4">Đặt Lại Mật Khẩu</h3>
                    <p class="text-center text-muted mb-4">Tạo mật khẩu mới cho tài khoản của bạn.</p>
                    
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>auth/datLaiMatKhau">
                        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" name="password_confirm" id="password_confirm" class="form-control" required>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success">Lưu Mật Khẩu Mới</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// Giả sử footer.php chứa các link JS của Bootstrap
include __DIR__ . '/../layouts/footer.php'; 
?>