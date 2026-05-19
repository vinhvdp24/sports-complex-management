<div class="container my-5">
    <h1 class="mb-4 text-success">⚽ Thêm Sân Mới</h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg p-4">
                
                <?php 
                // Hiển thị thông báo lỗi (nếu có)
                if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>admin-san-them">
                    
                    <div class="mb-3">
                        <label for="maSan" class="form-label fw-bold">Mã Sân (VD: SB1, SCL1, SPKB1):</label>
                        <input type="text" class="form-control" id="maSan" name="MaSan" placeholder="Nhập mã sân duy nhất" required>
                    </div>

                    <div class="mb-3">
                        <label for="tenSan" class="form-label fw-bold">Tên Sân:</label>
                        <input type="text" class="form-control" id="tenSan" name="TenSan" placeholder="VD: Sân bóng A1" required>
                    </div>

                    <div class="mb-3">
                        <label for="maLoai" class="form-label fw-bold">Loại Sân:</label>
                        <select class="form-select" id="maLoai" name="MaLoai" required>
                            <option value="">-- Chọn loại sân --</option>
                            <?php foreach ($loaiSanList as $loai): ?>
                                <option value="<?= htmlspecialchars($loai['MaLoai']) ?>">
                                    <?= htmlspecialchars($loai['TenLoai']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="giaThue" class="form-label fw-bold">Giá Thuê (VNĐ/Giờ):</label>
                        <input type="number" class="form-control" id="giaThue" name="GiaThue" min="0" step="0.01" placeholder="VD: 200000" required> 
                    </div>

                    <div class="mb-3">
                        <label for="tinhTrang" class="form-label fw-bold">Tình Trạng:</label>
                        <select class="form-select" id="tinhTrang" name="TinhTrang" required>
                            <option value="Hoạt động">Hoạt động</option>
                            <option value="Bảo trì">Bảo trì</option>
                            <option value="Trống">Trống</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="moTa" class="form-label fw-bold">Mô Tả:</label>
                        <textarea class="form-control" id="moTa" name="MoTa" rows="3" placeholder="Nhập mô tả chi tiết về sân..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success btn-lg">Thêm Sân</button>
                        <a href="<?= BASE_URL ?>admin-san" class="btn btn-secondary">Quay lại danh sách</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>