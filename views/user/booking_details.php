<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}
require_once __DIR__ . '/../layouts/header.php';

// Dữ liệu được truyền từ UserController::bookingDetails()
$booking = $data['booking'] ?? null;
$services = $data['services'] ?? [];
?>

<div class="container my-5">
    <h2 class="mb-4 text-primary"><?php echo htmlspecialchars($pageTitle ?? 'Chi Tiết Đặt Sân'); ?></h2>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Thông Tin Lịch Đặt</h5>
        </div>
        <div class="card-body">
            <?php if ($booking): ?>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Tên sân:</strong> <?php echo htmlspecialchars($booking['TenSan']); ?></p>
                        <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y', strtotime($booking['NgayDat'])); ?></p>
                        <p><strong>Thời gian:</strong> <?php echo date('H:i', strtotime($booking['GioBatDau'])) . ' - ' . date('H:i', strtotime($booking['GioKetThuc'])); ?></p>
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
                            <span class="badge <?php echo $badgeClass; ?>"><?php echo htmlspecialchars($booking['TrangThai']); ?></span>
                        </p>
                        <p><strong>Tiền sân:</strong> <?php echo number_format($booking['TongTien'], 0, ',', '.'); ?> VNĐ</p>
                    </div>
                </div>
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
                                    <td><?php echo $index + 1; ?></td>
                                    <td><?php echo htmlspecialchars($service['TenDV']); ?></td>
                                    <td class="text-center"><?php echo htmlspecialchars($service['SoLuong']); ?></td>
                                    <td class="text-end"><?php echo number_format($service['DonGia'], 0, ',', '.'); ?> VNĐ</td>
                                    <td class="text-end"><?php echo number_format($thanhTien, 0, ',', '.'); ?> VNĐ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="4" class="text-end">Tổng tiền dịch vụ:</td>
                                <td class="text-end text-danger"><?php echo number_format($totalServiceCost, 0, ',', '.'); ?> VNĐ</td>
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
                        // Hiển thị tổng tiền từ hóa đơn nếu có, nếu không thì tính tổng (tiền sân + dịch vụ)
                        $tongHoaDon = $booking['TongTienHoaDon'] ?? ($booking['TongTien'] + $totalServiceCost);
                        echo number_format($tongHoaDon, 0, ',', '.'); 
                    ?> VNĐ
                </span>
            </h4>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?php echo BASE_URL; ?>user/bookingHistory" class="btn btn-secondary">Quay Lại Lịch Sử</a>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
