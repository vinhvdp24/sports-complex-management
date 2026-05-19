<?php
// Lấy thông tin sân từ query string
$san_id = isset($_GET['san_id']) ? htmlspecialchars($_GET['san_id']) : '';
$san_info = null;
if ($san_id) {
    $sanModel = new \App\Models\SanModel();
    $san_info = $sanModel->getSanById($san_id);
    if ($san_info) {
        $loaiSan = $sanModel->getAllLoaiSan();
        foreach ($loaiSan as $loai) {
            if ($loai['MaLoai'] == $san_info['MaLoai']) {
                $san_info['TenLoai'] = $loai['TenLoai'];
                break;
            }
        }
    }
}
?>

<div class="container my-5">
    <h2 class="text-center mb-4">🗓️ Đặt Sân Cố Định</h2>

    <?php if (!$san_info): ?>
        <div class="alert alert-danger text-center">
            Không tìm thấy thông tin sân. Vui lòng quay lại <a href="<?php echo BASE_URL; ?>dat-san">trang chọn sân</a>.
        </div>
    <?php else: ?>
        <p class="text-center text-muted">
            Bạn đang đặt lịch cố định cho sân: <strong><?php echo htmlspecialchars($san_info['TenSan']); ?></strong>
            (Loại: <?php echo htmlspecialchars($san_info['TenLoai'] ?? 'N/A'); ?>).
        </p>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow-sm p-4">
                    <form id="formDatCoDinh" action="<?php echo BASE_URL; ?>datsan/luu-tam-dat-co-dinh" method="POST">
                        <input type="hidden" name="san_id" value="<?php echo $san_id; ?>">

                        <div class="mb-3">
                            <label for="ngay_da" class="form-label fw-bold">Chọn ngày đá hàng tuần:</label>
                            <select class="form-select" id="ngay_da" name="ngay_da" required>
                                <option value="1">Thứ Hai</option>
                                <option value="2">Thứ Ba</option>
                                <option value="3">Thứ Tư</option>
                                <option value="4">Thứ Năm</option>
                                <option value="5">Thứ Sáu</option>
                                <option value="6">Thứ Bảy</option>
                                <option value="7">Chủ Nhật</option>
                            </select>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="gio_bat_dau" class="form-label fw-bold">Giờ Bắt Đầu:</label>
                                <input type="time" class="form-control" id="gio_bat_dau" name="gio_bat_dau" required>
                            </div>
                            <div class="col-md-6">
                                <label for="gio_ket_thuc" class="form-label fw-bold">Giờ Kết Thúc:</label>
                                <input type="time" class="form-control" id="gio_ket_thuc" name="gio_ket_thuc" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="thoi_han" class="form-label fw-bold">Chọn thời hạn đặt sân:</label>
                            <select class="form-select" id="thoi_han" name="thoi_han" required>
                                <option value="1">1 Tháng (4 tuần)</option>
                                <option value="2">2 Tháng (8 tuần)</option>
                            </select>
                        </div>

                        <!-- Khu vực hiển thị kết quả AJAX -->
                        <div id="alertMessage" class="alert d-none mt-3" role="alert"></div>
                        <div id="ketQuaKiemTra" class="mt-3 p-3 border rounded bg-light d-none">
                            <h5 class="mb-2">💰 Tóm Tắt Chi Phí</h5>
                            <p class="h4">Tổng tiền tạm tính: <strong class="text-primary" id="hienThiTongTien"></strong></p>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="button" id="btnKiemTraCoDinh" class="btn btn-warning">Kiểm Tra Lịch & Tính Giá</button>
                            <button type="submit" id="btnXacNhan" class="btn btn-primary" disabled>Xác Nhận Đặt Lịch Cố Định</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Thêm jQuery nếu chưa có -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const AJAX_URL = '<?php echo BASE_URL; ?>datsan/kiem-tra-co-dinh';
    const sanId = $('input[name="san_id"]').val();

    // Reset khi người dùng thay đổi lựa chọn
    $('#ngay_da, #gio_bat_dau, #gio_ket_thuc, #thoi_han').change(function() {
        $('#btnXacNhan').prop('disabled', true);
        $('#alertMessage').addClass('d-none');
        $('#ketQuaKiemTra').addClass('d-none');
    });

    $('#btnKiemTraCoDinh').click(function() {
        const ngayDa = $('#ngay_da').val();
        const gioBatDau = $('#gio_bat_dau').val();
        const gioKetThuc = $('#gio_ket_thuc').val();
        const thoiHan = $('#thoi_han').val();
        const alertDiv = $('#alertMessage');
        const ketQuaDiv = $('#ketQuaKiemTra');

        // Đặt lại giao diện
        alertDiv.addClass('d-none').removeClass('alert-danger alert-success');
        ketQuaDiv.addClass('d-none');
        $('#btnXacNhan').prop('disabled', true);

        if (!ngayDa || !gioBatDau || !gioKetThuc || !thoiHan) {
            alertDiv.text('Vui lòng điền đầy đủ thông tin.').addClass('alert-danger').removeClass('d-none');
            return;
        }

        // --- XÁC THỰC THỜI GIAN PHÍA CLIENT ---
        const startTime = new Date(`2000-01-01T${gioBatDau}:00`);
        const endTime = new Date(`2000-01-01T${gioKetThuc}:00`);
        const minAllowedTime = new Date('2000-01-01T06:00:00');
        const maxAllowedTime = new Date('2000-01-01T23:00:00');

        if (startTime < minAllowedTime || endTime > maxAllowedTime || startTime >= endTime) {
            alertDiv.removeClass('d-none alert-success').addClass('alert-danger').text('Chỉ được phép đặt sân trong khung giờ từ 6:00 sáng đến 23:00 khuya và giờ kết thúc phải sau giờ bắt đầu.');
            return; // Dừng xử lý nếu thời gian không hợp lệ
        }
        // --- KẾT THÚC XÁC THỰC THỜI GIAN PHÍA CLIENT ---


        $.ajax({
            url: AJAX_URL,
            type: 'POST',
            dataType: 'json',
            data: {
                san_id: sanId,
                ngay_da: ngayDa,
                gio_bat_dau: gioBatDau,
                gio_ket_thuc: gioKetThuc,
                thoi_han: thoiHan
            },
            beforeSend: function() {
                $('#btnKiemTraCoDinh').prop('disabled', true).text('Đang kiểm tra...');
            },
            complete: function() {
                $('#btnKiemTraCoDinh').prop('disabled', false).text('Kiểm Tra Lịch & Tính Giá');
            },
            success: function(response) {
                if (response.success) {
                    alertDiv.text(response.message).addClass('alert-success').removeClass('d-none alert-danger');
                    $('#hienThiTongTien').text(response.total_price_formatted + ' VNĐ');
                    ketQuaDiv.removeClass('d-none');
                    $('#btnXacNhan').prop('disabled', false);
                } else {
                    alertDiv.html('<strong>Lỗi:</strong> ' + response.message).addClass('alert-danger').removeClass('d-none alert-success');
                }
            },
            error: function() {
                alertDiv.text('Lỗi kết nối đến server. Vui lòng thử lại.').addClass('alert-danger').removeClass('d-none');
            }
        });
    });
});
</script>