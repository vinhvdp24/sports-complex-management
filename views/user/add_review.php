<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <h2 class="mb-4 text-primary">Đánh giá lịch đặt sân</h2>

    <?php if (isset($booking) && $booking): ?>
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Thông tin lịch đặt</h5>
            </div>
            <div class="card-body">
                <p><strong>Tên sân:</strong> <?php echo htmlspecialchars($booking['TenSan']); ?></p>
                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y', strtotime($booking['NgayDat'])); ?></p>
                <p><strong>Thời gian:</strong> <?php echo date('H:i', strtotime($booking['GioBatDau'])) . ' - ' . date('H:i', strtotime($booking['GioKetThuc'])); ?></p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form action="<?php echo BASE_URL; ?>user/submitReview" method="POST">
                    <input type="hidden" name="MaDatSan" value="<?php echo htmlspecialchars($booking['MaDatSan']); ?>">
                    <input type="hidden" name="MaSan" value="<?php echo htmlspecialchars($booking['MaSan']); ?>">

                    <div class="mb-3">
                        <label for="diem" class="form-label"><strong>Điểm đánh giá (từ 1 đến 5)</strong></label>
                        <select class="form-select" id="diem" name="diem" required>
                            <option value="" disabled selected>-- Chọn điểm --</option>
                            <option value="5">5 - Rất tuyệt vời</option>
                            <option value="4">4 - Tốt</option>
                            <option value="3">3 - Trung bình</option>
                            <option value="2">2 - Tệ</option>
                            <option value="1">1 - Rất tệ</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="noi_dung" class="form-label"><strong>Nội dung đánh giá</strong></label>
                        <textarea class="form-control" id="noi_dung" name="noi_dung" rows="4" placeholder="Chia sẻ cảm nhận của bạn về sân bóng..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    <a href="<?php echo BASE_URL; ?>user/bookingHistory" class="btn btn-secondary">Quay lại</a>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Không tìm thấy thông tin lịch đặt sân hợp lệ.
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
