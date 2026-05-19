<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .history-wrapper {
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
        background: linear-gradient(45deg, #f39c12, #e67e22);
        bottom: -10px;
        left: 0;
        border-radius: 2px;
    }
    .history-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-modern {
        margin-bottom: 0;
    }
    .table-modern thead th {
        background-color: #f8f9fa;
        color: #636e72;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dfe4ea;
        padding: 15px 20px;
    }
    .table-modern tbody td {
        padding: 15px 20px;
        vertical-align: middle;
        color: #2d3436;
        border-bottom: 1px solid #f1f2f6;
    }
    .table-modern tbody tr {
        transition: all 0.3s ease;
    }
    .table-modern tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        position: relative;
        z-index: 1;
    }
    .badge-status {
        padding: 8px 12px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .btn-action {
        border-radius: 10px;
        padding: 6px 15px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
</style>

<div class="history-wrapper">
    <div class="container">
        <h2 class="page-title"><i class="fas fa-file-invoice-dollar text-warning me-2"></i><?php echo $pageTitle; ?></h2>

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


        <div class="history-card mt-4">
            <?php if (empty($lichSuHoaDon)): ?>
                <div class="p-5 text-center">
                    <img src="<?= BASE_URL ?>public/images/empty-cart.svg" alt="Empty" style="max-width: 150px; opacity: 0.5; margin-bottom: 20px;">
                    <h4 class="text-muted mb-3">Chưa có lịch sử mua hàng</h4>
                    <p class="text-muted">Bạn chưa từng đặt hàng nào trên cửa hàng.</p>
                    <a href="<?php echo BASE_URL; ?>store" class="btn btn-warning rounded-pill px-4 py-2 mt-2 text-white fw-bold"><i class="fas fa-shopping-bag me-2"></i>Mua Sắm Ngay</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th width="15%">Đơn Hàng</th>
                                <th width="20%">Thời Gian</th>
                                <th width="15%">Tổng Tiền</th>
                                <th width="15%">Thanh toán</th>
                                <th width="20%">Trạng Thái</th>
                                <th width="15%" class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lichSuHoaDon as $hoaDon): ?>
                                <tr onclick="if(!event.target.closest('.action-buttons')) window.location.href='<?= BASE_URL ?>user/order-details/<?= $hoaDon['id'] ?>'" style="cursor: pointer;" title="Nhấn để xem chi tiết đơn hàng">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-warning">
                                                <i class="fas fa-box-open fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">#<?php echo htmlspecialchars($hoaDon['id']); ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="far fa-calendar-alt me-2 text-muted"></i><?php echo date('d/m/Y', strtotime($hoaDon['created_at'])); ?></div>
                                        <div class="small text-muted"><i class="far fa-clock me-2"></i><?php echo date('H:i', strtotime($hoaDon['created_at'])); ?></div>
                                    </td>
                                    <td><strong class="text-success"><?php echo number_format($hoaDon['total_amount'], 0, ',', '.'); ?>đ</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if($hoaDon['payment_method'] === 'MoMo'): ?>
                                                <img src="<?= BASE_URL ?>public/images/momo.png" alt="MoMo" style="height: 20px; border-radius:4px;">
                                            <?php else: ?>
                                                <i class="fas fa-money-bill-wave text-success"></i>
                                            <?php endif; ?>
                                            <span class="small fw-bold"><?php echo htmlspecialchars($hoaDon['payment_method']); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = htmlspecialchars($hoaDon['status']);
                                            $badge_class = 'bg-secondary';
                                            if ($status === 'Chờ xử lý') {
                                                $badge_class = 'bg-warning text-dark';
                                            } elseif ($status === 'Chuẩn bị hàng') {
                                                $badge_class = 'bg-info text-dark';
                                            } elseif ($status === 'Đã bàn giao cho đơn vị vận chuyển') {
                                                $badge_class = 'bg-primary';
                                            } elseif ($status === 'Đã giao hàng') {
                                                $badge_class = 'bg-success';
                                            } elseif ($status === 'Đã hủy') {
                                                $badge_class = 'bg-danger';
                                            }
                                        ?>
                                        <span class="badge badge-status <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                    </td>
                                    <td class="text-end action-buttons">
                                        <a href="<?= BASE_URL ?>user/order-details/<?= $hoaDon['id'] ?>" class="btn btn-action btn-outline-warning">Chi tiết</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
