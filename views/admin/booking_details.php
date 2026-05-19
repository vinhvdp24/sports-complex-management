<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}

// Dữ liệu được truyền từ AdminController::bookingDetails()
$booking = $data['booking'] ?? null;
$services = $data['services'] ?? [];
$pageTitle = $data['title'] ?? 'Chi Tiết Đặt Sân';

// Giao diện Admin mặc định không sử dụng header/footer chính, vì vậy sẽ dùng layout đơn giản.
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; }
    </style>
</head>
<body>

<div class="container my-5">
    <h2 class="mb-4 text-primary"><?= htmlspecialchars($pageTitle) ?></h2>

    <?php if (isset($_SESSION['admin_success']) || isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['admin_success'] ?? $_SESSION['success_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['admin_success'], $_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['admin_error']) || isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['admin_error'] ?? $_SESSION['error_message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['admin_error'], $_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Thông Tin Lịch Đặt</h5>
        </div>
        <div class="card-body">
            <?php if ($booking): ?>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($booking['HoTen']) ?></p>
                        <p><strong>Tên sân:</strong> <?= htmlspecialchars($booking['TenSan']) ?></p>
                        <p><strong>Ngày đặt:</strong> <?= date('d/m/Y', strtotime($booking['NgayDat'])) ?></p>
                        <p><strong>Thời gian:</strong> <?= date('H:i', strtotime($booking['GioBatDau'])) . ' - ' . date('H:i', strtotime($booking['GioKetThuc'])) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Trạng thái:</strong> 
                            <?php 
                                $badgeClass = 'bg-success';
                                if (strpos($booking['TrangThai'], 'Đã hủy') === 0) {
                                    $badgeClass = 'bg-danger';
                                } else if ($booking['TrangThai'] === 'Chờ thanh toán') {
                                    $badgeClass = 'bg-warning text-dark';
                                }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($booking['TrangThai']) ?></span>
                        </p>
                        <p><strong>Tiền sân:</strong> <?= number_format($booking['TongTien'], 0, ',', '.') ?> VNĐ</p>
                    </div>
                </div>
                
                <?php if ($booking['TrangThai'] === 'Chờ thanh toán'): ?>
                    <hr>
                    <div class="text-end">
                        <form method="POST" action="<?= BASE_URL ?>admin/xuLyXacNhanThanhToan" onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận khách đã thanh toán đơn này không?');">
                            <input type="hidden" name="MaDatSan" value="<?= $booking['MaDatSan'] ?>">
                            <input type="hidden" name="redirect" value="<?= BASE_URL ?>admin/booking-details?id=<?= $booking['MaDatSan'] ?>">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-1"></i> Xác Nhận Đã Thanh Toán
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p class="text-danger">Không tìm thấy thông tin đặt sân.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header">
            <h5 class="mb-0">Dịch Vụ Đi Kèm</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($services)): ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên Dịch Vụ</th>
                                <th class="text-center">Số Lượng</th>
                                <th class="text-end">Đơn Giá</th>
                                <th class="text-end">Thành Tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $totalServiceCost = 0;
                            foreach ($services as $index => $service): 
                                $thanhTien = $service['SoLuong'] * $service['DonGia'];
                                $totalServiceCost += $thanhTien;
                            ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= htmlspecialchars($service['TenDV']) ?></td>
                                    <td class="text-center"><?= htmlspecialchars($service['SoLuong']) ?></td>
                                    <td class="text-end"><?= number_format($service['DonGia'], 0, ',', '.') ?> VNĐ</td>
                                    <td class="text-end"><?= number_format($thanhTien, 0, ',', '.') ?> VNĐ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Tổng tiền dịch vụ:</td>
                                <td class="text-end text-danger"><?= number_format($totalServiceCost, 0, ',', '.') ?> VNĐ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <p>Không có dịch vụ nào được sử dụng.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm mt-4 bg-light">
        <div class="card-body text-end">
            <h4 class="fw-bold">
                TỔNG HÓA ĐƠN:
                <span class="text-primary">
                    <?php 
                        $tongHoaDon = $booking['TongTienHoaDon'] ?? ($booking['TongTien'] + $totalServiceCost);
                        echo number_format($tongHoaDon, 0, ',', '.'); 
                    ?> VNĐ
                </span>
            </h4>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= BASE_URL ?>admin/datsan-theo-ngay" class="btn btn-secondary">Quay Lại Danh Sách</a>
    </div>
</div>

</body>
</html>
