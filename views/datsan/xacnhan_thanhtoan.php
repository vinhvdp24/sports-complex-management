<?php 
// Lấy dữ liệu từ Controller
$datSan = $data['datSan'];
$ptttList = $data['ptttList'];

// Lấy các giá trị đã tính toán từ Session
$tongTienSan = $datSan['tongTienSan'];
$tongTienDichVu = $datSan['tongTienDichVu'];
$dichVuChon = $datSan['dichVuChon'];
$tongTienHoaDon = $datSan['tongTienHoaDon'];

// Hàm định dạng tiền tệ
function formatVND($amount) {
    return number_format($amount) . ' VNĐ';
}
?>

<style>
    .booking-wrapper {
        background: #f8f9fa;
        min-height: 80vh;
        padding-top: 40px;
        padding-bottom: 60px;
    }
    .booking-title {
        font-weight: 800;
        color: #2c3e50;
        margin-bottom: 2rem;
        position: relative;
        display: inline-block;
    }
    .booking-title::after {
        content: '';
        position: absolute;
        width: 40%;
        height: 4px;
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        bottom: -10px;
        left: 30%;
        border-radius: 2px;
    }
    .booking-card {
        background: #fff;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 2.5rem;
    }
    .section-title {
        font-weight: 800;
        color: #2d3436;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
    }
    .section-icon {
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-size: 1.5rem;
        margin-right: 10px;
    }
    .info-card {
        background: #fff;
        border-radius: 15px;
        border: 1px solid #dfe4ea;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    .info-card:hover {
        border-color: #0072ff;
        box-shadow: 0 5px 15px rgba(0, 114, 255, 0.05);
    }
    .detail-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        align-items: center;
    }
    .detail-label {
        color: #636e72;
        font-weight: 600;
    }
    .detail-value {
        color: #2d3436;
        font-weight: 700;
        text-align: right;
    }
    .table-services {
        background: #fff;
        border-radius: 15px;
        overflow: hidden;
        border: 1px solid #dfe4ea;
    }
    .table-services thead th {
        background: #f8f9fa;
        border-bottom: 1px solid #dfe4ea;
        color: #636e72;
        font-weight: 700;
        padding: 15px;
    }
    .table-services tbody td {
        padding: 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f2f6;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #dfe4ea;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0072ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 114, 255, 0.15) !important;
    }
    .total-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 20px;
        border: 2px dashed #00c6ff;
        padding: 30px;
        text-align: center;
    }
    .btn-submit-booking {
        background: linear-gradient(45deg, #00b09b, #96c93d);
        border: none;
        border-radius: 15px;
        padding: 15px;
        font-size: 1.15rem;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(0, 176, 155, 0.3);
        transition: all 0.3s ease;
        color: white;
    }
    .btn-submit-booking:hover:not(:disabled) {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(0, 176, 155, 0.4);
        color: white;
    }
    .btn-submit-booking:disabled {
        background: #e9ecef;
        color: #adb5bd;
        box-shadow: none;
        cursor: not-allowed;
    }
    .btn-back {
        border-radius: 15px;
        padding: 15px;
        font-size: 1.15rem;
        font-weight: 600;
        transition: all 0.3s;
    }
</style>

<div class="booking-wrapper">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="booking-title">✅ Xác Nhận Đặt Sân</h2>
            <p class="text-muted mt-3">Vui lòng kiểm tra lại thông tin đơn hàng và chọn phương thức thanh toán.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-card">

                    <?php if (isset($_SESSION['error_message'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($_SESSION['error_message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['error_message']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['thieuKho']) && !empty($_SESSION['thieuKho'])): ?>
                        <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-4">
                            <h5 class="alert-heading text-warning-emphasis fw-bold"><i class="fas fa-box-open me-2"></i>Cảnh báo kho hàng</h5>
                            <ul class="mb-2">
                                <?php foreach ($_SESSION['thieuKho'] as $item): ?>
                                    <li><strong><?= htmlspecialchars($item['tenDV']) ?></strong>: Yêu cầu <?= $item['can'] ?>, hiện chỉ còn <strong><?= $item['ton'] ?></strong>.</li>
                                <?php endforeach; ?>
                            </ul>
                            <p class="mb-0 small text-danger"><i class="fas fa-arrow-left me-1"></i>Vui lòng quay lại bước chọn dịch vụ để điều chỉnh số lượng.</p>
                        </div>
                    <?php endif; ?>

                    <form id="formXacNhan" action="<?= BASE_URL ?>hoan-tat-dat-san" method="POST">
                        
                        <h4 class="section-title"><i class="fas fa-file-invoice section-icon"></i>Chi Tiết Đơn Đặt Sân</h4>
                        
                        <div class="info-card">
                            <h5 class="fw-bold text-primary mb-3"><?= htmlspecialchars($datSan['tenSan'] ?? $datSan['maSan']) ?></h5>
                            
                            <?php if (isset($datSan['is_co_dinh']) && $datSan['is_co_dinh']): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Loại đặt:</span>
                                    <span class="detail-value text-info">Cố định (<?= htmlspecialchars($datSan['number_of_weeks']) ?> tuần)</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Các ngày đặt:</span>
                                    <span class="detail-value text-muted small text-end" style="max-width: 60%;">
                                        <?php 
                                            $dates = array_map(function($date) { return date('d/m/Y', strtotime($date)); }, $datSan['dates_to_book']);
                                            echo implode(', ', $dates);
                                        ?>
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Khung giờ:</span>
                                    <span class="detail-value"><?= htmlspecialchars($datSan['gioBatDau']) ?> - <?= htmlspecialchars($datSan['gioKetThuc']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Thời lượng/buổi:</span>
                                    <span class="detail-value"><?= htmlspecialchars($datSan['thoiLuongGio']) ?> giờ</span>
                                </div>
                                <hr class="my-3">
                                <div class="detail-row mb-0">
                                    <span class="detail-label fw-bold">Tổng tiền sân:</span>
                                    <span class="detail-value fs-5 text-success"><?= formatVND($tongTienSan) ?></span>
                                </div>
                            <?php else: ?>
                                <div class="detail-row">
                                    <span class="detail-label">Ngày đặt:</span>
                                    <span class="detail-value"><?= htmlspecialchars($datSan['ngayDat']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Khung giờ:</span>
                                    <span class="detail-value"><?= htmlspecialchars($datSan['gioBatDau']) ?> - <?= htmlspecialchars($datSan['gioKetThuc']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Thời lượng:</span>
                                    <span class="detail-value"><?= htmlspecialchars($datSan['thoiLuongGio']) ?> giờ</span>
                                </div>
                                <hr class="my-3">
                                <div class="detail-row mb-0">
                                    <span class="detail-label fw-bold">Tổng tiền sân:</span>
                                    <span class="detail-value fs-5 text-success"><?= formatVND($tongTienSan) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($dichVuChon)): ?>
                            <h5 class="fw-bold mb-3 mt-4 text-secondary"><i class="fas fa-plus-circle me-2"></i>Dịch Vụ Đi Kèm</h5>
                            <div class="table-responsive table-services mb-4">
                                <table class="table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Tên dịch vụ</th>
                                            <th class="text-end">Đơn giá</th>
                                            <th class="text-center">Số lượng</th>
                                            <th class="text-end">Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($dichVuChon as $dv): ?>
                                            <tr>
                                                <td class="fw-bold"><?= htmlspecialchars($dv['TenDV']) ?></td>
                                                <td class="text-end text-muted"><?= formatVND($dv['DonGia']) ?></td>
                                                <td class="text-center"><span class="badge bg-light text-dark border px-3 py-2"><?= htmlspecialchars($dv['SoLuong']) ?></span></td>
                                                <td class="text-end fw-bold text-success"><?= formatVND($dv['ThanhTien']) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr class="bg-light">
                                            <td colspan="3" class="text-end fw-bold">Tổng tiền dịch vụ:</td>
                                            <td class="text-end fw-bold text-success fs-5"><?= formatVND($tongTienDichVu) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mt-5">
                            <div class="col-md-6 mb-4 mb-md-0">
                                <h4 class="section-title"><i class="fas fa-wallet section-icon"></i>Thanh Toán</h4>
                                
                                <div class="mb-4">
                                    <label class="form-label text-muted fw-bold mb-2">Ghi chú cho đơn hàng (Tùy chọn):</label>
                                    <textarea name="ghiChuHoaDon" class="form-control" rows="3" placeholder="Yêu cầu đặc biệt, ví dụ: 'Xuất hóa đơn đỏ', 'Gọi trước 10 phút'..."></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label text-muted fw-bold mb-2">Chọn phương thức:</label>
                                    <select name="maPT" class="form-select" required>
                                        <option value="">-- Chọn Phương Thức Thanh Toán --</option>
                                        <?php foreach ($ptttList as $pttt): ?>
                                            <option value="<?= htmlspecialchars($pttt['MaPT']) ?>">
                                                <?= htmlspecialchars($pttt['TenPT']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center">
                                <div class="total-box w-100">
                                    <p class="h5 text-muted mb-2 fw-bold">TỔNG TIỀN PHẢI TRẢ</p>
                                    <h1 class="text-danger fw-bold mb-0" id="tongTienHoaDonCuoi" style="font-size: 2.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">
                                        <?= formatVND($tongTienHoaDon) ?>
                                    </h1>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mt-4 pt-3 border-top">
                            <div class="col-md-4">
                                <a href="<?= BASE_URL ?>chon-dich-vu" class="btn btn-outline-secondary btn-back w-100">
                                    <i class="fas fa-arrow-left me-2"></i> Quay Lại
                                </a>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-submit-booking w-100" <?= (isset($_SESSION['thieuKho']) && !empty($_SESSION['thieuKho'])) ? 'disabled title="Vui lòng điều chỉnh lại số lượng dịch vụ"' : '' ?>>
                                    Hoàn Tất Đặt Sân <i class="fas fa-check-circle ms-2"></i>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>