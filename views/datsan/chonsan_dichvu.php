<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
    unset($_SESSION['error_message']);
}


$datSan = $data['datSan'];
$dichVuList = $data['dichVuList']; 
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
    .summary-box {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 15px;
        border: none;
        padding: 20px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
    }
    .service-item {
        background: #fff;
        border: 1px solid #dfe4ea;
        border-radius: 15px;
        padding: 15px 20px;
        transition: all 0.3s;
    }
    .service-item:hover {
        border-color: #0072ff;
        box-shadow: 0 5px 15px rgba(0, 114, 255, 0.1);
        transform: translateY(-2px);
    }
    .form-control {
        border-radius: 10px;
        border: 1px solid #dfe4ea;
        text-align: center;
        font-weight: bold;
    }
    .form-control:focus {
        border-color: #0072ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 114, 255, 0.15) !important;
    }
    .total-box {
        background: #fff;
        border-radius: 15px;
        border: 2px dashed #00c6ff;
        padding: 25px;
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
    .btn-submit-booking:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(0, 176, 155, 0.4);
        color: white;
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
            <h2 class="booking-title">🛒 Chọn Dịch Vụ</h2>
            <p class="text-muted mt-3">Chọn thêm nước uống cho trận đấu của bạn.</p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-card">
                    <div class="summary-box mb-4">
                        <h5 class="fw-bold text-primary mb-3"><i class="fas fa-clipboard-check me-2"></i>Tóm Tắt Đơn Đặt Sân:</h5>
                        <?php if (isset($datSan['is_co_dinh']) && $datSan['is_co_dinh']): ?>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Sân:</span>
                                <strong><?= htmlspecialchars($datSan['tenSan']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Loại đặt:</span>
                                <strong>Cố định (<?= htmlspecialchars($datSan['number_of_weeks']) ?> tuần)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Khung giờ:</span>
                                <strong><?= htmlspecialchars($datSan['gioBatDau']) ?> - <?= htmlspecialchars($datSan['gioKetThuc']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold">Tiền sân:</span>
                                <strong class="fs-5 text-success"><?= number_format($datSan['tongTienSan']) ?> VNĐ</strong>
                            </div>
                        <?php else: ?>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Sân:</span>
                                <strong><?= htmlspecialchars($datSan['tenSan']) ?></strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Lịch đặt:</span>
                                <strong><?= htmlspecialchars($datSan['ngayDat']) ?> (<?= htmlspecialchars($datSan['gioBatDau']) ?> - <?= htmlspecialchars($datSan['gioKetThuc']) ?>)</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Thời lượng:</span>
                                <strong><?= htmlspecialchars($datSan['thoiLuongGio']) ?> giờ</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="fw-bold">Tiền Sân:</span>
                                <strong class="fs-5 text-success"><?= number_format($datSan['tongTienSan']) ?> VNĐ</strong>
                            </div>
                        <?php endif; ?>
                        <input type="hidden" id="tongTienSan" value="<?= htmlspecialchars($datSan['tongTienSan']) ?>">
                        <input type="hidden" id="isCoDinh" value="<?= isset($datSan['is_co_dinh']) && $datSan['is_co_dinh'] ? 'true' : 'false'; ?>">
                        <input type="hidden" id="soTuan" value="<?= htmlspecialchars($datSan['number_of_weeks'] ?? 1) ?>">
                    </div>
                    
                    <form id="formChonDichVu" action="<?= BASE_URL ?>luu-tam-dich-vu" method="POST">
                        
                        <h5 class="fw-bold mb-4"><i class="fas fa-concierge-bell text-warning me-2"></i>Dịch Vụ Đi Kèm:</h5>
                        
                        <?php if (isset($_SESSION['thieuKho']) && !empty($_SESSION['thieuKho'])): ?>
                            <div class="alert alert-warning border-0 rounded-3 shadow-sm mb-4">
                                <h5 class="alert-heading text-warning-emphasis fw-bold"><i class="fas fa-box-open me-2"></i>Cảnh báo kho hàng</h5>
                                <ul class="mb-2">
                                    <?php foreach ($_SESSION['thieuKho'] as $item): ?>
                                        <li><strong><?= htmlspecialchars($item['tenDV']) ?></strong>: Yêu cầu <?= $item['can'] ?>, hiện chỉ còn <strong><?= $item['ton'] ?></strong>.</li>
                                    <?php endforeach; ?>
                                </ul>
                                <p class="mb-0 small text-danger">Vui lòng điều chỉnh lại số lượng phù hợp với tồn kho trước khi tiếp tục.</p>
                            </div>
                            <?php unset($_SESSION['thieuKho']); ?>
                        <?php endif; ?>

                        <?php if (isset($datSan['is_co_dinh']) && $datSan['is_co_dinh']): ?>
                            <div class="alert alert-info border-0 rounded-3 shadow-sm mb-4">
                                <i class="fas fa-info-circle me-2"></i>Lưu ý: Dịch vụ bạn chọn sẽ được áp dụng cho tất cả <strong><?= htmlspecialchars($datSan['number_of_weeks']) ?></strong> buổi đặt.
                            </div>
                        <?php endif; ?>

                        <div id="dichVuContainer" class="mb-5">
                            <div class="row g-3">
                                <?php foreach ($dichVuList as $dv): ?>
                                <div class="col-12" data-gia="<?= htmlspecialchars($dv['DonGia']) ?>">
                                    <div class="service-item d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <div class="ms-2">
                                                <h6 class="mb-1 fw-bold"><?= htmlspecialchars($dv['TenDV']) ?></h6>
                                                <small class="text-muted">Giá: <span class="gia-dv text-primary fw-bold"><?= number_format($dv['DonGia']) ?></span> đ</small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <span class="tong-tien-dv text-secondary fw-bold" style="min-width: 80px; text-align: right;">0 đ</span>
                                            <input type="number" 
                                                class="form-control so-luong-dv" 
                                                data-madv="<?= htmlspecialchars($dv['MaDV']) ?>"
                                                data-don-gia="<?= htmlspecialchars($dv['DonGia']) ?>"
                                                name="dichVu[<?= htmlspecialchars($dv['MaDV']) ?>][soLuong]" 
                                                min="0" value="0" style="width: 70px;">
                                            <input type="hidden" 
                                                name="dichVu[<?= htmlspecialchars($dv['MaDV']) ?>][donGia]" 
                                                value="<?= htmlspecialchars($dv['DonGia']) ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="total-box text-center mb-4">
                            <h5 class="text-muted mb-3">Tổng Hóa Đơn Tạm Tính</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Tổng tiền dịch vụ:</span>
                                <strong id="tongTienDichVu" class="text-warning">0 VNĐ</strong>
                            </div>
                            <hr class="my-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h4 mb-0 fw-bold">TỔNG CỘNG:</span>
                                <strong id="tongTienHoaDon" class="h3 mb-0 text-primary fw-bold">0 VNĐ</strong>
                            </div>
                        </div>

                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <a href="<?= BASE_URL ?>dat-san" class="btn btn-outline-secondary btn-back w-100">
                                    <i class="fas fa-arrow-left me-2"></i> Quay Lại
                                </a>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-submit-booking w-100" id="btnXacNhan">
                                    Xác Nhận & Thanh Toán <i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    
    const tongTienSan = parseFloat($('#tongTienSan').val());
    const isCoDinh = $('#isCoDinh').val() === 'true';
    const soTuan = parseInt($('#soTuan').val());
    
    const tongTienDichVuEl = $('#tongTienDichVu');
    const tongTienHoaDonEl = $('#tongTienHoaDon');

    function formatVND(amount) {
        return amount.toLocaleString('vi-VN') + ' VNĐ';
    }

    function tinhTongHoaDon() {
        let tongTienDichVuTheoBuoi = 0;

        $('.so-luong-dv').each(function() {
            const $input = $(this);
            const soLuong = parseInt($input.val()) || 0;
            const donGia = parseFloat($input.data('don-gia'));
            const thanhTienDV = soLuong * donGia;
            tongTienDichVuTheoBuoi += thanhTienDV;
            
            // Hiển thị giá cho 1 buổi nếu là đặt cố định
            let displayPrice = isCoDinh ? thanhTienDV * soTuan : thanhTienDV;
            $input.closest('.row').find('.tong-tien-dv').text(formatVND(displayPrice));
        });

        const tongTienDichVuFinal = isCoDinh ? tongTienDichVuTheoBuoi * soTuan : tongTienDichVuTheoBuoi;
        const tongTienCuoi = tongTienSan + tongTienDichVuFinal;

        tongTienDichVuEl.text(formatVND(tongTienDichVuFinal));
        tongTienHoaDonEl.text(formatVND(tongTienCuoi));
    }

    $('#dichVuContainer').on('input', '.so-luong-dv', tinhTongHoaDon);

    tinhTongHoaDon();
});
</script>