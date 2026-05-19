<?php 
// Đảm bảo các biến cần thiết (như $pageTitle) đã được thiết lập trong Controller

// Nhúng Header (bao gồm Navbar responsive)
require_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    /* Homepage Background Watermark */
    body {
        background-color: #f8f9fa;
        background-image: 
            linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.95)), 
            url('<?php echo BASE_URL; ?>public/images/background.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
    }

    /* Hero Banner Animated Gradient */
    #hero-banner {
        background: linear-gradient(-45deg, #007bff, #6610f2, #17a2b8, #28a745);
        background-size: 400% 400%;
        animation: gradientBG 15s ease infinite;
        border-radius: 30px !important;
        margin-top: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    #hero-banner::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('data:image/svg+xml;utf8,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.05)"/></svg>') repeat;
        opacity: 0.5;
        pointer-events: none;
    }

    /* Modern Sport Cards */
    .sport-card {
        border: none;
        border-radius: 25px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        z-index: 1;
        background-color: transparent;
    }
    .sport-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 25px 40px rgba(0,0,0,0.2) !important;
    }
    .sport-icon-bg {
        font-size: 8rem;
        position: absolute;
        bottom: -20px;
        right: -10px;
        opacity: 0.15;
        z-index: -1;
        transform: rotate(-20deg);
        transition: transform 0.4s ease;
        pointer-events: none;
    }
    .sport-card:hover .sport-icon-bg {
        transform: rotate(0deg) scale(1.2);
    }
    
    .bg-football { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-badminton { background: linear-gradient(135deg, #f2994a 0%, #f2c94c 100%); }
    .bg-pickleball { background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%); }

    /* Features Section */
    .feature-item {
        padding: 40px 20px;
        border-radius: 25px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.5);
        height: 100%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    .feature-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: rgba(255,255,255,0.8);
        background: rgba(255, 255, 255, 0.95);
    }
    .feature-icon-wrapper {
        width: 80px;
        height: 80px;
        margin: 0 auto 25px;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 5px 5px 15px rgba(0,0,0,0.05), -5px -5px 15px rgba(255,255,255,1);
    }

    /* Review Cards */
    .review-card {
        border: none;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
        position: relative;
        border: 1px solid rgba(255,255,255,0.5);
    }
    .review-card::after {
        content: '"';
        position: absolute;
        top: 10px;
        right: 20px;
        font-size: 5rem;
        color: rgba(0,0,0,0.03);
        font-family: serif;
        pointer-events: none;
    }
    .review-card:hover {
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 1);
    }
    
    .section-title {
        font-weight: 800;
        background: linear-gradient(45deg, #2c3e50, #3498db);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 3rem;
        text-align: center;
    }
    .btn-custom {
        transition: all 0.3s ease;
        font-weight: bold;
        border-radius: 30px;
        padding: 10px 30px;
    }
    .btn-custom:hover {
        transform: scale(1.05);
    }
</style>

<div class="container">
    <div id="hero-banner" class="p-5 mb-5 text-center text-white"> 
        <div class="container-fluid py-5 position-relative" style="z-index: 2;">
            <h1 class="display-4 fw-bold mb-4 text-shadow">Hệ Thống Đặt Sân Thể Thao</h1>
            <p class="col-md-8 fs-5 mx-auto mb-5 opacity-75">
                Trải nghiệm đặt sân nhanh chóng, tiện lợi cho Bóng đá, Cầu lông và Pickleball. Tham gia ngay cộng đồng thể thao sôi động của chúng tôi.
            </p>
            
            <?php if (!isset($_SESSION['user_logged_in'])): ?>
                <a href="<?php echo BASE_URL; ?>auth/hienThiDangKy" class="btn btn-light text-primary btn-lg btn-custom shadow-lg">🚀 Đăng Ký Thành Viên Ngay</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="booking-section" class="mb-5 py-4">
        <h2 class="section-title">Chọn Nhu Cầu Chơi Của Bạn</h2>
        <div class="row g-4 justify-content-center">
            <!-- Bóng đá -->
            <div class="col-lg-4 col-md-6">
                <?php $linkBD = isset($_SESSION['user_logged_in']) ? BASE_URL . 'datsan/chonsan?loai=bd' : BASE_URL . 'auth/hienThiDangNhap'; ?>
                <a href="<?php echo $linkBD; ?>" class="text-decoration-none">
                    <div class="card sport-card h-100 shadow-sm text-white bg-football p-4">
                        <div class="card-body d-flex flex-column text-center">
                            <h3 class="fw-bold mb-3">BÓNG ĐÁ</h3>
                            <p class="mb-4 opacity-75">Hệ thống sân cỏ nhân tạo tiêu chuẩn, đèn chiếu sáng hiện đại, thoáng mát.</p>
                            <div class="mt-auto">
                                <span class="btn btn-light text-success btn-custom w-75 mx-auto shadow-sm">Đặt sân ngay</span>
                            </div>
                            <div class="sport-icon-bg">⚽</div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Cầu lông -->
            <div class="col-lg-4 col-md-6">
                <?php $linkCL = isset($_SESSION['user_logged_in']) ? BASE_URL . 'datsan/chonsan?loai=cl' : BASE_URL . 'auth/hienThiDangNhap'; ?>
                <a href="<?php echo $linkCL; ?>" class="text-decoration-none">
                    <div class="card sport-card h-100 shadow-sm text-dark bg-badminton p-4">
                        <div class="card-body d-flex flex-column text-center">
                            <h3 class="fw-bold mb-3">CẦU LÔNG</h3>
                            <p class="mb-4 opacity-75">Sân thảm chất lượng cao, không gian rộng rãi, trang thiết bị chuyên nghiệp.</p>
                            <div class="mt-auto">
                                <span class="btn btn-dark text-warning btn-custom w-75 mx-auto shadow-sm">Đặt sân ngay</span>
                            </div>
                            <div class="sport-icon-bg">🏸</div>
                        </div>
                    </div>
                </a>
            </div>
            <!-- Pickleball -->
            <div class="col-lg-4 col-md-6">
                <?php $linkPKB = isset($_SESSION['user_logged_in']) ? BASE_URL . 'datsan/chonsan?loai=pkb' : BASE_URL . 'auth/hienThiDangNhap'; ?>
                <a href="<?php echo $linkPKB; ?>" class="text-decoration-none">
                    <div class="card sport-card h-100 shadow-sm text-white bg-pickleball p-4">
                        <div class="card-body d-flex flex-column text-center">
                            <h3 class="fw-bold mb-3">PICKLEBALL</h3>
                            <p class="mb-4 opacity-75">Xu hướng thể thao mới nhất, mặt sân thiết kế đạt tiêu chuẩn quốc tế.</p>
                            <div class="mt-auto">
                                <span class="btn btn-light text-primary btn-custom w-75 mx-auto shadow-sm">Đặt sân ngay</span>
                            </div>
                            <div class="sport-icon-bg">🎾</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="py-5 mb-5">
        <h2 class="section-title">Trải Nghiệm Dịch Vụ Hàng Đầu</h2>
        <div class="row g-4 text-center features-section">
            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon-wrapper">
                        <img src="<?php echo BASE_URL; ?>public/images/image_5.png" alt="" class="img-fluid" style="max-width: 40px;">
                    </div>
                    <h4 class="fw-bold text-dark mb-3">Kết Nối Đam Mê</h4>
                    <p class="text-muted">Tìm kiếm đồng đội, giao lưu giữa các CLB bóng đá, cầu lông và pickleball. Xây dựng cộng đồng thể thao sôi nổi.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon-wrapper">
                        <img src="<?php echo BASE_URL; ?>public/images/image_4.png" alt="" class="img-fluid" style="max-width: 40px;">
                    </div>
                    <h4 class="fw-bold text-dark mb-3">Một Chạm Đặt Lịch</h4>
                    <p class="text-muted">Không cần gọi điện, chủ động kiểm tra lịch trống và đặt sân ở bất kỳ đâu. Dễ dàng quản lý, hủy hoặc thay đổi lịch.</p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="feature-item">
                    <div class="feature-icon-wrapper">
                        <img src="<?php echo BASE_URL; ?>public/images/image_3.png" alt="" class="img-fluid" style="max-width: 40px;">
                    </div>
                    <h4 class="fw-bold text-dark mb-3">Hệ Sinh Thái Đa Dạng</h4>
                    <p class="text-muted">Theo dõi lịch sử thuê sân, mua sắm phụ kiện và chia sẻ đánh giá chất lượng để cùng phát triển cộng đồng uy tín.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reviews Section -->
    <?php if (isset($recentReviews) && !empty($recentReviews)): ?>
    <div class="py-5 mb-5">
        <h2 class="section-title">Khách Hàng Nói Gì Về Chúng Tôi</h2>
        <div class="row g-4 justify-content-center">
            <?php foreach ($recentReviews as $review): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card review-card h-100 p-3">
                        <div class="card-body d-flex flex-column">
                            <div class="review-stars mb-3 text-warning fs-5">
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <?php if ($i < $review['Diem']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                            <blockquote class="blockquote mb-4 flex-grow-1">
                                <p class="fs-6 fst-italic text-dark">"<?php echo htmlspecialchars($review['NoiDung']); ?>"</p>
                            </blockquote>
                            
                            <div class="d-flex align-items-center mt-auto border-top pt-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold me-3" style="width: 40px; height: 40px;">
                                    <?php echo strtoupper(substr($review['HoTen'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($review['HoTen']); ?></h6>
                                    <small class="text-muted"><i class="fas fa-map-marker-alt me-1 text-danger"></i><?php echo htmlspecialchars($review['TenSan']); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php 
// Nhúng Footer
require_once __DIR__ . '/../layouts/footer.php'; 
?>