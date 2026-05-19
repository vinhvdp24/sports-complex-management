<?php
$pageTitle = $data['pageTitle'] ?? 'Chi tiết sản phẩm';
require_once __DIR__ . '/../layouts/header.php';

$product = $data['product'];
$variants = $data['variants'];

if (!$product) {
    echo '<div class="container my-5"><div class="alert alert-warning text-center rounded-4 shadow-sm py-5"><i class="fas fa-exclamation-triangle fs-1 mb-3 text-warning d-block"></i><h4>Sản phẩm không tìm thấy</h4><a href="'.BASE_URL.'store" class="btn btn-primary mt-3">Quay lại cửa hàng</a></div></div>';
    require_once __DIR__ . '/../layouts/footer.php';
    exit();
}
?>

<style>
    .product-detail-wrapper {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        padding: 40px;
        margin-top: 10px;
    }
    .product-img-box {
        background: #f8f9fa;
        border-radius: 20px;
        padding: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        min-height: 400px;
        transition: all 0.3s ease;
    }
    .product-img-box:hover {
        box-shadow: inset 0 0 20px rgba(0,0,0,0.05);
    }
    .product-img-box img {
        max-height: 400px;
        object-fit: contain;
        transition: transform 0.5s ease;
    }
    .product-img-box:hover img {
        transform: scale(1.05);
    }
    .category-badge {
        background: rgba(0, 123, 255, 0.1);
        color: #007bff;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 30px;
        display: inline-block;
        margin-bottom: 15px;
    }
    .product-title {
        font-weight: 800;
        color: #2c3e50;
        font-size: 2.2rem;
        line-height: 1.2;
        margin-bottom: 20px;
    }
    .product-price {
        font-size: 2rem;
        font-weight: 800;
        background: linear-gradient(45deg, #ff0844, #ffb199);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 30px;
    }
    .product-description {
        color: #636e72;
        font-size: 1.05rem;
        line-height: 1.6;
        margin-bottom: 30px;
    }
    .form-label {
        font-weight: 600;
        color: #2d3436;
    }
    .custom-select, .custom-input {
        border-radius: 12px;
        padding: 12px 15px;
        border: 2px solid #e9ecef;
        box-shadow: none !important;
        transition: border-color 0.3s ease;
    }
    .custom-select:focus, .custom-input:focus {
        border-color: #007bff;
    }
    .btn-add-cart {
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        border: none;
        border-radius: 15px;
        padding: 15px 30px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 114, 255, 0.3);
    }
    .btn-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 114, 255, 0.4);
    }
    .btn-add-cart:disabled {
        background: #bdc3c7;
        box-shadow: none;
        transform: none;
    }
    .back-link {
        display: inline-flex;
        align-items: center;
        color: #636e72;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 20px;
        transition: color 0.2s ease;
    }
    .back-link:hover {
        color: #007bff;
    }
</style>

<div class="container my-5">
    <a href="<?php echo BASE_URL; ?>store" class="back-link"><i class="fas fa-arrow-left me-2"></i> Quay lại cửa hàng</a>
    
    <div class="product-detail-wrapper">
        <div class="row g-5">
            <!-- Product Image -->
            <div class="col-md-6">
                <div class="product-img-box">
                    <img src="<?php echo BASE_URL . htmlspecialchars($product['image_url']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="col-md-6 d-flex flex-column">
                <div>
                    <span class="category-badge"><i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($product['category']); ?></span>
                    <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="product-price"><?php echo number_format($product['price'], 0, ',', '.'); ?> <span class="fs-4 text-muted fw-normal" style="-webkit-text-fill-color: initial;">VNĐ</span></div>
                    <p class="product-description"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <div id="cart-notification" class="mb-3"></div>

                <form id="add-to-cart-form" action="<?php echo BASE_URL; ?>cart/add" method="POST" class="mt-auto bg-light p-4 rounded-4 border border-light">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                    
                    <div class="row g-3 mb-4">
                        <div class="col-sm-8">
                            <label for="variant_id" class="form-label"><i class="fas fa-ruler-combined me-1 text-primary"></i> Chọn Kích Thước (Size)</label>
                            <select class="form-select custom-select" id="variant_id" name="variant_id" required>
                                <?php if (!empty($variants)): ?>
                                    <?php foreach ($variants as $variant): ?>
                                        <option value="<?php echo htmlspecialchars($variant['id']); ?>" <?php echo ($variant['stock'] <= 0) ? 'disabled' : ''; ?>>
                                            Size <?php echo htmlspecialchars($variant['size']); ?> 
                                            <?php if($variant['stock'] > 0): ?>
                                                - Còn lại: <?php echo htmlspecialchars($variant['stock']); ?> SP
                                            <?php else: ?>
                                                - Hết hàng
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="">Không có size nào</option>
                                <?php endif; ?>
                            </select>
                        </div>
                        
                        <div class="col-sm-4">
                            <label for="quantity" class="form-label"><i class="fas fa-sort-numeric-up me-1 text-primary"></i> Số lượng</label>
                            <input type="number" class="form-control custom-input text-center" id="quantity" name="quantity" value="1" min="1" max="100" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-add-cart text-white">
                        <i class="fas fa-cart-plus me-2 fs-5"></i> THÊM VÀO GIỎ HÀNG
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-to-cart-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = e.target;
    const formData = new FormData(form);
    const notificationDiv = document.getElementById('cart-notification');
    const submitBtn = form.querySelector('button[type="submit"]');

    // Thêm trạng thái đang tải
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> ĐANG XỬ LÝ...';
    submitBtn.disabled = true;

    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            notificationDiv.innerHTML = `<div class="alert alert-success border-0 shadow-sm rounded-3"><i class="fas fa-check-circle me-2"></i>${data.message}</div>`;
            if (typeof updateCartCount === 'function') {
                updateCartCount(); // Cập nhật số lượng trên icon giỏ hàng
            }
            if (data.variantId !== undefined && data.newStock !== undefined) {
                updateStockDisplay(data.variantId, data.newStock);
            }
        } else {
            notificationDiv.innerHTML = `<div class="alert alert-danger border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-circle me-2"></i>${data.message}</div>`;
        }
        
        // Khôi phục trạng thái nút
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;

        // Tự động ẩn thông báo sau 3 giây
        setTimeout(() => {
            notificationDiv.innerHTML = '';
        }, 3000);
    })
    .catch(error => {
        console.error('Error:', error);
        notificationDiv.innerHTML = `<div class="alert alert-danger border-0 shadow-sm rounded-3"><i class="fas fa-exclamation-triangle me-2"></i>Có lỗi xảy ra, vui lòng thử lại.</div>`;
        submitBtn.innerHTML = originalBtnText;
        submitBtn.disabled = false;
    });
});

function updateStockDisplay(variantId, newStock) {
    const optionElement = document.querySelector(`#variant_id option[value="${variantId}"]`);
    if (!optionElement) return;

    const sizeText = optionElement.textContent.split('-')[0].trim();

    if (newStock > 0) {
        optionElement.textContent = `${sizeText} - Còn lại: ${newStock} SP`;
    } else {
        optionElement.textContent = `${sizeText} - Hết hàng`;
        optionElement.disabled = true;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
