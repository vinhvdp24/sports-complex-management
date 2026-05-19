<?php
// Giao diện: user/order_details.php

// Bao gồm header
require_once __DIR__ . '/../layouts/header.php';

// Lấy dữ liệu đơn hàng và các mục hàng từ controller
$order = $data['order'] ?? null;
$orderItems = $data['orderItems'] ?? [];
?>

<div class="container my-5">
    <?php if ($order): ?>
        <div class="card shadow-sm">
            <div class="card-header">
                <h2 class="mb-0">Chi Tiết Đơn Hàng: <strong>#<?= htmlspecialchars($order['id']) ?></strong></h2>
            </div>
            <div class="card-body">
                <div class="text-start mt-3">
                    <!-- Thông tin khách hàng -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin giao hàng:</h5>
                            <p><strong>Tên người nhận:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['customer_address']) ?></p>
                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_sdt']) ?></p>
                            <p><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng:</h5>
                            <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                            <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
                            <p><strong>Trạng thái:</strong>
                                <?php 
                                    $status = htmlspecialchars($order['status']);
                                    $badge_class = 'bg-secondary';
                                    if ($status === 'Pending') $badge_class = 'bg-warning text-dark';
                                    elseif ($status === 'Processing') $badge_class = 'bg-info text-dark';
                                    elseif ($status === 'Completed') $badge_class = 'bg-success';
                                    elseif ($status === 'Cancelled') $badge_class = 'bg-danger';
                                ?>
                                <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                            </p>
                        </div>
                    </div>

                    <!-- Bảng chi tiết sản phẩm -->
                    <h4 class="mb-3">Các sản phẩm đã đặt</h4>
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
                                        <img src="<?= BASE_URL . htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" style="width: 50px; height: auto; margin-right: 10px;">
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
                    <a href="<?= BASE_URL ?>user/invoice-history" class="btn btn-outline-secondary mx-2">Quay lại lịch sử đơn hàng</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger" role="alert">
            <h4 class="alert-heading">Không tìm thấy đơn hàng!</h4>
            <p>Rất tiếc, chúng tôi không thể tìm thấy thông tin cho đơn hàng này.</p>
        </div>
    <?php endif; ?>
</div>

<?php
// Bao gồm footer
require_once __DIR__ . '/../layouts/footer.php';
?>
