<?php
// Giao diện: checkout/success.php
// Trang này hiển thị thông báo đặt hàng thành công và chi tiết đơn hàng

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Bao gồm header
require_once __DIR__ . '/../layouts/header.php';

// Lấy dữ liệu đơn hàng và các mục hàng từ controller
$order = $data['order'] ?? null;
$orderItems = $data['orderItems'] ?? [];
?>

<div class="container my-5">
    <?php if ($order): ?>
        <div class="card shadow-sm border-success text-center">
            <div class="card-body">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success_message']) ?>
                        <?php unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>
                <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                <h1 class="card-title">Đặt Hàng Thành Công!</h1>
                <p class="card-text">Cảm ơn bạn đã mua hàng. Đơn hàng của bạn đã được ghi nhận.</p>
                <p>Mã đơn hàng của bạn là: <strong>#<?= htmlspecialchars($order['id']) ?></strong></p>
                
                <div class="text-start mt-5">
                    <h4 class="mb-3">Chi Tiết Đơn Hàng</h4>
                    
                    <!-- Thông tin khách hàng -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin giao hàng:</h5>
                            <p><strong>Tên người nhận:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>
                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_sdt']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng:</h5>
                            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                            <p><strong>Trạng thái:</strong> <span class="badge bg-warning text-dark"><?= htmlspecialchars($order['status']) ?></span></p>
                        </div>
                    </div>

                    <!-- Bảng chi tiết sản phẩm -->
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orderItems as $item): ?>
                                <tr>
                                    <td>
                                        <?= htmlspecialchars($item['name']) ?>
                                        <small class="text-muted">(Size: <?= htmlspecialchars($item['size']) ?>)</small>
                                    </td>
                                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    <td><?= number_format($item['price'], 0, ',', '.') ?> VNĐ</td>
                                    <td><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> VNĐ</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="table-light fw-bold">
                                <td colspan="3" class="text-end">Tổng cộng:</td>
                                <td><?= number_format($order['total_amount'], 0, ',', '.') ?> VNĐ</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4">
                    <a href="<?= BASE_URL ?>store" class="btn btn-primary mx-2">Tiếp tục mua sắm</a>
                    <a href="<?= BASE_URL ?>user/invoice-history" class="btn btn-outline-secondary mx-2">Xem lịch sử đơn hàng</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Không tìm thấy đơn hàng!</h4>
            <p>Rất tiếc, chúng tôi không thể tìm thấy thông tin cho đơn hàng này. Vui lòng kiểm tra lại mã đơn hàng hoặc liên hệ bộ phận hỗ trợ.</p>
            <hr>
            <a href="<?= BASE_URL ?>user/invoice-history" class="btn btn-primary">Quay lại lịch sử đơn hàng</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Bao gồm footer
require_once __DIR__ . '/../layouts/footer.php';
?>
