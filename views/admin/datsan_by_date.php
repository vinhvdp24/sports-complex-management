

<div class="container my-5">
    <h1 class="mb-4">Danh Sách Đặt Sân Theo Ngày</h1>

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

    <!-- Form để chọn ngày -->
    <form method="GET" action="" class="mb-4 p-4 bg-light border rounded">
        <div class="row align-items-end g-3">
            <div class="col-md-5">
                <label for="startDate" class="form-label fw-bold">Từ Ngày</label>
                <input type="date" id="startDate" name="startDate" class="form-control" value="<?= htmlspecialchars($data['startDate']) ?>">
            </div>
            <div class="col-md-5">
                <label for="endDate" class="form-label fw-bold">Đến Ngày</label>
                <input type="date" id="endDate" name="endDate" class="form-control" value="<?= htmlspecialchars($data['endDate']) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Xem</button>
            </div>
        </div>
    </form>

    <h3 class="mt-5">Kết quả từ ngày: <?= htmlspecialchars(date('d/m/Y', strtotime($data['startDate']))) ?> đến ngày: <?= htmlspecialchars(date('d/m/Y', strtotime($data['endDate']))) ?></h3>
    
    <!-- Bảng hiển thị danh sách đặt sân -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Đặt Sân</th>
                            <th>Tên Khách Hàng</th>
                            <th>Tên Sân</th>
                            <th>Ngày Đặt</th>
                            <th>Giờ</th>
                            <th>Tổng Hóa Đơn</th>
                            <th>Trạng Thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($data['danhSachDatSan'])): ?>
                            <?php 
                            date_default_timezone_set('Asia/Ho_Chi_Minh');
                            $thoiGianHienTai = new DateTime();
                            foreach ($data['danhSachDatSan'] as $datSan): 
                                $thoiGianBatDauDatSan = new DateTime($datSan['NgayDat'] . ' ' . $datSan['GioBatDau']);
                                $canCancel = ($thoiGianBatDauDatSan > $thoiGianHienTai && strpos($datSan['TrangThai'], 'Đã hủy') !== 0);
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($datSan['MaDatSan'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($datSan['TenKhachHang'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($datSan['TenSan'] ?? '') ?></td>
                                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($datSan['NgayDat']))) ?></td>
                                    <td><?= htmlspecialchars(date('H:i', strtotime($datSan['GioBatDau']))) ?> - <?= htmlspecialchars(date('H:i', strtotime($datSan['GioKetThuc']))) ?></td>
                                    <td>
                                        <?php
                                            $tongTien = $datSan['TongTienHoaDon'] ?? $datSan['TongTien'];
                                            echo htmlspecialchars(number_format($tongTien, 0, ',', '.')) . ' đ';
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'bg-success';
                                            if (strpos($datSan['TrangThai'], 'Đã hủy') === 0) {
                                                $badgeClass = 'bg-danger';
                                            } else if ($datSan['TrangThai'] === 'Chờ thanh toán') {
                                                $badgeClass = 'bg-warning text-dark';
                                            }
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($datSan['TrangThai']) ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= BASE_URL ?>admin/booking-details?id=<?= $datSan['MaDatSan'] ?>" class="btn btn-info btn-sm mb-1">Xem chi tiết</a>
                                        <?php if ($datSan['TrangThai'] === 'Chờ thanh toán'): ?>
                                            <form method="POST" action="<?= BASE_URL ?>admin/xuLyXacNhanThanhToan" onsubmit="return confirm('Bạn có chắc chắn muốn xác nhận thanh toán cho lịch đặt #<?= $datSan['MaDatSan'] ?> này không?');" style="display: inline-block;">
                                                <input type="hidden" name="MaDatSan" value="<?= $datSan['MaDatSan'] ?>">
                                                <input type="hidden" name="startDate" value="<?= htmlspecialchars($data['startDate']) ?>">
                                                <input type="hidden" name="endDate" value="<?= htmlspecialchars($data['endDate']) ?>">
                                                <button type="submit" class="btn btn-success btn-sm mb-1">Xác nhận thanh toán</button>
                                            </form>
                                        <?php endif; ?>
                                        <?php if ($canCancel): ?>
                                            <form method="POST" action="<?= BASE_URL ?>admin/xuLyHuyDatSan" onsubmit="return confirm('Bạn có chắc chắn muốn hủy lịch đặt #<?= $datSan['MaDatSan'] ?> này không?');" style="display: inline-block;">
                                                <input type="hidden" name="MaDatSan" value="<?= $datSan['MaDatSan'] ?>">
                                                <input type="hidden" name="startDate" value="<?= htmlspecialchars($data['startDate']) ?>">
                                                <input type="hidden" name="endDate" value="<?= htmlspecialchars($data['endDate']) ?>">
                                                <button type="submit" class="btn btn-danger btn-sm mb-1">Hủy</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">Không có lượt đặt sân nào trong khoảng thời gian đã chọn.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
