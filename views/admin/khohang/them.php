<div class="container my-5">
    <h1 class="mb-4 text-primary">Thêm Sản Phẩm Mới</h1>

    <div class="card shadow-lg">
        <div class="card-body">
            <form action="<?= BASE_URL ?>admin/kho-hang/them" method="POST" enctype="multipart/form-data">
                
                <!-- Product Details -->
                <div class="mb-3">
                    <label for="name" class="form-label">Tên Sản Phẩm</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Mô Tả</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="price" class="form-label">Giá (VNĐ)</label>
                        <input type="number" class="form-control" id="price" name="price" step="1000" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="category" class="form-label">Loại Sản Phẩm</label>
                        <select class="form-select" id="category" name="category" required>
                            <option value="">-- Chọn loại --</option>
                            <option value="Áo">Áo</option>
                            <option value="Giày">Giày</option>
                            <option value="Phụ kiện">Phụ kiện</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="image" class="form-label">Hình Ảnh</label>
                    <input class="form-control" type="file" id="image" name="image" accept="image/*" required>
                </div>

                <!-- Variants Section -->
                <hr>
                <h5 class="mb-3">Tồn Kho Theo Size</h5>
                <div id="variants-container">
                    <!-- JS will populate this -->
                </div>
                <button type="button" id="add-variant-btn" class="btn btn-outline-primary mt-2">
                    <i class="fas fa-plus"></i> Thêm Size
                </button>
                <hr>

                <!-- Actions -->
                <div class="d-flex justify-content-end">
                    <a href="<?= BASE_URL ?>admin/kho-hang" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Lưu Sản Phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const variantsContainer = document.getElementById('variants-container');
    const addVariantBtn = document.getElementById('add-variant-btn');
    let variantIndex = 0;

    function createVariantRow() {
        const variantRow = document.createElement('div');
        variantRow.classList.add('row', 'align-items-center', 'mb-2', 'variant-row');
        variantRow.innerHTML = `
            <div class="col-md-5">
                <label class="form-label">Size</label>
                <input type="text" class="form-control" name="variants[${variantIndex}][size]" placeholder="VD: M, L, 40, 41..." required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Số Lượng</label>
                <input type="number" class="form-control" name="variants[${variantIndex}][stock]" min="0" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-variant-btn">Xóa</button>
            </div>
        `;
        variantsContainer.appendChild(variantRow);
        variantIndex++;
    }

    addVariantBtn.addEventListener('click', createVariantRow);

    variantsContainer.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-variant-btn')) {
            e.target.closest('.variant-row').remove();
        }
    });

    // Thêm một hàng mặc định
    createVariantRow();
});
</script>
