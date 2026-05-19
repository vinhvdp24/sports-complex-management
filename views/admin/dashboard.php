<?php 
$role = $data['role'] ?? 'admin';
$isOwner = ($role === 'owner');
?>

<style>
    .dashboard-header {
        font-weight: 800;
        background: -webkit-linear-gradient(45deg, #007bff, #6610f2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dashboard-header.owner {
        background: -webkit-linear-gradient(45deg, #f6d365, #fda085);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .dashboard-card {
        border: none;
        border-radius: 20px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    .dashboard-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
    }
    .dashboard-card .card-body {
        position: relative;
        z-index: 2;
        padding: 1.5rem;
    }
    .card-icon {
        font-size: 5rem;
        position: absolute;
        right: -10px;
        bottom: -15px;
        opacity: 0.15;
        z-index: 1;
        transform: rotate(-15deg);
        transition: transform 0.3s ease;
        pointer-events: none;
    }
    .dashboard-card:hover .card-icon {
        transform: rotate(0deg) scale(1.1);
    }
    .bg-gradient-info { background: linear-gradient(135deg, #36D1DC 0%, #5B86E5 100%); }
    .bg-gradient-warning { background: linear-gradient(135deg, #FFB75E 0%, #ED8F03 100%); }
    .bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
    .bg-gradient-primary { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
    .bg-gradient-secondary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .bg-gradient-danger { background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); }
    .bg-gradient-dark { background: linear-gradient(135deg, #2c3e50 0%, #000000 100%); }
    .bg-gradient-owner { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
    .display-number { font-weight: 800; font-size: 2.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); line-height: 1; }
    .btn-access { transition: all 0.2s ease; font-weight: 600; }
    .btn-access:hover { transform: scale(1.05); }
</style>

<div class="container my-5">
    <div class="d-flex align-items-center mb-4">
        <h1 class="<?= $isOwner ? 'dashboard-header owner' : 'dashboard-header' ?> m-0">
            <?= $isOwner ? '👑 Bảng Điều Khiển Chủ Sân' : '🛠️ Bảng Điều Khiển Quản Trị' ?>
        </h1>
    </div>
    <p class="text-muted fs-5 mb-5">Chào mừng, <strong class="text-dark"><?php echo htmlspecialchars($data['username']); ?></strong>. Chúc bạn một ngày làm việc hiệu quả!</p>
    
    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3"><i class="fas fa-check-circle me-2"></i><?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Quản Lý Sân Bóng -->
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-info text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3">Quản Lý Sân Bóng</h5>
                    <div class="mt-auto d-flex justify-content-between align-items-end">
                        <div>
                            <p class="mb-1 opacity-75 small">Tổng số sân</p>
                            <div class="display-number"><?php echo htmlspecialchars($data['totalSan']); ?></div>
                        </div>
                        <a href="<?= BASE_URL ?>admin-san" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-primary shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">⚽</div>
                </div>
            </div>
        </div>
        
        <!-- Quản Lý Dịch Vụ -->
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-warning text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3 text-white">Quản Lý Dịch Vụ</h5>
                    <div class="mt-auto d-flex justify-content-between align-items-end">
                        <div>
                            <p class="mb-1 opacity-75 small text-white">Tổng số dịch vụ</p>
                            <div class="display-number text-white"><?php echo htmlspecialchars($data['totalDichVu']); ?></div>
                        </div>
                        <a href="<?= BASE_URL ?>admin/dichvu" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-warning shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">🥤</div>
                </div>
            </div>
        </div>

        <!-- Quản Lý Đơn Hàng -->
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-success text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3">Quản Lý Đơn Hàng</h5>
                    <div class="mt-auto d-flex justify-content-between align-items-end">
                        <div>
                            <p class="mb-1 opacity-75 small">Chờ xử lý</p>
                            <div class="display-number"><?php echo htmlspecialchars($data['pendingOrdersCount']); ?></div>
                        </div>
                        <a href="<?= BASE_URL ?>admin/don-hang" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-success shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">🛒</div>
                </div>
            </div>
        </div>

        <!-- Đặt Sân Theo Ngày -->
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-primary text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3">Lịch Đặt Sân</h5>
                    <p class="card-text flex-grow-1 opacity-75 small mb-4">Quản lý và theo dõi các lượt đặt sân bóng theo ngày.</p>
                    <div class="mt-auto text-end">
                        <a href="<?= BASE_URL ?>admin/hienThiDatSanTheoNgay" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-primary shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">📅</div>
                </div>
            </div>
        </div>

        <!-- Quản Lý Khách Hàng -->
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-secondary text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3">Khách Hàng</h5>
                    <p class="card-text flex-grow-1 opacity-75 small mb-4">Tra cứu thông tin, lịch sử và quản lý khách hàng.</p>
                    <div class="mt-auto text-end">
                        <a href="<?= BASE_URL ?>admin/khachhang" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-secondary shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">👥</div>
                </div>
            </div>
        </div>

        <!-- Quản Lý Kho Hàng -->
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-danger text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3">Quản Lý Kho Hàng</h5>
                    <div class="mt-auto d-flex justify-content-between align-items-end">
                        <div>
                            <p class="mb-1 opacity-75 small">Sản phẩm trong kho</p>
                            <div class="display-number"><?php echo htmlspecialchars($data['totalProducts']); ?></div>
                        </div>
                        <a href="<?= BASE_URL ?>admin/kho-hang" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-danger shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">📦</div>
                </div>
            </div>
        </div>

        <!-- Chức năng chỉ dành cho Owner -->
        <?php if ($isOwner): ?>
        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-dark text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3 text-warning">Quản Lý Nhân Viên</h5>
                    <p class="card-text flex-grow-1 opacity-75 small mb-4">Phân quyền, cấp tài khoản cho các Admin và Staff.</p>
                    <div class="mt-auto text-end">
                        <a href="<?= BASE_URL ?>admin/nhan-vien" class="btn btn-warning btn-sm btn-access rounded-pill px-3 text-dark shadow-sm">Truy cập</a>
                    </div>
                    <div class="card-icon">👨‍💼</div>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="card dashboard-card bg-gradient-owner text-white h-100 shadow-sm">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3">Thống Kê Doanh Thu</h5>
                    <p class="card-text flex-grow-1 opacity-75 small mb-4">Xem biểu đồ, báo cáo doanh thu chi tiết theo thời gian.</p>
                    <div class="mt-auto text-end">
                        <a href="<?= BASE_URL ?>admin/revenue" class="btn btn-light btn-sm btn-access rounded-pill px-3 text-danger shadow-sm">Xem báo cáo</a>
                    </div>
                    <div class="card-icon">📈</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>