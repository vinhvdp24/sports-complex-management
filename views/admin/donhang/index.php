<?php
// Lấy các thông báo nếu có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$success_message = $_SESSION['admin_success'] ?? null;
$error_message = $_SESSION['admin_error'] ?? null;
unset($_SESSION['admin_success'], $_SESSION['admin_error']);

// Mảng định nghĩa màu sắc cho các trạng thái
$status_colors = [
    'Chờ xử lý' => 'warning',
    'Paid' => 'success',
    'Chuẩn bị hàng' => 'info',
    'Đã bàn giao cho đơn vị vận chuyển' => 'primary',
    'Đã giao hàng' => 'success',
    'Đã hủy' => 'danger'
];
$allowed_statuses = ['Chờ xử lý', 'Paid', 'Chuẩn bị hàng', 'Đã bàn giao cho đơn vị vận chuyển', 'Đã giao hàng', 'Đã hủy'];
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= htmlspecialchars($title ?? 'Quản Lý Đơn Hàng') ?></h1>

    <!-- Hiển thị thông báo -->
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách đơn hàng</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã ĐH</th>
                            <th>Tên Khách Hàng</th>
                            <th>SĐT</th>
                            <th>Email</th>
                            <th>Địa chỉ</th>
                            <th>Tổng Tiền</th>
                            <th>Ngày Đặt</th>
                            <th>Trạng Thái Thanh Toán</th>
                            <th>Trạng Thái Đơn Hàng</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="10" class="text-center">Chưa có đơn hàng nào.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= htmlspecialchars($order['id']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_name']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_sdt']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_email']) ?></td>
                                    <td><?= htmlspecialchars($order['customer_address']) ?></td>
                                    <td><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</td>
                                    <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($order['created_at']))) ?></td>
                                    <td>
                                        <?php 
                                            $payMethod = $order['payment_method'] ?? '';
                                            $currStatus = $order['status'] ?? '';
                                            $isMoMo = ($payMethod === 'MoMo' || strpos(strtolower($payMethod), 'momo') !== false);
                                            
                                            if ($isMoMo) {
                                                if ($currStatus === 'Paid' || strpos(strtolower($currStatus), 'paid') !== false) {
                                                    echo '<span class="badge bg-success text-white px-2 py-1 fs-6"><i class="fas fa-check-circle me-1"></i>Đã thanh toán</span>';
                                                } else if ($currStatus === 'Đã hủy' || strpos(strtolower($currStatus), 'hủy') !== false) {
                                                    echo '<span class="badge bg-danger text-white px-2 py-1 fs-6"><i class="fas fa-times-circle me-1"></i>Đã hủy</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark px-2 py-1 fs-6"><i class="fas fa-clock me-1"></i>Chưa thanh toán</span>';
                                                }
                                            } else {
                                                if ($currStatus === 'Đã giao hàng') {
                                                    echo '<span class="badge bg-success text-white px-2 py-1 fs-6"><i class="fas fa-check-circle me-1"></i>Đã thanh toán</span>';
                                                } else if ($currStatus === 'Đã hủy' || strpos(strtolower($currStatus), 'hủy') !== false) {
                                                    echo '<span class="badge bg-danger text-white px-2 py-1 fs-6"><i class="fas fa-times-circle me-1"></i>Đã hủy</span>';
                                                } else {
                                                    echo '<span class="badge bg-info text-dark px-2 py-1 fs-6"><i class="fas fa-money-bill-wave me-1"></i>Thanh toán khi nhận</span>';
                                                }
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $currStatus = $order['status'];
                                            $badgeClass = $status_colors[$currStatus] ?? 'secondary';
                                            $textColor = in_array($badgeClass, ['warning', 'info', 'light']) ? 'text-dark' : 'text-white';
                                        ?>
                                        <span class="badge bg-<?= $badgeClass ?> <?= $textColor ?> px-2 py-1 fs-6">
                                            <?= htmlspecialchars($currStatus) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="<?= BASE_URL ?>admin/don-hang/cap-nhat" method="POST" class="d-flex">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" class="form-control form-control-sm mr-2" style="width: 130px;">
                                                <?php foreach ($allowed_statuses as $status): ?>
                                                    <option value="<?= $status ?>" <?= $currStatus === $status ? 'selected' : '' ?>>
                                                        <?= $status ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-primary">Lưu</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
