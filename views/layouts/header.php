<?php 
// Đảm bảo BASE_URL đã được định nghĩa
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
$hoTen = $isLoggedIn ? ($_SESSION['ho_ten'] ?? $_SESSION['username']) : '';
$userRole = $isLoggedIn ? ($_SESSION['user_role'] ?? '') : '';
$pageTitle = $pageTitle ?? 'Trang Chủ'; 
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống đặt sân thể thao - <?php echo $pageTitle; ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-zoom@2.0.1/dist/chartjs-plugin-zoom.min.js"></script>
    <script>
        function updateCartCount() {
            fetch('<?php echo BASE_URL; ?>cart/count')
                .then(response => response.json())
                .then(data => {
                    const cartCountElement = document.getElementById('cart-count');
                    if (cartCountElement) {
                        cartCountElement.innerText = data.count;
                    }
                })
                .catch(error => console.error('Error fetching cart count:', error));
        }

        // Cập nhật khi tải trang
        document.addEventListener('DOMContentLoaded', () => {
            updateCartCount();
        });
    </script>
</head>
<body>

<style>
    /* Custom Header Styles */
    .custom-navbar {
        background: linear-gradient(to right, #0f2027, #203a43, #2c5364) !important;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        padding: 0.8rem 0;
        transition: all 0.3s ease;
    }
    .navbar-brand {
        font-size: 1.5rem;
        letter-spacing: 0.5px;
    }
    .nav-btn {
        border-radius: 20px;
        padding: 8px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }
    .nav-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }
    .btn-cart {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
    }
    .btn-cart:hover {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }
    .btn-login {
        background: transparent;
        border: 1px solid rgba(255,255,255,0.5);
    }
    .btn-register {
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        color: white !important;
        border: none;
    }
</style>

<header class="sticky-top">
    <nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
        <div class="container-fluid px-4">
            <div class="d-flex align-items-center">
                <a class="navbar-brand text-white fw-bold mb-0 d-flex align-items-center" href="<?php echo BASE_URL; ?>home/index">
                    <span class="fs-3 me-2">⚽</span> <span style="background: linear-gradient(45deg, #00f2fe, #4facfe); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Hệ thống đặt sân thể thao</span>
                </a>
            </div>
            
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center gap-2 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link text-white fw-medium px-3" href="<?php echo BASE_URL; ?>home/index">Trang Chủ</a>
                    </li>
                    
                    <?php if ($isLoggedIn): ?>
                        <li class="nav-item">
                            <a class="btn btn-warning nav-btn text-dark fw-bold ms-2 shadow-sm" href="<?php echo BASE_URL; ?>store">
                                <i class="fas fa-store me-1"></i> Cửa Hàng
                            </a>
                        </li>

                        <?php if ($userRole !== 'admin' && $userRole !== 'owner'): ?>
                        <li class="nav-item">
                            <a class="btn btn-cart nav-btn ms-2 position-relative" href="<?php echo BASE_URL; ?>cart">
                                <i class="fas fa-shopping-cart me-1"></i> Giỏ hàng
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light shadow-sm" id="cart-count">0</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light text-primary nav-btn ms-2 shadow-sm" href="<?php echo BASE_URL; ?>user/dashboard">
                                <i class="fas fa-user-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php if ($userRole === 'admin' || $userRole === 'owner'): ?>
                            <li class="nav-item">
                                <a class="btn btn-danger nav-btn ms-2 shadow-sm" href="<?= BASE_URL ?>admin-dashboard"> 
                                    <i class="fas fa-cog me-1"></i> <?= $userRole === 'admin' ? 'Quản Trị' : 'Chủ Sân' ?> 
                                </a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="nav-item">
                            <a class="btn btn-outline-light nav-btn ms-2" href="<?php echo BASE_URL; ?>auth/dangXuat">
                                <i class="fas fa-sign-out-alt me-1"></i> Đăng Xuất
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-login nav-btn text-white ms-2" href="<?php echo BASE_URL; ?>auth/hienThiDangNhap">Đăng Nhập</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-register nav-btn ms-2 shadow-sm" href="<?php echo BASE_URL; ?>auth/hienThiDangKy">Đăng Ký</a>
                        </li>
                    <?php endif;?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<div id="wrapper">
    <main class="container mt-4 flex-fill">