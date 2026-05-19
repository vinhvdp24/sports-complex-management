<?php
$pageTitle = $data['pageTitle'] ?? 'Giỏ Hàng';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .cart-wrapper {
        background: #f8f9fa;
        min-height: 80vh;
        padding-top: 40px;
        padding-bottom: 60px;
    }
    .cart-title {
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }
    .cart-title::after {
        content: '';
        position: absolute;
        width: 60%;
        height: 4px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        bottom: -10px;
        left: 20%;
        border-radius: 2px;
    }
    .cart-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .table-cart {
        margin-bottom: 0;
    }
    .table-cart thead th {
        background: #f1f2f6;
        border-bottom: none;
        color: #636e72;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        padding: 15px;
    }
    .table-cart tbody td {
        vertical-align: middle;
        padding: 20px 15px;
        border-bottom: 1px solid #f1f2f6;
    }
    .product-img-box {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: #f4f6f9;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        margin-right: 15px;
    }
    .product-img-box img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }
    .product-name {
        font-weight: 700;
        color: #2d3436;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    .quantity-wrapper {
        background: #f1f2f6;
        border-radius: 30px;
        padding: 5px;
        display: inline-flex;
        align-items: center;
    }
    .quantity-input {
        background: transparent;
        border: none;
        text-align: center;
        width: 40px;
        font-weight: 600;
        color: #2d3436;
    }
    .quantity-input:focus {
        outline: none;
        box-shadow: none;
    }
    /* Hide arrows */
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .quantity-input[type=number] {
        -moz-appearance: textfield;
    }
    
    .btn-update-icon {
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s;
    }
    .btn-update-icon:hover {
        background: #0056b3;
        transform: scale(1.1);
    }
    .btn-remove-icon {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
        border: none;
        border-radius: 10px;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-remove-icon:hover {
        background: #dc3545;
        color: white;
        transform: scale(1.1);
    }
    .item-price {
        font-weight: 600;
        color: #636e72;
    }
    .item-subtotal {
        font-weight: 800;
        color: #d63031;
        font-size: 1.1rem;
    }
    .summary-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 30px;
        margin-top: 0;
    }
    .summary-title {
        font-weight: 800;
        color: #2d3436;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px dashed #dfe4ea;
    }
    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    .summary-total {
        font-size: 1.5rem;
        font-weight: 800;
        background: linear-gradient(45deg, #ff0844, #ffb199);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .btn-checkout {
        background: linear-gradient(45deg, #00b09b, #96c93d);
        border: none;
        border-radius: 15px;
        padding: 15px;
        font-size: 1.15rem;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(0, 176, 155, 0.3);
        transition: all 0.3s ease;
        display: block;
        text-align: center;
        color: white;
        text-decoration: none;
    }
    .btn-checkout:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(0, 176, 155, 0.4);
        color: white;
    }
    .back-to-shop {
        color: #636e72;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: color 0.2s;
    }
    .back-to-shop:hover {
        color: #007bff;
    }
</style>

<div class="cart-wrapper">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-5">
            <h1 class="cart-title m-0"><?php echo htmlspecialchars($pageTitle); ?></h1>
            <a href="<?php echo BASE_URL; ?>store" class="back-to-shop"><i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm</a>
        </div>

        <div id="cart-notification" class="mb-3"></div>

        <?php if (!empty($data['cartItems'])): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-card">
                        <div class="table-responsive">
                            <table class="table table-cart align-middle" id="cart-table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">Kích cỡ</th>
                                        <th class="text-center">Đơn giá</th>
                                        <th class="text-center">Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                        <th class="text-center">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['cartItems'] as $cartItemId => $item): ?>
                                        <tr data-item-id="<?php echo htmlspecialchars($cartItemId); ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="product-img-box">
                                                        <img src="<?php echo BASE_URL . htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                    </div>
                                                    <div>
                                                        <div class="product-name"><?php echo htmlspecialchars($item['name']); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center fw-bold text-muted">
                                                Size <?php echo htmlspecialchars($item['size']); ?>
                                            </td>
                                            <td class="text-center item-price">
                                                <?php echo number_format($item['price'], 0, ',', '.'); ?> đ
                                            </td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center align-items-center gap-2">
                                                    <div class="quantity-wrapper">
                                                        <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" max="<?php echo htmlspecialchars($item['max_quantity']); ?>" class="quantity-input" data-item-id="<?php echo htmlspecialchars($cartItemId); ?>">
                                                    </div>
                                                    <button type="button" class="btn-update-icon update-cart-btn" data-item-id="<?php echo htmlspecialchars($cartItemId); ?>" title="Cập nhật số lượng">
                                                        <i class="fas fa-sync-alt" style="pointer-events: none;"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="text-end item-subtotal">
                                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> đ
                                            </td>
                                            <td class="text-center">
                                                <button class="btn-remove-icon remove-cart-btn mx-auto" data-item-id="<?php echo htmlspecialchars($cartItemId); ?>" title="Xóa sản phẩm">
                                                    <i class="fas fa-trash-alt" style="pointer-events: none;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="summary-card sticky-top" style="top: 100px;">
                        <h4 class="summary-title">Tổng Quan Đơn Hàng</h4>
                        
                        <div class="summary-row">
                            <span class="text-muted">Tổng số sản phẩm:</span>
                            <span class="fw-bold" id="cart-total-quantity"><?php echo htmlspecialchars($data['totalQuantity']); ?></span>
                        </div>
                        
                        <div class="summary-row align-items-center mt-4">
                            <span class="fw-bold fs-5">Tạm tính:</span>
                            <span class="summary-total" id="cart-total-price"><?php echo number_format($data['totalPrice'], 0, ',', '.'); ?> VNĐ</span>
                        </div>
                        
                        <hr class="my-4">
                        
                        <a href="<?php echo BASE_URL; ?>checkout" class="btn-checkout w-100">
                            Tiến Hành Thanh Toán <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                        
                        <div class="text-center mt-3">
                            <small class="text-muted"><i class="fas fa-shield-alt text-success me-1"></i> Thanh toán an toàn và bảo mật</small>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="cart-card p-5 text-center my-5">
                <i class="fas fa-shopping-cart text-muted opacity-25 mb-4" style="font-size: 5rem;"></i>
                <h3 class="fw-bold mb-3">Giỏ hàng của bạn đang trống</h3>
                <p class="text-muted mb-4">Có vẻ như bạn chưa chọn sản phẩm nào. Hãy khám phá các sản phẩm thể thao của chúng tôi nhé!</p>
                <a href="<?php echo BASE_URL; ?>store" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">Bắt đầu mua sắm</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cartTable = document.getElementById('cart-table');
    const notificationDiv = document.getElementById('cart-notification');

    if (cartTable) {
        cartTable.addEventListener('click', function(e) {
            const target = e.target;

            // Xử lý nút Cập nhật
            if (target.classList.contains('update-cart-btn')) {
                e.preventDefault(); // Chặn hành vi mặc định tuyệt đối
                const row = target.closest('tr'); // Lấy dòng chứa nút bấm
                const itemId = target.dataset.itemId;
                handleUpdate(row, itemId);
            } 
            // Xử lý nút Xóa
            else if (target.classList.contains('remove-cart-btn')) {
                e.preventDefault();
                const row = target.closest('tr');
                const itemId = target.dataset.itemId;
                handleRemove(row, itemId);
            }
        });
    }

    function handleUpdate(row, cartItemId) {
        const quantityInput = row.querySelector('.quantity-input');
        const newQuantity = parseInt(quantityInput.value);

        // Hiệu ứng UX: Mờ dòng đi chút xíu để biết đang xử lý
        row.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('cart_item_id', cartItemId);
        formData.append('quantity', newQuantity);
        
        fetch(`<?= BASE_URL ?>cart/update`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text()) // Lấy text trước để debug lỗi JSON
        .then(text => {
            let data;
            try {
                data = JSON.parse(text); // Thử parse JSON
            } catch (e) {
                console.error("Server Response Error:", text);
                throw new Error("Server trả về dữ liệu lỗi (không phải JSON). Xem Console để biết chi tiết.");
            }

            row.style.opacity = '1'; 
            
            showNotification(data.message, data.success);
            
            if (data.success) {
                // CẬP NHẬT GIAO DIỆN NGAY LẬP TỨC
                const subtotalCell = row.querySelector('.item-subtotal');
                if (subtotalCell) {
                    subtotalCell.textContent = data.itemSubtotal + ' đ';
                    subtotalCell.style.backgroundColor = '#ffffcc';
                    setTimeout(() => subtotalCell.style.backgroundColor = '', 500);
                }
                updateTotals(data.totalQuantity, data.totalPrice);
                if (typeof updateCartCount === 'function') updateCartCount();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            row.style.opacity = '1';
            console.error('AJAX Error:', error);
            alert('Lỗi: ' + error.message);
        });
    }

    function handleRemove(row, cartItemId) {
        if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

        const formData = new FormData();
        formData.append('cart_item_id', cartItemId);

        fetch(`<?= BASE_URL ?>cart/remove`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success);
            if (data.success) {
                row.remove(); // Xóa dòng khỏi bảng ngay lập tức
                updateTotals(data.totalQuantity, data.totalPrice);
                if (typeof updateCartCount === 'function') updateCartCount();
                
                // Nếu giỏ hàng trống thì reload để hiện thông báo trống
                 if (data.totalQuantity === 0) {
                    location.reload();
                }
            }
        })
        .catch(console.error);
    }

    function updateTotals(totalQuantity, totalPrice) {
        const qtyEl = document.getElementById('cart-total-quantity');
        const priceEl = document.getElementById('cart-total-price');
        
        if (qtyEl) qtyEl.textContent = totalQuantity;
        if (priceEl) priceEl.innerHTML = totalPrice + ' VNĐ';
    }

    function showNotification(message, isSuccess) {
        const alertClass = isSuccess ? 'alert-success border-0 shadow-sm rounded-3' : 'alert-danger border-0 shadow-sm rounded-3';
        const icon = isSuccess ? '<i class="fas fa-check-circle me-2"></i>' : '<i class="fas fa-exclamation-circle me-2"></i>';
        if (notificationDiv) {
            notificationDiv.innerHTML = `<div class="alert ${alertClass} alert-dismissible fade show">${icon}${message}</div>`;
            // Tự tắt sau 3 giây
            setTimeout(() => {
                notificationDiv.innerHTML = '';
            }, 3000);
        }
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
