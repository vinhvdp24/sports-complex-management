<div class="container my-5">
    <h1 class="mb-4 text-warning">🛠️ Sửa Sân: <?= htmlspecialchars($san['TenSan']) ?></h1>
    
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg p-4">
                
                <?php 
                if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="<?= BASE_URL ?>admin-san-capnhat">
                    
                    <div class="mb-3">
                        <label for="maSan" class="form-label fw-bold">Mã Sân:</label>
                        <input type="text" class="form-control bg-light" id="maSan" name="MaSan" value="<?= htmlspecialchars($san['MaSan']) ?>" readonly>
                        <small class="text-muted">Mã sân không thể thay đổi để đảm bảo tính nhất quán của dữ liệu.</small>
                    </div>

                    <div class="mb-3">
                        <label for="tenSan" class="form-label fw-bold">Tên Sân:</label>
                        <input type="text" class="form-control" id="tenSan" name="TenSan" value="<?= htmlspecialchars($san['TenSan']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="maLoai" class="form-label fw-bold">Loại Sân:</label>
                        <select class="form-select" id="maLoai" name="MaLoai" required>
                            <?php foreach ($loaiSanList as $loai): ?>
                                <option value="<?= htmlspecialchars($loai['MaLoai']) ?>" <?= ($san['MaLoai'] == $loai['MaLoai']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($loai['TenLoai']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="giaThue" class="form-label fw-bold">Giá Thuê (VNĐ/Giờ):</label>
                        <input type="number" class="form-control" id="giaThue" name="GiaThue" min="0" step="0.01" value="<?= htmlspecialchars($san['GiaThue']) ?>" required> 
                    </div>
                    
                    <div class="mb-3">
                        <label for="tinhTrang" class="form-label fw-bold">Tình Trạng:</label>
                        <select class="form-select" id="tinhTrang" name="TinhTrang" required>
                            <?php 
                            $tinhTrangHienTai = $san['TinhTrang'];
                            $options = ['Hoạt động', 'Bảo trì', 'Trống'];
                            foreach ($options as $option): 
                            ?>
                                <option value="<?= $option ?>" <?= ($tinhTrangHienTai == $option) ? 'selected' : '' ?>>
                                    <?= $option ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="moTa" class="form-label fw-bold">Mô Tả:</label>
                        <textarea class="form-control" id="moTa" name="MoTa" rows="3"><?= htmlspecialchars($san['MoTa']) ?></textarea>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-warning btn-lg">Lưu Thay Đổi</button>
                        <a href="<?= BASE_URL ?>admin-san" class="btn btn-secondary">Hủy và Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>