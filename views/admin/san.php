<div class="container my-5">
    <h1 class="mb-4 text-primary">Danh Sách Sân - Sân Vận Động</h1>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
        <a href="<?= BASE_URL ?>form-them-san" class="btn btn-success">
            <i class="fas fa-plus"></i> Thêm Sân Mới
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

    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title">Danh Sách (Tổng: <?php echo count($sanBongList ?? []); ?>)</h5>

            <?php if (empty($sanBongList)): ?>
                <div class="alert alert-warning">
                    Chưa có sân nào được thêm vào hệ thống.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Mã Sân</th>
                                <th>Tên Sân</th>
                                <th>Loại Sân</th>
                                <th>Giá Thuê/Giờ</th>
                                <th>Tình Trạng</th>
                                <th>Mô Tả</th>
                                <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
                                <th>Thao Tác</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sanBongList as $san): ?>
                                <tr>
                                    <td><?= htmlspecialchars($san['MaSan'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($san['TenSan'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($san['TenLoai'] ?? '') ?></td>
                                    <td><?= number_format($san['GiaThue'] ?? 0) ?> VNĐ</td>
                                    <td>
                                        <?php 
                                            // Highlight tình trạng
                                            $badgeClass = ($san['TinhTrang'] == 'Hoạt động') ? 'bg-success' : 'bg-danger';
                                            if ($san['TinhTrang'] == 'Trống') $badgeClass = 'bg-info';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($san['TinhTrang'] ?? 'Không rõ') ?></span>
                                    </td>
                                    <td><?= htmlspecialchars(substr($san['MoTa'] ?? '', 0, 50)) ?>...</td>
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
                                    <td>
                                        <a 
                                            href="<?= BASE_URL ?>admin-san-sua?MaSan=<?= htmlspecialchars($san['MaSan'] ?? '') ?>" 
                                            class="btn btn-sm btn-warning me-2"
                                        >
                                            Sửa
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSanModal" data-masan="<?= htmlspecialchars($san['MaSan'] ?? '') ?>" data-tensan="<?= htmlspecialchars($san['TenSan'] ?? '') ?>">
                                            Xóa
                                        </button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteSanModal" tabindex="-1" aria-labelledby="deleteSanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteSanModalLabel">Xác nhận xóa Sân</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Bạn có chắc chắn muốn xóa sân: <strong id="sanNameToDelete"></strong>?
        <p class="text-danger mt-2">Hành động này không thể hoàn tác và có thể ảnh hưởng đến các lịch đặt sân liên quan.</p>
      </div>
      <div class="modal-footer">
        <form id="deleteSanForm" method="POST" action="<?= BASE_URL ?>admin-san-xoa">
            <input type="hidden" name="MaSan" id="maSanToDelete">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var deleteModal = document.getElementById('deleteSanModal');
    deleteModal.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var maSan = button.getAttribute('data-masan');
        var tenSan = button.getAttribute('data-tensan');
        
        var modalBodyName = deleteModal.querySelector('#sanNameToDelete');
        var modalFormInput = deleteModal.querySelector('#maSanToDelete');
        
        modalBodyName.textContent = tenSan;
        modalFormInput.value = maSan;
    });
});
</script>
