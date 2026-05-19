<div class="container my-5">
    <h1 class="mb-4">Quản Lý Khách Hàng</h1>

    <!-- Form tìm kiếm -->
    <form method="POST" action="<?= BASE_URL ?>admin/khachhang" class="mb-4 p-4 bg-light border rounded">
        <div class="row align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label fw-bold">Tìm Kiếm Theo Tên Khách Hàng</label>
                <input type="text" id="search" name="search" class="form-control" value="<?= htmlspecialchars($searchTerm ?? '') ?>" placeholder="Nhập tên khách hàng...">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tìm Kiếm</button>
            </div>
             <div class="col-md-2">
                <a href="<?= BASE_URL ?>admin/khachhang" class="btn btn-secondary w-100">Làm Mới</a>
            </div>
        </div>
    </form>

    <h3 class="mt-5">Danh Sách Khách Hàng</h3>
    
    <!-- Bảng hiển thị danh sách -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Mã KH</th>
                        <th>Tên KH</th>
                        <th>Số Điện Thoại</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($khachHangList)): ?>
                        <?php foreach ($khachHangList as $khachHang): ?>
                            <tr>
                                <td><?= htmlspecialchars($khachHang['MaKH']) ?></td>
                                <td><?= htmlspecialchars($khachHang['HoTen']) ?></td>
                                <td><?= htmlspecialchars($khachHang['SDT']) ?></td>
                                <td><?= htmlspecialchars($khachHang['Email']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">Không tìm thấy khách hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
