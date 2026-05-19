<?php
// Giao diện: checkout/index.php
// Trang này hiển thị thông tin thanh toán cho khách hàng

// Bắt đầu session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Bao gồm header
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .checkout-wrapper {
        background: #f8f9fa;
        min-height: 100vh;
        padding-top: 30px;
        padding-bottom: 50px;
    }
    .checkout-title {
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }
    .checkout-title::after {
        content: '';
        position: absolute;
        width: 50%;
        height: 4px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        bottom: -10px;
        left: 25%;
        border-radius: 2px;
    }
    .custom-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        margin-bottom: 30px;
    }
    .card-header-custom {
        background: transparent;
        border-bottom: 1px solid #f1f2f6;
        padding: 20px 25px;
    }
    .card-header-custom h4 {
        margin: 0;
        font-weight: 700;
        color: #2d3436;
        font-size: 1.25rem;
    }
    .info-label {
        color: #636e72;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 5px;
    }
    .info-value {
        background: #f1f2f6;
        border-radius: 12px;
        padding: 12px 15px;
        color: #2d3436;
        font-weight: 500;
        margin-bottom: 15px;
    }
    .btn-edit-info {
        border-radius: 10px;
        font-weight: 600;
        color: #007bff;
        background: rgba(0,123,255,0.1);
        border: none;
        padding: 10px 20px;
        transition: all 0.3s ease;
    }
    .btn-edit-info:hover {
        background: #007bff;
        color: white;
    }
    .order-item {
        border-bottom: 1px dashed #dfe4ea;
        padding: 15px 0;
    }
    .order-item:last-child {
        border-bottom: none;
    }
    .order-item-title {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 3px;
    }
    .order-total-row {
        background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        border-radius: 15px;
        padding: 20px;
        margin-top: 20px;
    }
    .order-total-price {
        font-size: 1.5rem;
        font-weight: 800;
        color: #d63031;
    }
    .payment-method-box {
        border: 2px solid #f1f2f6;
        border-radius: 15px;
        padding: 15px 20px;
        margin-bottom: 15px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .payment-method-box:hover {
        border-color: #00c6ff;
        background: rgba(0, 198, 255, 0.05);
    }
    .form-check-input:checked + .form-check-label {
        font-weight: 700;
        color: #007bff;
    }
    .btn-place-order {
        background: linear-gradient(45deg, #00b09b, #96c93d);
        border: none;
        border-radius: 15px;
        padding: 15px;
        font-size: 1.15rem;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(0, 176, 155, 0.3);
        transition: all 0.3s ease;
    }
    .btn-place-order:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(0, 176, 155, 0.4);
    }
    .btn-place-order:disabled {
        background: #bdc3c7;
        box-shadow: none;
    }
</style>

<div class="checkout-wrapper">
    <div class="container">
        <div class="text-center">
            <h1 class="checkout-title">Thanh Toán Đơn Hàng</h1>
        </div>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
                <i class="fas fa-exclamation-circle me-2"></i> <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Cột thông tin khách hàng -->
            <div class="col-lg-5">
                <div class="custom-card">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-map-marker-alt text-primary me-2"></i>Thông Tin Giao Hàng</h4>
                        <a href="<?= BASE_URL ?>user/indexUser" class="btn btn-edit-info btn-sm"><i class="fas fa-edit me-1"></i>Sửa</a>
                    </div>
                    <div class="card-body p-4">
                        <?php if (isset($data['customer']) && $data['customer']): ?>
                            <div class="info-label">Họ và tên</div>
                            <div class="info-value"><i class="fas fa-user text-muted me-2"></i><?= htmlspecialchars($data['customer']['HoTen']) ?></div>
                            
                            <div class="info-label">Email</div>
                            <div class="info-value"><i class="fas fa-envelope text-muted me-2"></i><?= htmlspecialchars($data['customer']['Email']) ?></div>
                            
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value"><i class="fas fa-phone-alt text-muted me-2"></i><?= htmlspecialchars($data['customer']['SDT']) ?></div>
                            
                            <div class="info-label">Địa chỉ giao hàng</div>
                            <div class="info-value"><i class="fas fa-home text-muted me-2"></i><?= htmlspecialchars($data['customer']['DiaChi']) ?></div>
                        <?php else: ?>
                            <div class="alert alert-warning border-0 rounded-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>Không tìm thấy thông tin khách hàng. Vui lòng cập nhật hồ sơ của bạn.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Cột giỏ hàng và thanh toán -->
            <div class="col-lg-7">
                <div class="custom-card">
                    <div class="card-header-custom">
                        <h4><i class="fas fa-shopping-bag text-success me-2"></i>Đơn Hàng Của Bạn</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="order-items-list mb-4">
                            <?php
                            $totalAmount = 0;
                            if (isset($data['cart']) && !empty($data['cart'])):
                                foreach ($data['cart'] as $item):
                                    $subtotal = $item['price'] * $item['quantity'];
                                    $totalAmount += $subtotal;
                            ?>
                                    <div class="order-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="order-item-title"><?= htmlspecialchars($item['name']) ?></div>
                                            <span class="badge bg-secondary opacity-75 fw-normal me-2">Size: <?= htmlspecialchars($item['size']) ?></span>
                                            <span class="text-muted small">x <?= htmlspecialchars($item['quantity']) ?></span>
                                        </div>
                                        <div class="fw-bold text-dark"><?= number_format($subtotal, 0, ',', '.') ?> đ</div>
                                    </div>
                            <?php
                                endforeach;
                            else:
                            ?>
                                <p class="text-center text-muted my-3">Giỏ hàng của bạn đang trống.</p>
                            <?php endif; ?>
                        </div>

                        <div class="order-total-row d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-dark">Tổng cộng:</span>
                            <span class="order-total-price"><?= number_format($totalAmount, 0, ',', '.') ?> VNĐ</span>
                        </div>

                        <form action="<?= BASE_URL ?>checkout/placeOrder" method="POST" class="mt-4 pt-3 border-top">
                            <h5 class="mb-3 fw-bold"><i class="fas fa-wallet text-warning me-2"></i>Phương Thức Thanh Toán</h5>
                            
                            <div class="payment-method-box">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="Thanh toán khi nhận hàng" checked>
                                    <label class="form-check-label w-100" for="payment_cash" style="cursor:pointer;">
                                        <i class="fas fa-money-bill-wave text-success me-2"></i>Thanh toán khi nhận hàng (COD)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="payment-method-box mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_momo" value="MoMo">
                                    <label class="form-check-label w-100" for="payment_momo" style="cursor:pointer;">
                                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" width="22" height="22" class="me-2" style="border-radius:4px;">Thanh toán qua Ví MoMo
                                    </label>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 btn-place-order text-white" <?= (empty($data['cart']) || !$data['customer']) ? 'disabled' : '' ?>>
                                ĐẶT HÀNG NGAY <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Bao gồm footer
require_once __DIR__ . '/../layouts/footer.php';
?>
