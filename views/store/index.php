<?php
$pageTitle = $data['pageTitle'] ?? 'Cửa Hàng';
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    /* Store Page Custom Styles */
    .store-banner {
        background: linear-gradient(135deg, #1f4037, #99f2c8); /* A nice sporty green/dark gradient */
        border-radius: 25px;
        position: relative;
        overflow: hidden;
        margin-top: 20px;
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }
    .store-banner img {
        object-fit: cover;
        opacity: 0.8;
        mix-blend-mode: overlay;
    }
    
    /* Category Sidebar */
    .category-title {
        font-weight: 800;
        background: linear-gradient(45deg, #2c3e50, #3498db);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 1.5rem;
    }
    .custom-list-group .list-group-item {
        border: none;
        border-radius: 12px;
        margin-bottom: 8px;
        padding: 12px 20px;
        font-weight: 500;
        color: #495057;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }
    .custom-list-group .list-group-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .custom-list-group .list-group-item.active {
        background: linear-gradient(45deg, #007bff, #00c6ff);
        color: white;
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
    }

    /* Product Cards */
    .product-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.05);
        transition: all 0.4s ease;
        overflow: hidden;
        background: #fff;
    }
    .product-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .product-img-wrapper {
        height: 220px;
        overflow: hidden;
        background: #f4f6f9;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }
    .product-img-wrapper img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.5s ease;
    }
    .product-card:hover .product-img-wrapper img {
        transform: scale(1.1);
    }
    .product-title {
        font-weight: 700;
        color: #2d3436;
        font-size: 1.15rem;
        margin-bottom: 0.5rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-desc {
        color: #636e72;
        font-size: 0.9rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-price {
        font-weight: 800;
        color: #d63031;
        font-size: 1.3rem;
    }
    .btn-view-product {
        background: #f8f9fa;
        color: #007bff;
        border: 2px solid transparent;
        font-weight: 600;
        border-radius: 25px;
        transition: all 0.3s ease;
    }
    .btn-view-product:hover {
        background: linear-gradient(45deg, #007bff, #00c6ff);
        color: white;
        box-shadow: 0 5px 15px rgba(0,123,255,0.4);
    }
    .page-title {
        font-weight: 800;
        color: #2c3e50;
    }
</style>

<div class="container mb-5">
    <div class="store-banner p-5 text-white d-flex align-items-center justify-content-center text-center mb-5" style="height: 250px;">
        <div style="z-index: 2;">
            <h1 class="display-4 fw-bold mb-2">Cửa Hàng Thể Thao</h1>
            <p class="fs-5 opacity-75">Trang phục, phụ kiện và dụng cụ thể thao chính hãng</p>
        </div>
    </div>

    <div class="row g-5">
        <!-- Cột Danh mục / Bộ lọc -->
        <div class="col-lg-3 col-md-4">
            <h4 class="category-title"><i class="fas fa-list-ul me-2"></i> Danh Mục</h4>
            <div class="list-group custom-list-group shadow-sm">
                <a href="<?php echo BASE_URL; ?>store" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo ($data['currentCategory'] == 'Tất cả sản phẩm') ? 'active' : ''; ?>">
                    Tất cả sản phẩm
                    <i class="fas fa-chevron-right fs-6 opacity-50"></i>
                </a>
                <?php if (!empty($data['categories'])): ?>
                    <?php foreach ($data['categories'] as $category): ?>
                        <a href="<?php echo BASE_URL; ?>store/category/<?php echo urlencode($category); ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo ($data['currentCategory'] == $category) ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category); ?>
                            <i class="fas fa-chevron-right fs-6 opacity-50"></i>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cột Lưới sản phẩm -->
        <div class="col-lg-9 col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="page-title m-0">
                    <?php echo ($data['currentCategory'] == 'Tất cả sản phẩm') ? 'Tất Cả Sản Phẩm' : htmlspecialchars($data['currentCategory']); ?>
                </h2>
                <span class="badge bg-secondary rounded-pill px-3 py-2 fw-medium">
                    <?php echo count($data['products'] ?? []); ?> sản phẩm
                </span>
            </div>

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php if (!empty($data['products'])): ?>
                    <?php foreach ($data['products'] as $product): ?>
                        <div class="col">
                            <div class="card product-card h-100">
                                <div class="product-img-wrapper">
                                    <img src="<?php echo BASE_URL . htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                <div class="card-body d-flex flex-column p-4">
                                    <h5 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                    <p class="product-desc mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                                    
                                    <div class="mt-auto d-flex flex-column gap-3">
                                        <div class="product-price text-center">
                                            <?php echo number_format($product['price'], 0, ',', '.'); ?> <span class="fs-6 text-muted fw-normal">VNĐ</span>
                                        </div>
                                        <a href="<?php echo BASE_URL . 'store/product/' . htmlspecialchars($product['id']); ?>" class="btn btn-view-product w-100 py-2">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 text-center">
                            <i class="fas fa-box-open fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold">Chưa có sản phẩm</h5>
                            <p class="mb-0">Hiện chưa có sản phẩm nào trong danh mục này. Vui lòng quay lại sau.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>