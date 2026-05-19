<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .user-dashboard-header {
        font-weight: 800;
        background: -webkit-linear-gradient(45deg, #28a745, #20c997);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .user-card {
        border: none;
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .user-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    .user-card .card-body {
        position: relative;
        z-index: 2;
        padding: 2.5rem 1.5rem;
    }
    .card-icon-bg {
        font-size: 7rem;
        position: absolute;
        right: -15px;
        bottom: -25px;
        opacity: 0.15;
        z-index: 1;
        transform: rotate(-15deg);
        transition: transform 0.3s ease;
        pointer-events: none;
    }
    .user-card:hover .card-icon-bg {
        transform: rotate(0deg) scale(1.1);
    }
    .bg-gradient-booking { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-invoice { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
    .bg-gradient-profile { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
    .btn-user-action { transition: all 0.2s ease; font-weight: 600; }
    .btn-user-action:hover { transform: scale(1.05); }
</style>

<div class="container my-5">
    <div class="d-flex align-items-center mb-4">
        <h2 class="user-dashboard-header m-0">👋 Trang Tổng Quan Khách Hàng</h2>
    </div>
    <p class="text-muted fs-5 mb-5">Chào mừng bạn quay trở lại! Bạn có thể tra cứu và quản lý các hoạt động cá nhân của mình tại đây.</p>

    <div class="row g-4 justify-content-center">
        <!-- Xem lịch đặt sân -->
        <div class="col-md-4 col-sm-6">
            <div class="card user-card bg-gradient-booking text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <h4 class="card-title fw-bold mb-3">Lịch Đặt Sân</h4>
                    <p class="card-text flex-grow-1 opacity-75 mb-4">Xem chi tiết và lịch sử các sân bóng bạn đã đặt trong thời gian qua.</p>
                    <div class="mt-auto">
                        <a href="<?php echo BASE_URL; ?>user/bookingHistory" class="btn btn-light btn-user-action rounded-pill px-4 text-primary shadow-sm">Xem chi tiết</a>
                    </div>
                    <div class="card-icon-bg">📅</div>
                </div>
            </div>
        </div>

        <!-- Xem hóa đơn -->
        <div class="col-md-4 col-sm-6">
            <div class="card user-card bg-gradient-invoice text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <h4 class="card-title fw-bold mb-3 text-dark">Lịch Sử Mua Hàng</h4>
                    <p class="card-text flex-grow-1 text-dark opacity-75 mb-4">Tra cứu, kiểm tra và quản lý các hóa đơn thanh toán của bạn.</p>
                    <div class="mt-auto">
                        <a href="<?php echo BASE_URL; ?>user/invoiceHistory" class="btn btn-dark btn-user-action rounded-pill px-4 shadow-sm">Xem chi tiết</a>
                    </div>
                    <div class="card-icon-bg text-dark">🧾</div>
                </div>
            </div>
        </div>

        <!-- Thông tin cá nhân -->
        <div class="col-md-4 col-sm-6">
            <div class="card user-card bg-gradient-profile text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column text-center">
                    <h4 class="card-title fw-bold mb-3 text-dark">Thông Tin Cá Nhân</h4>
                    <p class="card-text flex-grow-1 text-dark opacity-75 mb-4">Cập nhật mật khẩu và chỉnh sửa thông tin hồ sơ của bạn.</p>
                    <div class="mt-auto">
                        <a href="<?php echo BASE_URL; ?>user/showProfile" class="btn btn-danger btn-user-action rounded-pill px-4 shadow-sm">Quản lý hồ sơ</a>
                    </div>
                    <div class="card-icon-bg text-dark">👤</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
