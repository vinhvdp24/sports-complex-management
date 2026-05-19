<div class="container my-5">
    <h1 class="mb-4 text-primary">Thêm Dịch Vụ Mới</h1>
    
    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="<?php echo BASE_URL; ?>admin/xuLyThemDichVu" enctype="multipart/form-data">
                
                <div class="mb-3">
                    <label for="TenDV" class="form-label fw-bold">Tên Dịch Vụ</label>
                    <input type="text" class="form-control" id="TenDV" name="TenDV" required>
                </div>

                <div class="mb-3">
                    <label for="DonGia" class="form-label fw-bold">Đơn Giá (VNĐ)</label>
                    <input type="number" class="form-control" id="DonGia" name="DonGia" required>
                </div>

                <div class="mb-3">
                    <label for="MoTa" class="form-label fw-bold">Mô Tả</label>
                    <textarea class="form-control" id="MoTa" name="MoTa" rows="3"></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <a href="<?php echo BASE_URL; ?>admin/dichvu" class="btn btn-secondary me-2">Hủy</a>
                    <button type="submit" class="btn btn-primary">Thêm Dịch Vụ</button>
                </div>

            </form>
        </div>
    </div>
</div>

