<?php
// Cờ hiệu đã được kiểm tra ở Controller, chỉ cần xóa cờ sau khi hiển thị thành công
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
unset($_SESSION['dat_san_thanh_cong']);
?>

<style>
    .success-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        background: #f8f9fa;
        padding: 40px 0;
    }
    .success-card {
        background: #fff;
        border-radius: 25px;
        border: none;
        box-shadow: 0 15px 40px rgba(0,0,0,0.08);
        padding: 3rem;
        position: relative;
        overflow: hidden;
    }
    .success-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, #00b09b, #96c93d);
    }
    .icon-circle {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #e0f8e9 0%, #d1f2eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 2rem;
        box-shadow: 0 10px 20px rgba(0, 176, 155, 0.15);
    }
    .icon-circle i {
        font-size: 4rem;
        background: linear-gradient(45deg, #00b09b, #96c93d);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .success-title {
        font-weight: 800;
        color: #2d3436;
        margin-bottom: 1rem;
        letter-spacing: -0.5px;
    }
    .success-text {
        color: #636e72;
        font-size: 1.15rem;
        line-height: 1.6;
        margin-bottom: 2rem;
    }
    .btn-action {
        border-radius: 15px;
        padding: 15px 25px;
        font-size: 1.1rem;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-history {
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        color: white;
        border: none;
        box-shadow: 0 10px 20px rgba(0, 114, 255, 0.3);
    }
    .btn-history:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(0, 114, 255, 0.4);
        color: white;
    }
    .btn-home {
        background: transparent;
        color: #636e72;
        border: 2px solid #dfe4ea;
    }
    .btn-home:hover {
        background: #f8f9fa;
        color: #2d3436;
        border-color: #b2bec3;
        transform: translateY(-3px);
    }
</style>

<div class="success-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="success-card text-center">
                    
                    <div class="icon-circle">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    
                    <h1 class="success-title">ĐẶT SÂN THÀNH CÔNG!</h1>
                    
                    <p class="success-text">
                        Tuyệt vời! Đơn đặt sân của bạn đã được hệ thống ghi nhận thành công. Vui lòng kiểm tra lại thông tin chi tiết trong lịch sử để chuẩn bị tốt nhất cho trận đấu.
                    </p>
                    
                    <hr class="my-4" style="border-color: #f1f2f6;">

                    <div class="d-grid gap-3 mt-4">
                        <a href="<?= BASE_URL ?>user/booking-history" class="btn btn-action btn-history">
                            <i class="fas fa-history me-2"></i>Xem Lịch Sử Đặt Sân
                        </a>
                        <a href="<?= BASE_URL ?>home/index" class="btn btn-action btn-home">
                            <i class="fas fa-home me-2"></i>Quay về Trang Chủ
                        </a>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>