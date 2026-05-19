<div class="container my-5">
    <h1 class="mb-4 text-primary">Quản Lý Kho Hàng</h1>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
        <a href="<?= BASE_URL ?>admin/kho-hang/them" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm Sản Phẩm Mới
        </a>
        <?php endif; ?>
    </div>

    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="alert alert-success">
            <?php echo $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger">
            <?php echo $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?>
        </div>
    <?php endif; ?>

    <style>
        .product-card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .product-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }
        .product-img-wrapper {
            position: relative;
            padding-top: 100%; /* 1:1 Aspect Ratio */
            overflow: hidden;
            background: #f8f9fa;
        }
        .product-img-wrapper img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .product-card:hover .product-img-wrapper img {
            transform: scale(1.08);
        }
        .category-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 2;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .price-tag {
            font-size: 1.3rem;
            font-weight: 700;
            color: #e63946;
        }
        .variant-badge {
            font-size: 0.8rem;
            margin: 2px;
            padding: 5px 10px;
            background-color: #f1f3f5;
            color: #495057;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            display: inline-block;
        }
        .variant-badge strong {
            color: #212529;
        }
        .stock-low {
            color: #dc3545;
            font-weight: bold;
        }
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            line-height: 1.4;
            height: 2.8em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px dashed #e9ecef;
        }
        .action-buttons .btn {
            border-radius: 8px;
            font-weight: 500;
        }
    </style>

    <?php if (empty($products)): ?>
        <div class="alert alert-warning shadow-sm rounded-3">
            <i class="fas fa-box-open me-2"></i> Chưa có sản phẩm nào trong kho hàng.
        </div>
    <?php else: ?>
        <div class="mb-3 text-muted fw-bold">
            <i class="fas fa-boxes me-2"></i>Tổng số sản phẩm: <span class="text-primary"><?php echo count($products ?? []); ?></span>
        </div>
        
        <div class="row g-4">
            <?php foreach ($products as $product): ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100">
                        <div class="product-img-wrapper border-bottom">
                            <?php if (!empty($product['category'])): ?>
                                <span class="category-badge"><?= htmlspecialchars($product['category']) ?></span>
                            <?php endif; ?>
                            <img src="<?= BASE_URL . htmlspecialchars($product['image_url'] ?? '') ?>" alt="<?= htmlspecialchars($product['name'] ?? '') ?>" loading="lazy">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-1 text-muted small">ID: #<?= htmlspecialchars($product['id'] ?? '') ?></div>
                            <h5 class="card-title product-title" title="<?= htmlspecialchars($product['name'] ?? '') ?>">
                                <?= htmlspecialchars($product['name'] ?? '') ?>
                            </h5>
                            
                            <div class="price-tag mb-3 mt-2">
                                <?= number_format($product['price'] ?? 0) ?> đ
                            </div>
                            
                            <div class="variants-container mb-2 flex-grow-1">
                                <div class="small text-muted mb-2 fw-bold"><i class="fas fa-tags me-1"></i> Phân loại & Tồn kho:</div>
                                <?php if (!empty($product['variants'])): ?>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($product['variants'] as $variant): ?>
                                            <?php $isLowStock = (int)$variant['stock'] <= 5; ?>
                                            <span class="variant-badge">
                                                <?= htmlspecialchars($variant['size']) ?>: 
                                                <span class="<?= $isLowStock ? 'stock-low' : '' ?>"><?= htmlspecialchars($variant['stock']) ?></span>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary opacity-50">Chưa phân loại</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
                            <div class="action-buttons">
                                <a href="<?= BASE_URL ?>admin/kho-hang/sua?id=<?= htmlspecialchars($product['id'] ?? '') ?>" class="btn btn-outline-primary btn-sm flex-grow-1">
                                    <i class="fas fa-edit"></i> Sửa
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm flex-grow-1" data-bs-toggle="modal" data-bs-target="#deleteProductModal" data-product-id="<?= htmlspecialchars($product['id'] ?? '') ?>" data-product-name="<?= htmlspecialchars($product['name'] ?? '') ?>">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteProductModalLabel">Xác nhận xóa Sản Phẩm</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Bạn có chắc chắn muốn xóa sản phẩm: <strong id="productNameToDelete"></strong>?
        <p class="text-danger mt-2">Hành động này không thể hoàn tác.</p>
      </div>
      <div class="modal-footer">
        <form id="deleteProductForm" method="POST" action="<?= BASE_URL ?>admin/kho-hang/xoa">
            <input type="hidden" name="id" id="productIdToDelete">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteProductModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var productId = button.getAttribute('data-product-id');
        var productName = button.getAttribute('data-product-name');
        
        var modalBodyName = deleteModal.querySelector('#productNameToDelete');
        var modalFormInput = deleteModal.querySelector('#productIdToDelete');
        
        modalBodyName.textContent = productName;
        modalFormInput.value = productId;
    });
});
</script>
