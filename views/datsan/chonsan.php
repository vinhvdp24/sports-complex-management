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
        margin-bottom: 1rem;
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
    .step-label {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
    }
    .step-number {
        background: linear-gradient(45deg, #00c6ff, #0072ff);
        color: white;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        margin-right: 10px;
        font-size: 1rem;
    }
    .form-control, .form-select {
        border-radius: 12px;
        padding: 12px 15px;
        border: 1px solid #dfe4ea;
        box-shadow: none !important;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0072ff;
        box-shadow: 0 0 0 0.2rem rgba(0, 114, 255, 0.15) !important;
    }
    .custom-radio-group .form-check-input {
        display: none;
    }
    .custom-radio-group .form-check-label {
        border: 2px solid #dfe4ea;
        border-radius: 12px;
        padding: 10px 20px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 600;
        color: #636e72;
        background: #fff;
    }
    .custom-radio-group .form-check-input:checked + .form-check-label {
        border-color: #0072ff;
        background: rgba(0, 114, 255, 0.05);
        color: #0072ff;
    }
    .slot-btn {
        border-radius: 12px !important;
        padding: 10px 15px;
        font-weight: 600;
        border: 2px solid #007bff;
        color: #007bff;
        background: white;
        transition: all 0.3s;
        margin-bottom: 10px;
    }
    .slot-btn:hover {
        background: rgba(0, 123, 255, 0.1);
        transform: translateY(-2px);
    }
    .slot-btn.active {
        background: linear-gradient(45deg, #00c6ff, #0072ff) !important;
        color: white !important;
        border-color: transparent !important;
        box-shadow: 0 5px 15px rgba(0, 114, 255, 0.3);
    }
    .summary-box {
        background: #f8f9fa;
        border-radius: 15px;
        border: 1px dashed #ced4da;
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
</style>

<div class="booking-wrapper">
    <div class="container">
        <?php 
            $tenLoaiHienTai = "Sân Vận Động";
            if ($maLoai == 'bd') $tenLoaiHienTai = "Sân Bóng Đá";
            if ($maLoai == 'cl') $tenLoaiHienTai = "Sân Cầu Lông";
            if ($maLoai == 'pkb') $tenLoaiHienTai = "Sân Pickleball";
        ?>
        <div class="text-center mb-5">
            <h2 class="booking-title">🗓️ Đặt <?php echo $tenLoaiHienTai; ?></h2>
            <p class="text-muted mt-3">Vui lòng chọn sân, ngày, thời lượng và khung giờ bạn muốn đặt.</p>
            <a href="<?php echo BASE_URL; ?>home/index" class="btn btn-outline-secondary btn-sm rounded-pill px-4 mt-2 transition-all hover:shadow">
                <i class="fas fa-arrow-left me-1"></i> Thay đổi nhu cầu chơi
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="booking-card">
                    <form id="formDatSan">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="maSan" class="step-label"><span class="step-number">1</span> Chọn Sân</label>
                                <select class="form-select" id="maSan" name="maSan" required>
                                    <option value="">-- Chọn Sân --</option>
                                    <?php foreach ($sanBongList as $san): ?>
                                        <option 
                                            value="<?= htmlspecialchars($san['MaSan']) ?>" 
                                            data-gia="<?= htmlspecialchars($san['GiaThue']) ?>"
                                            data-loaisan="<?= htmlspecialchars($san['TenLoai']) ?>"
                                            data-mota="<?= htmlspecialchars($san['MoTa'] ?? '') ?>"
                                            data-tinhtrang="<?= htmlspecialchars($san['TinhTrang'] ?? 'Hoạt động') ?>"
                                        >
                                            <?= htmlspecialchars($san['TenSan']) ?> (Giá: <?= number_format($san['GiaThue']) ?>/giờ)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="statusWarning" class="alert alert-danger mt-3 d-none rounded-3 border-0 shadow-sm">
                                    <i class="fas fa-exclamation-triangle me-2"></i> 
                                    <strong>Sân hiện đang bảo trì!</strong> Quý khách vui lòng chọn sân khác.
                                </div>
                                <div id="sanDescription" class="mt-3 text-muted small d-none p-3 bg-light rounded-3">
                                    <i class="fas fa-info-circle me-1 text-primary"></i> <span id="textMoTa"></span>
                                </div>
                                <input type="hidden" id="giaThueHienTai" name="giaThueHienTai">
                            </div>

                            <div class="col-md-6">
                                <label for="ngayDat" class="step-label"><span class="step-number">2</span> Ngày Đặt</label>
                                <input type="date" class="form-control" id="ngayDat" name="ngayDat" required 
                                       min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="step-label"><span class="step-number">3</span> Chọn Thời Lượng Thuê</label>
                            <div class="d-flex flex-wrap gap-3 custom-radio-group mt-2">
                                <div class="form-check p-0">
                                    <input class="form-check-input" type="radio" name="thoiLuong" id="tl1h" value="1" checked>
                                    <label class="form-check-label" for="tl1h">1.0 Giờ</label>
                                </div>
                                <div class="form-check p-0">
                                    <input class="form-check-input" type="radio" name="thoiLuong" id="tl15h" value="1.5">
                                    <label class="form-check-label" for="tl15h">1.5 Giờ</label>
                                </div>
                                <div class="form-check p-0">
                                    <input class="form-check-input" type="radio" name="thoiLuong" id="tl2h" value="2">
                                    <label class="form-check-label" for="tl2h">2.0 Giờ</label>
                                </div>
                            </div>
                        </div>

                        <div id="khungGioContainer" class="mb-4 d-none">
                            <label class="step-label"><span class="step-number">4</span> Chọn Khung Giờ Trống</label>
                            <div id="listKhungGio" class="d-flex flex-wrap gap-2 mt-3">
                                <!-- Khung giờ sẽ được load bằng AJAX -->
                            </div>
                            <input type="hidden" id="gioBatDau" name="gioBatDau">
                            <input type="hidden" id="gioKetThuc" name="gioKetThuc">
                        </div>

                        <div id="alertMessage" class="alert d-none rounded-3 border-0 shadow-sm" role="alert"></div>

                        <div id="ketQuaDatSan" class="mt-4 p-4 summary-box d-none">
                            <h5 class="mb-3 fw-bold text-primary"><i class="fas fa-receipt me-2"></i> Tóm Tắt Chi Phí Sân</h5>
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <span class="text-muted">Thời lượng thuê:</span>
                                <span><strong id="hienThiThoiLuong"></strong> giờ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3 border-bottom pb-2">
                                <span class="text-muted">Khung giờ:</span>
                                <span><strong id="hienThiKhungGio"></strong></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5">Tạm tính:</span>
                                <span class="fs-4 fw-bold text-danger" id="hienThiTongTien"></span>
                            </div>
                            <input type="hidden" id="tongTienSanTamTinh" name="tongTienSanTamTinh">
                            <input type="hidden" id="thoiLuongThueTamTinh" name="thoiLuongThueTamTinh">
                        </div>

                        <div class="d-grid mt-4 pt-2">
                            <button type="submit" class="btn btn-submit-booking" id="btnTiepTuc" disabled>
                                Tiếp Tục Chọn Dịch Vụ Kèm Theo <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <div id="datCoDinhContainer" class="text-center mt-4 d-none">
                            <a href="#" id="btnDatCoDinh" class="text-decoration-none fw-bold" style="color: #667eea;">
                                <i class="bi bi-calendar-range"></i> Bạn muốn đặt lịch cố định theo tuần/tháng? Bấm vào đây!
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Thêm jQuery nếu chưa có -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const API_GET_SLOTS = '<?php echo BASE_URL; ?>get-khung-gio-trong';
    
    function loadKhungGio() {
        const maSan = $('#maSan').val();
        const ngayDat = $('#ngayDat').val();
        const thoiLuong = $('input[name="thoiLuong"]:checked').val();
        
        if (!maSan || !ngayDat) return;

        $('#khungGioContainer').removeClass('d-none');
        $('#listKhungGio').html('<div class="text-center w-100"><div class="spinner-border text-primary" role="status"></div><p>Đang tải khung giờ...</p></div>');
        $('#btnTiepTuc').prop('disabled', true);
        $('#ketQuaDatSan').addClass('d-none');
        $('#gioBatDau').val('');
        $('#gioKetThuc').val('');

        $.ajax({
            url: API_GET_SLOTS,
            type: 'POST',
            data: { maSan, ngayDat, thoiLuong },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    let html = '';
                    if (response.slots.length === 0) {
                        html = '<p class="text-danger">Rất tiếc, không còn khung giờ trống nào cho lựa chọn này.</p>';
                    } else {
                        response.slots.forEach(slot => {
                            html += `<button type="button" class="btn btn-outline-primary slot-btn" 
                                        data-start="${slot.start}" data-end="${slot.end}">
                                        ${slot.label}
                                     </button>`;
                        });
                    }
                    $('#listKhungGio').html(html);
                } else {
                    $('#listKhungGio').html('<p class="text-danger">' + response.message + '</p>');
                }
            },
            error: function() {
                $('#listKhungGio').html('<p class="text-danger">Lỗi kết nối máy chủ.</p>');
            }
        });
    }

    // Sự kiện khi thay đổi các yếu tố đầu vào
    $('#maSan, #ngayDat, input[name="thoiLuong"]').change(function() {
        const selectedOption = $('#maSan').find('option:selected');
        const giaThue = selectedOption.data('gia');
        const moTa = selectedOption.data('mota');
        const tinhTrang = selectedOption.data('tinhtrang');

        $('#giaThueHienTai').val(giaThue);

        // Kiểm tra tình trạng bảo trì
        if (tinhTrang === 'Bảo trì') {
            $('#statusWarning').removeClass('d-none');
            $('#khungGioContainer').addClass('d-none');
            $('#datCoDinhContainer').addClass('d-none');
            $('#ketQuaDatSan').addClass('d-none');
            $('#btnTiepTuc').prop('disabled', true);
            return; // Dừng lại không load khung giờ
        } else {
            $('#statusWarning').addClass('d-none');
        }

        // Hiển thị mô tả sân
        if (moTa && moTa.trim() !== '') {
            $('#textMoTa').text(moTa);
            $('#sanDescription').removeClass('d-none');
        } else {
            $('#sanDescription').addClass('d-none');
        }

        if ($('#maSan').val()) {
            const baseUrl = '<?php echo BASE_URL; ?>';
            $('#btnDatCoDinh').attr('href', `${baseUrl}datsan/dat-co-dinh?san_id=${$('#maSan').val()}`);
            $('#datCoDinhContainer').removeClass('d-none');
        } else {
            $('#datCoDinhContainer').addClass('d-none');
        }

        loadKhungGio();
    });

    // Sự kiện khi chọn một khung giờ
    $(document).on('click', '.slot-btn', function() {
        $('.slot-btn').removeClass('active');
        $(this).addClass('active');

        const start = $(this).data('start');
        const end = $(this).data('end');
        $('#gioBatDau').val(start);
        $('#gioKetThuc').val(end);

        // Tính toán giá tiền
        const giaThue = parseFloat($('#giaThueHienTai').val());
        const thoiLuong = parseFloat($('input[name="thoiLuong"]:checked').val());
        const tongTien = giaThue * thoiLuong;

        $('#hienThiThoiLuong').text(thoiLuong);
        $('#hienThiKhungGio').text(start + ' - ' + end);
        $('#hienThiTongTien').text(new Intl.NumberFormat('vi-VN').format(tongTien) + ' VNĐ');
        
        $('#tongTienSanTamTinh').val(tongTien);
        $('#thoiLuongThueTamTinh').val(thoiLuong);

        $('#ketQuaDatSan').removeClass('d-none');
        $('#btnTiepTuc').prop('disabled', false);
    });

    // Gửi biểu mẫu
    $('#formDatSan').submit(function(e) {
        if (!$('#gioBatDau').val()) {
            e.preventDefault();
            alert('Vui lòng chọn khung giờ.');
            return;
        }
        this.action = '<?php echo BASE_URL; ?>luu-tam-dat-san'; 
        this.method = 'POST';
    });

    // Load khung giờ ngay nếu đã có sân (trường hợp quay lại từ bước sau)
    if ($('#maSan').val()) {
        loadKhungGio();
    }
});
</script>