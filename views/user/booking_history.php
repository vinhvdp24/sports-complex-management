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
        background: linear-gradient(45deg, #00c6ff, #0072ff);
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
        <h2 class="page-title"><i class="fas fa-calendar-check text-primary me-2"></i><?php echo $pageTitle; ?></h2>

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
            <?php if (empty($lichSuDatSan)): ?>
                <div class="p-5 text-center">
                    <img src="<?= BASE_URL ?>public/images/empty-calendar.svg" alt="Empty" style="max-width: 150px; opacity: 0.5; margin-bottom: 20px;">
                    <h4 class="text-muted mb-3">Chưa có lịch sử đặt sân</h4>
                    <p class="text-muted">Bạn chưa từng đặt sân nào trên hệ thống.</p>
                    <a href="<?php echo BASE_URL; ?>dat-san" class="btn btn-primary rounded-pill px-4 py-2 mt-2"><i class="fas fa-plus me-2"></i>Đặt Sân Ngay</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Sân Bóng</th>
                                <th width="20%">Thời Gian Đặt</th>
                                <th width="15%">Tổng Tiền</th>
                                <th width="15%">Trạng Thái</th>
                                <th width="25%" class="text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            date_default_timezone_set('Asia/Ho_Chi_Minh');
                            $thoiGianHienTai = new DateTime();

                            foreach ($lichSuDatSan as $index => $datSan): 
                                $thoiGianDatSan = new DateTime($datSan['NgayDat'] . ' ' . $datSan['GioBatDau']);
                            ?>
                                <tr onclick="if(!event.target.closest('.action-buttons')) window.location.href='<?php echo BASE_URL; ?>user/booking-details?id=<?php echo $datSan['MaDatSan']; ?>'" style="cursor: pointer;" title="Nhấn để xem chi tiết">
                                    <td class="text-muted fw-bold"><?php echo $index + 1; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-3 text-primary">
                                                <i class="fas fa-futbol fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark"><?php echo htmlspecialchars($datSan['TenSan']); ?></h6>
                                                <small class="text-muted">Mã ĐS: #<?php echo $datSan['MaDatSan']; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><i class="far fa-calendar-alt me-2 text-muted"></i><?php echo date('d/m/Y', strtotime($datSan['NgayDat'])); ?></div>
                                        <div class="small text-muted"><i class="far fa-clock me-2"></i><?php echo date('H:i', strtotime($datSan['GioBatDau'])); ?> - <?php echo date('H:i', strtotime($datSan['GioKetThuc'])); ?></div>
                                    </td>
                                    <td>
                                        <?php 
                                            $tongTienHienThi = $datSan['TongTienHoaDon'] ?? $datSan['TongTien'];
                                        ?>
                                        <strong class="text-success"><?php echo number_format($tongTienHienThi, 0, ',', '.'); ?>đ</strong>
                                    </td>
                                    <td>
                                        <?php 
                                            $status = htmlspecialchars($datSan['TrangThai']);
                                            $badge_class = 'bg-secondary';
                                            if ($status === 'Chờ thanh toán') $badge_class = 'bg-warning text-dark';
                                            elseif ($status === 'Đã thanh toán' || $status === 'Đã đặt sân') $badge_class = 'bg-primary';
                                            elseif ($status === 'Hoàn thành') $badge_class = 'bg-success';
                                            elseif (strpos($status, 'Đã hủy') === 0) $badge_class = 'bg-danger';
                                        ?>
                                        <span class="badge badge-status <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                                    </td>
                                    <td class="text-end action-buttons">
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="<?php echo BASE_URL; ?>user/booking-details?id=<?php echo $datSan['MaDatSan']; ?>" class="btn btn-action btn-outline-primary">Chi tiết</a>
                                            <?php 
                                            if ($thoiGianDatSan > $thoiGianHienTai && strpos($datSan['TrangThai'], 'Đã hủy') !== 0): 
                                            ?>
                                                <form method="POST" action="<?php echo BASE_URL; ?>user/cancelBooking" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch đặt này không?');" class="m-0">
                                                    <input type="hidden" name="MaDatSan" value="<?php echo $datSan['MaDatSan']; ?>">
                                                    <button type="submit" class="btn btn-action btn-outline-danger">Hủy</button>
                                                </form>
                                            <?php 
                                            endif; 
                                            $thoiGianKetThuc = new DateTime($datSan['NgayDat'] . ' ' . $datSan['GioKetThuc']);
                                            if ($thoiGianKetThuc < $thoiGianHienTai):
                                            ?>
                                                <a href="<?php echo BASE_URL; ?>user/review?booking_id=<?php echo $datSan['MaDatSan']; ?>" class="btn btn-action btn-info text-white">Đánh giá</a>
                                            <?php endif; ?>
                                        </div>
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
