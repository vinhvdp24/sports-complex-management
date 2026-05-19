

<style>
    /* CSS Tùy chỉnh cho 404 */
    .error-container {
        padding: 60px 0;
        text-align: center;
        margin-top: 50px;
    }
    .error-code {
        font-size: 150px;
        font-weight: 700;
        color: #dc3545; /* Màu đỏ của Bootstrap */
        text-shadow: 4px 4px 0 #fff, 6px 6px 0 #ccc;
    }
    .error-message {
        font-size: 24px;
        color: #6c757d; /* Màu xám */
        margin-bottom: 25px;
    }
</style>

<div class="container">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="error-container card shadow-lg p-5">
                
                <div class="error-code">404</div>
                
                <h2 class="mt-4 text-dark">Trang Không Tìm Thấy</h2>
                
                <p class="error-message">
                    Xin lỗi, đường dẫn bạn truy cập không tồn tại hoặc đã bị xóa.
                </p>
                
                <?php 
                    // Tùy chọn: Hiển thị thông báo chi tiết nếu được truyền từ Controller
                    if (isset($message) && !empty($message)) {
                        echo '<p class="text-muted small mb-4">Chi tiết: ' . htmlspecialchars($message) . '</p>';
                    }
                ?>

                <div class="mt-4">
                    <a href="<?= BASE_URL ?>home/index" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-home me-2"></i> Về Trang Chủ
                    </a>
                    
                    <a href="javascript:history.back()" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left me-2"></i> Quay Lại Trang Trước
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

