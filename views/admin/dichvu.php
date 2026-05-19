<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/QuanLySVD/');
}

// Lấy và xóa thông báo để chúng không xuất hiện lại
$error = $_SESSION['admin_error'] ?? $_SESSION['error_message'] ?? null;
$success = $_SESSION['admin_success'] ?? $_SESSION['success_message'] ?? null;
unset($_SESSION['admin_error'], $_SESSION['error_message']);
unset($_SESSION['admin_success'], $_SESSION['success_message']);

$dichVuList = $data['dichVuList'] ?? [];
$tonKhoDichVu = $data['tonKhoDichVu'] ?? [];
?>

<div class="container my-5">
    <h1 class="text-primary mb-4">Quản Lý Dịch Vụ</h1>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="list-service-tab" data-bs-toggle="tab" data-bs-target="#list-service" type="button" role="tab" aria-controls="list-service" aria-selected="true">
                <i class="fas fa-list me-2"></i>Danh Sách Dịch Vụ
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="import-stock-tab" data-bs-toggle="tab" data-bs-target="#import-stock" type="button" role="tab" aria-controls="import-stock" aria-selected="false">
                <i class="fas fa-dolly-flatbed me-2"></i>Nhập Hàng & Tồn Kho
            </button>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content" id="myTabContent">
        <!-- Tab 1: Service List -->
        <div class="tab-pane fade show active" id="list-service" role="tabpanel" aria-labelledby="list-service-tab">
            <div class="card shadow-lg mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <h5 class="card-title">Danh Sách Dịch Vụ (Tổng: <?php echo count($dichVuList); ?>)</h5>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
                        <a href="<?php echo BASE_URL; ?>admin/hienThiThemDichVu" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>Thêm Dịch Vụ Mới
                        </a>
                        <?php endif; ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Mã DV</th>
                                    <th>Tên Dịch Vụ</th>
                                    <th>Đơn Giá</th>
                                    <th>Mô Tả</th>
                                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
                                    <th>Thao Tác</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($dichVuList)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Chưa có dịch vụ nào.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($dichVuList as $dichVu): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($dichVu['MaDV']); ?></td>
                                            <td><?php echo htmlspecialchars($dichVu['TenDV']); ?></td>
                                            <td><?php echo number_format($dichVu['DonGia'], 0, ',', '.'); ?> VNĐ</td>
                                            <td><?php echo htmlspecialchars(substr($dichVu['MoTa'] ?? '', 0, 50)) . '...'; ?></td>
                                            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner'): ?>
                                            <td>
                                                <a href="<?php echo BASE_URL . 'admin/hienThiSuaDichVu?id=' . $dichVu['MaDV']; ?>" class="btn btn-warning btn-sm me-2">Sửa</a>
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteServiceModal" data-madv="<?php echo $dichVu['MaDV']; ?>" data-tendv="<?php echo htmlspecialchars($dichVu['TenDV']); ?>">
                                                    Xóa
                                                </button>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Import & Stock -->
        <div class="tab-pane fade" id="import-stock" role="tabpanel" aria-labelledby="import-stock-tab">
            <div class="row mt-3">
                <!-- Cột Form Nhập Hàng -->
                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Form Nhập Hàng</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="<?= BASE_URL ?>admin/nhaphang">
                                <div class="mb-3">
                                    <label for="MaDV" class="form-label fw-bold">Chọn Dịch Vụ</label>
                                    <select id="MaDV" name="MaDV" class="form-select" required>
                                        <option value="">-- Chọn dịch vụ --</option>
                                        <?php foreach ($dichVuList as $dichVu): ?>
                                            <option value="<?= htmlspecialchars($dichVu['MaDV']) ?>">
                                                <?= htmlspecialchars($dichVu['TenDV']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="SoLuongNhap" class="form-label fw-bold">Số Lượng Nhập</label>
                                    <input type="number" id="SoLuongNhap" name="SoLuongNhap" class="form-control" min="1" required>
                                </div>
                                <div class="mb-3">
                                    <label for="GhiChu" class="form-label fw-bold">Ghi Chú</label>
                                    <textarea id="GhiChu" name="GhiChu" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Xác Nhận Nhập Hàng</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Cột Bảng Tồn Kho -->
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Tình Trạng Tồn Kho Dịch Vụ</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark sticky-top">
                                        <tr>
                                            <th>Tên Dịch Vụ</th>
                                            <th class="text-end">Số Lượng Tồn</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($tonKhoDichVu)): ?>
                                            <?php foreach ($tonKhoDichVu as $tonKho): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($tonKho['TenDV']) ?></td>
                                                    <td class="text-end fw-bold">
                                                        <?php
                                                            $soLuong = $tonKho['SoLuongTon'];
                                                            $mauSac = $soLuong <= 10 ? 'text-danger' : ($soLuong <= 20 ? 'text-warning' : 'text-success');
                                                            echo "<span class='$mauSac'>$soLuong</span>";
                                                        ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="2" class="text-center">Chưa có dữ liệu tồn kho.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteServiceModal" tabindex="-1" aria-labelledby="deleteServiceModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteServiceModalLabel">Xác nhận xóa Dịch Vụ</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Bạn có chắc chắn muốn xóa dịch vụ: <strong id="serviceNameToDelete"></strong>?
        <p class="text-danger mt-2">Hành động này không thể hoàn tác.</p>
      </div>
      <div class="modal-footer">
        <form id="deleteServiceForm" method="POST" action="<?php echo BASE_URL . 'admin/xuLyXoaDichVu'; ?>">
            <input type="hidden" name="MaDV" id="maDVToDelete">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Script cho modal xác nhận xóa
    var deleteModal = document.getElementById('deleteServiceModal');
    if(deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var maDV = button.getAttribute('data-madv');
            var tenDV = button.getAttribute('data-tendv');
            var modalBodyName = deleteModal.querySelector('#serviceNameToDelete');
            var modalFormInput = deleteModal.querySelector('#maDVToDelete');
            modalBodyName.textContent = tenDV;
            modalFormInput.value = maDV;
        });
    }

    // Script to activate tab based on URL hash
    var hash = window.location.hash;
    if (hash) {
        var triggerEl = document.querySelector('.nav-tabs button[data-bs-target="' + hash + '"]');
        if (triggerEl) {
            var tab = new bootstrap.Tab(triggerEl);
            tab.show();
        }
    }

    // Tùy chọn: cập nhật hash URL khi chuyển tab mà không nhảy trang
    var tabEls = document.querySelectorAll('.nav-tabs button[data-bs-toggle="tab"]');
    tabEls.forEach(function(tabEl) {
        tabEl.addEventListener('shown.bs.tab', function(event) {
            history.pushState(null, null, event.target.dataset.bsTarget);
        });
    });
});
</script>
