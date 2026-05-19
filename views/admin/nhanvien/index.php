<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>👥 Quản Lý Nhân Viên</h1>
        <a href="<?= BASE_URL ?>admin/nhan-vien/them" class="btn btn-primary">+ Thêm Nhân Viên</a>
    </div>

    <?php if (isset($_SESSION['admin_error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['admin_error']; unset($_SESSION['admin_error']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['admin_success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['admin_success']; unset($_SESSION['admin_success']); ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Tên Đăng Nhập</th>
                        <th>Vai Trò</th>
                        <th class="text-end">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['staffList'] as $staff): ?>
                    <tr>
                        <td><?= $staff['MaTK'] ?></td>
                        <td><strong><?= htmlspecialchars($staff['TenDangNhap']) ?></strong></td>
                        <td>
                            <span class="badge <?= $staff['LoaiTK'] === 'owner' ? 'bg-primary' : 'bg-info text-dark' ?>">
                                <?= strtoupper($staff['LoaiTK']) ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <?php if ($staff['TenDangNhap'] !== $_SESSION['username']): ?>
                            <form action="<?= BASE_URL ?>admin/nhan-vien/xoa" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?');" style="display:inline;">
                                <input type="hidden" name="maTK" value="<?= $staff['MaTK'] ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted small">Đang đăng nhập</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        <a href="<?= BASE_URL ?>admin/dashboard" class="btn btn-secondary">Quay lại Dashboard</a>
    </div>
</div>