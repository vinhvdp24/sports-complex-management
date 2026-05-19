<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Thêm Nhân Viên Mới</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['admin_error'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
                    <?php endif; ?>

                    <form action="<?= BASE_URL ?>admin/nhan-vien/xu-ly-them" method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Tên Đăng Nhập</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Mật Khẩu</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Vai Trò</label>
                            <select class="form-select" id="role" name="role">
                                <option value="admin">Admin (Nhân viên)</option>
                                <option value="owner">Owner (Chủ sân)</option>
                            </select>
                            <div class="form-text">Nhân viên chỉ có quyền vận hành, Chủ sân có toàn quyền.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Tạo Tài Khoản</button>
                            <a href="<?= BASE_URL ?>admin/nhan-vien" class="btn btn-outline-secondary">Hủy</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>