<?php
namespace App\Controllers;
require_once __DIR__ . '/../Core/Controller.php'; 
require_once __DIR__ . '/../Models/SanModel.php'; 
require_once __DIR__ . '/../Models/DatSanModel.php';
require_once __DIR__ . '/../Models/KhachHangModel.php';
require_once __DIR__ . '/../Models/DichVuModel.php';

use App\Core\Controller; 
use App\Models\SanModel;
use App\Models\DichVuModel;
use App\Models\TonKhoModel;
use App\Models\DatSanModel;
use App\Models\KhachHangModel;
use App\Core\Session;
use App\Core\MoMoPayment;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class DatsanController extends Controller
{
    private $sanModel;
    private $tonKhoModel;
    private $datSanModel;
    private $khachHangModel;
    private $dichVuModel;

    public function __construct()
    {
        $this->sanModel = new SanModel();
        $this->tonKhoModel = new TonKhoModel();
        $this->datSanModel = new DatSanModel();
        $this->khachHangModel = new KhachHangModel();
        $this->dichVuModel = new DichVuModel();
    }

    private function checkAuth()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
            $_SESSION['login_error'] = "Vui lòng đăng nhập để thực hiện chức năng này.";
            header("Location: " . BASE_URL . "auth/hienThiDangNhap");
            exit;
        }
    }

    /**
     * [View 1] Hiển thị trang chọn sân, ngày và giờ.
     */
    public function chonSan()
    {
        $this->checkAuth();

        $maLoai = $_GET['loai'] ?? null;
        
        if ($maLoai) {
            $sanBongList = $this->sanModel->getSanByLoai($maLoai);
        } else {
            $sanBongList = $this->sanModel->getAllSan();
        }

        // Truyền dữ liệu sang View
        $this->view('datsan/chonsan', [
            'sanBongList' => $sanBongList,
            'title' => 'Chọn Sân & Thời Gian',
            'maLoai' => $maLoai
        ]);
    }

    /**
     * API trả về danh sách khung giờ trống
     */
    public function getKhungGioTrong()
    {
        $maSan = $_POST['maSan'] ?? null;
        $ngayDat = $_POST['ngayDat'] ?? null;
        $thoiLuong = floatval($_POST['thoiLuong'] ?? 1); // Có thể là 1, 1.5 hoặc 2 giờ

        if (!$maSan || !$ngayDat) {
            $this->json(['success' => false, 'message' => 'Thiếu thông tin sân hoặc ngày.']);
            return;
        }

        // Lấy danh sách các slot đã đặt
        $bookedSlots = $this->datSanModel->getBookedSlots($maSan, $ngayDat);

        $availableSlots = [];
        $startTime = strtotime('05:00'); // Đồng bộ giờ mở cửa là 05:00
        $endTimeLimit = strtotime('00:00 +1 day'); // Sân đóng cửa lúc 24:00 (00:00 ngày hôm sau)

        $isToday = (date('Y-m-d') === $ngayDat);
        $currentTime = time();

        // Bước nhảy là 30 phút (1800 giây)
        for ($current = $startTime; $current < $endTimeLimit; $current += 1800) {
            $slotStart = $current;
            $slotEnd = $current + ($thoiLuong * 3600);

            // Nếu slot kết thúc vượt quá 23:00 thì bỏ qua
            if ($slotEnd > $endTimeLimit) {
                break;
            }

            // MỚI: Nếu người dùng chọn đặt sân cho hôm nay, bỏ qua các khung giờ đã trôi qua
            if ($isToday && $slotStart <= $currentTime) {
                continue;
            }

            $isOccupied = false;
            foreach ($bookedSlots as $booked) {
                $bookedStart = strtotime($booked['GioBatDau']);
                $bookedEnd = strtotime($booked['GioKetThuc']);

                // Kiểm tra chồng lấn khung giờ
                if ($slotEnd > $bookedStart && $slotStart < $bookedEnd) {
                    $isOccupied = true;
                    break;
                }
            }

            if (!$isOccupied) {
                $availableSlots[] = [
                    'start' => date('H:i', $slotStart),
                    'end' => date('H:i', $slotEnd),
                    'label' => date('H:i', $slotStart) . ' - ' . date('H:i', $slotEnd)
                ];
            }
        }

        $this->json([
            'success' => true,
            'slots' => $availableSlots
        ]);
    }
    
    /**
     * Xử lý AJAX/POST để kiểm tra sân trống và tính giá tạm.
     */
    public function kiemTraVaTinhGia()
    {
        // 1. Lấy dữ liệu từ POST
        $maSan = $_POST['maSan'] ?? null;
        $ngayDat = $_POST['ngayDat'] ?? null;
        $gioBatDau = $_POST['gioBatDau'] ?? null;
        $gioKetThuc = $_POST['gioKetThuc'] ?? null;
        $giaThue = $_POST['giaThue'] ?? 0; 

        // 2. Validate dữ liệu cơ bản
        if (!$maSan || !$ngayDat || !$gioBatDau || !$gioKetThuc) {
            $this->json(['success' => false, 'message' => 'Vui lòng điền đủ thông tin.']);
            return;
        }

        // Kiểm tra khung giờ hợp lệ (5:00 - 24:00)
        $gioBatDauTimestamp = strtotime($gioBatDau);
        $gioKetThucTimestamp = strtotime($gioKetThuc);
        if ($gioKetThuc === '00:00' || $gioKetThuc === '24:00') {
            $gioKetThucTimestamp = strtotime('00:00 +1 day');
        }
        $gioMoCua = strtotime('05:00');
        $gioDongCua = strtotime('00:00 +1 day'); 

        if ($gioBatDauTimestamp < $gioMoCua || $gioKetThucTimestamp > $gioDongCua) {
            $this->json(['success' => false, 'message' => 'Chỉ được phép đặt sân trong khung giờ từ 5:00 sáng đến 24:00 đêm.']);
            return;
        }

        // 3. Kiểm tra trùng lặp
        $isTrung = $this->sanModel->checkSanTrung($maSan, $ngayDat, $gioBatDau, $gioKetThuc);

        if ($isTrung) {
            $this->json(['success' => false, 'message' => 'Sân này đã được đặt trong khoảng thời gian này.']);
            return;
        }

        // 4. Tính toán giá tiền
        // Chuyển đổi giờ thành giây (hoặc timestamp) để tính khoảng thời gian
        $timeStart = strtotime($gioBatDau);
        $timeEnd = strtotime($gioKetThuc);
        if ($gioKetThuc === '00:00' || $gioKetThuc === '24:00') {
            $timeEnd = strtotime('00:00 +1 day');
        }
        $thoiLuongGio = round(($timeEnd - $timeStart) / 3600, 2); // Thời gian thuê (giờ)

        if ($thoiLuongGio <= 0) {
            $this->json(['success' => false, 'message' => 'Thời gian kết thúc phải lớn hơn thời gian bắt đầu.']);
            return;
        }
        if ($thoiLuongGio < 1 ) {
            $this->json(['success' => false, 'message' => 'Thời gian thuê tối thiểu là 1 giờ.']);
            return;
        }

        $tongTienSan = $giaThue * $thoiLuongGio;

        // 5. Trả về kết quả thành công
       $this->json([
        'success' => true,
        'message' => 'Sân còn trống.',
        'tongTienSan' => number_format($tongTienSan), 
        'rawTongTien' => $tongTienSan, // <-- GIÁ TRỊ SỐ GỐC ĐỂ DÙNG TRONG JS VÀ SESSION
        'thoiLuongGio' => $thoiLuongGio
    ]);
    }
    public function luuTamDatSan(){
        $this->checkAuth();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $maSan = $_POST['maSan'] ?? null;
    
    $tenSan = '';
    if ($maSan) {
        $tenSan = $this->sanModel->getTenSanByMaSan($maSan); 
    }
    
    // Lấy dữ liệu từ POST
    $dataDatSan = [
        'maSan' => $_POST['maSan'] ?? null,
        'tenSan' => $tenSan,
        'ngayDat' => $_POST['ngayDat'] ?? null,
        'gioBatDau' => $_POST['gioBatDau'] ?? null,
        'gioKetThuc' => $_POST['gioKetThuc'] ?? null,
        'giaThueMoc' => $_POST['giaThueHienTai'] ?? 0, // Giá thuê cơ bản (từ input ẩn)
        'tongTienSan' => floatval($_POST['tongTienSanTamTinh'] ?? 0), 
        'thoiLuongGio' => floatval($_POST['thoiLuongThueTamTinh'] ?? 0),
        'trangThai' => 'Đã đặt sân',
        'ghiChu' => ''
    ];

    // LƯU DỮ LIỆU ĐẶT SÂN VÀO SESSION
    $_SESSION['datSanTam'] = $dataDatSan;
    unset($_SESSION['thieuKho']);

    header('Location: ' . BASE_URL . 'chon-dich-vu'); 
    exit;
}
public function chonDichVu()
{
    $this->checkAuth();
    
    // Bắt đầu Session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $datSanInfo = $_SESSION['datSanTam'] ?? null;

    if (!$datSanInfo) {
        // Nếu không có dữ liệu Session, chuyển hướng về lại View 1
        header('Location: ' . BASE_URL . 'dat-san');
        exit;
    }
    

    $dichVuList = $this->sanModel->getAllDichVu(); 
    
    // Tạo mảng số lượng tồn kho từ danh sách dịch vụ để truyền sang view
    $soluongTonArr = [];
    foreach ($dichVuList as $dv) {
        $soluongTonArr[$dv['MaDV']] = $dv['SoLuongTon'];
    }

    // Truyền dữ liệu Session và Danh sách Dịch vụ sang View
    $this->view('datsan/chonsan_dichvu', [
        'datSan' => $datSanInfo,
        'dichVuList' => $dichVuList,
        'soluongTon' => $soluongTonArr,
    ]);
}


/**
 * [Controller POST] Lưu chi tiết dịch vụ đã chọn và tính toán tổng hóa đơn cuối cùng.
 * Sau đó chuyển hướng sang View 3.
 */
public function luuTamDichVu()
{
    $this->checkAuth();
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $datSanInfo = $_SESSION['datSanTam'] ?? null;
    if (!$datSanInfo) {
        header('Location: ' . BASE_URL . 'dat-san');
        exit;
    }

    $dichVuPost = $_POST['dichVu'] ?? [];
    
    // --- BẮT ĐẦU: KIỂM TRA TỒN KHO ---
    $isCoDinh = isset($datSanInfo['is_co_dinh']) && $datSanInfo['is_co_dinh'];
    $so_buoi = $isCoDinh ? $datSanInfo['number_of_weeks'] : 1;
    $tenDichVuLookupForCheck = $this->sanModel->getTenDichVuByIDs(array_keys($dichVuPost));
    $thieuKho = [];

    foreach ($dichVuPost as $maDV => $dvData) {
        $soLuongChon = intval($dvData['soLuong'] ?? 0);
        if ($soLuongChon > 0) {
            $soLuongCan = $soLuongChon * $so_buoi;
            $soLuongTon = $this->tonKhoModel->getSoLuongTon($maDV);
            $tenDV = $tenDichVuLookupForCheck[$maDV] ?? 'Dịch vụ không xác định';

            if ($soLuongCan > $soLuongTon) {
                $thieuKho[] = [
                    'tenDV' => $tenDV,
                    'can' => $soLuongCan,
                    'ton' => $soLuongTon
                ];
            }
        }
    }
    $_SESSION['thieuKho'] = $thieuKho;

    if (!empty($thieuKho)) {
        header('Location: ' . BASE_URL . 'chon-dich-vu');
        exit;
    }
    // --- KẾT THÚC: KIỂM TRA TỒN KHO ---

    $maDichVuChon = array_keys($dichVuPost);
    $tenDichVuLookup = $this->sanModel->getTenDichVuByIDs($maDichVuChon);

    $tongTienSan = floatval($datSanInfo['tongTienSan']);
    $tongTienDichVuTheoBuoi = 0;
    $chiTietDichVuChon = [];

    foreach ($dichVuPost as $maDV => $dvData) {
        $soLuong = intval($dvData['soLuong'] ?? 0);
        if ($soLuong > 0) {
            $donGia = floatval($dvData['donGia'] ?? 0);
            $thanhTien = $soLuong * $donGia;
            $tongTienDichVuTheoBuoi += $thanhTien;
            $chiTietDichVuChon[] = [
                'MaDV' => $maDV,
                'TenDV' => $tenDichVuLookup[$maDV] ?? 'Dịch vụ',
                'SoLuong' => $soLuong,
                'DonGia' => $donGia,
                'ThanhTien' => $thanhTien,
            ];
        }
    }

    // Tính toán tổng tiền dựa trên loại đặt sân
    $tongTienDichVuFinal = $isCoDinh ? $tongTienDichVuTheoBuoi * $datSanInfo['number_of_weeks'] : $tongTienDichVuTheoBuoi;
    $tongTienHoaDon = $tongTienSan + $tongTienDichVuFinal;
    
    // Cập nhật session
    $datSanInfo['dichVuChon'] = $chiTietDichVuChon;
    $datSanInfo['tongTienDichVu'] = $tongTienDichVuFinal;
    $datSanInfo['tongTienHoaDon'] = $tongTienHoaDon;
    $_SESSION['datSanTam'] = $datSanInfo;

    header('Location: ' . BASE_URL . 'xac-nhan-thanh-toan'); 
    exit;
}
public function xacNhanThanhToan()
{
    $this->checkAuth();
    // Bắt đầu Session
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $datSanInfo = $_SESSION['datSanTam'] ?? null;

    if (!$datSanInfo || !isset($datSanInfo['tongTienHoaDon'])) {
        // Nếu thiếu dữ liệu, chuyển hướng về lại View 2
        header('Location: ' . BASE_URL . 'chon-dich-vu');
        exit;
    }
    
    // Lấy danh sách Phương Thức Thanh Toán từ Model (Cần có hàm này trong Model)
    $ptttList = $this->sanModel->getAllPhuongThucThanhToan(); 

    // Truyền dữ liệu Session và Danh sách PTTT sang View
    $this->view('datsan/xacnhan_thanhtoan', [
        'datSan' => $datSanInfo,
        'ptttList' => $ptttList
    ]);
}

    public function hoanTatDatSan()
    {
        $this->checkAuth();
        
        $datSanInfo = $_SESSION['datSanTam'] ?? null;
        $maKH = $_SESSION['user_id'] ?? null; 
        $maPT = $_POST['maPT'] ?? null;
        $ghiChuHD = $_POST['ghiChuHoaDon'] ?? ''; 

        // Kiểm tra chi tiết để biết cái nào thiếu
        if (!$datSanInfo) {
            $_SESSION['error_message'] = "Lỗi: Mất thông tin đặt sân trong phiên làm việc. Vui lòng chọn lại sân.";
            header('Location: ' . BASE_URL . 'dat-san');
            exit;
        }
        if (!$maKH) {
            $_SESSION['error_message'] = "Lỗi: Không tìm thấy thông tin khách hàng. Vui lòng đăng nhập lại.";
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }
        if (!$maPT) {
            $_SESSION['error_message'] = "Lỗi: Vui lòng chọn phương thức thanh toán.";
            header('Location: ' . BASE_URL . 'xac-nhan-thanh-toan');
            exit;
        }

        $isCoDinh = isset($datSanInfo['is_co_dinh']) && $datSanInfo['is_co_dinh'];

        // SỬA BUG: Mặc định tất cả là 'Chờ thanh toán' để an toàn cho doanh nghiệp
        // Chỉ khi nào thanh toán MoMo thành công (callback) hoặc Admin xác nhận mới đổi trạng thái
        $trangThaiBanDau = 'Chờ thanh toán';

        // --- KIỂM TRA TỒN KHO ĐÃ ĐƯỢC CHUYỂN LÊN luuTamDichVu() ---
        
        $this->sanModel->beginTransaction();
        try {
            $maDatSan = null;
            if ($isCoDinh) {
                $dates_to_book = $datSanInfo['dates_to_book'];
                foreach ($dates_to_book as $date) {
                    $tongTienSanBuoi = $datSanInfo['tongTienSan'] / count($dates_to_book);
                    $dataDatSanDB = [
                        'MaKH' => $maKH,
                        'MaSan' => $datSanInfo['maSan'],
                        'NgayDat' => $date,
                        'GioBatDau' => $datSanInfo['gioBatDau'],
                        'GioKetThuc' => $datSanInfo['gioKetThuc'],
                        'TongTien' => $tongTienSanBuoi,
                        'TrangThai' => $trangThaiBanDau,
                        'GhiChu' => $datSanInfo['ghiChu'] ?? ''
                    ];
                    
                    $maDatSanID = $this->sanModel->saveDatSan($dataDatSanDB);
                    if (!$maDatSanID) throw new \Exception("Lưu đặt sân thất bại ngày $date");

                    if (!empty($datSanInfo['dichVuChon'])) {
                        foreach ($datSanInfo['dichVuChon'] as $dv) {
                            $this->sanModel->saveChiTietDichVu([
                                'MaDatSan' => $maDatSanID,
                                'MaDV' => $dv['MaDV'],
                                'SoLuong' => $dv['SoLuong'],
                                'DonGia' => $dv['DonGia']
                            ]);
                            $this->tonKhoModel->decreaseSoLuongTon($dv['MaDV'], $dv['SoLuong']);
                        }
                    }

                    $this->sanModel->saveHoaDon([
                        'MaDatSan' => $maDatSanID,
                        'MaBaoCao' => null,
                        'NgayLap' => date('Y-m-d H:i:s'),
                        'TongTien' => $tongTienSanBuoi + ($datSanInfo['tongTienDichVu'] / count($dates_to_book)),
                        'TenPhuongThuc' => $maPT,
                        'GhiChu' => $ghiChuHD
                    ]);
                }
                $maDatSan = "MULTIPLE";
            } else {
                $dataDatSanDB = [
                    'MaKH' => $maKH,
                    'MaSan' => $datSanInfo['maSan'],
                    'NgayDat' => $datSanInfo['ngayDat'],
                    'GioBatDau' => $datSanInfo['gioBatDau'],
                    'GioKetThuc' => $datSanInfo['gioKetThuc'],
                    'TongTien' => $datSanInfo['tongTienSan'],
                    'TrangThai' => $trangThaiBanDau,
                    'GhiChu' => $datSanInfo['ghiChu'] ?? ''
                ];
                
                $maDatSan = $this->sanModel->saveDatSan($dataDatSanDB);
                if (!$maDatSan) throw new \Exception("Lưu đặt sân thất bại");

                if (!empty($datSanInfo['dichVuChon'])) {
                    foreach ($datSanInfo['dichVuChon'] as $dv) {
                        $this->sanModel->saveChiTietDichVu([
                            'MaDatSan' => $maDatSan,
                            'MaDV' => $dv['MaDV'],
                            'SoLuong' => $dv['SoLuong'],
                            'DonGia' => $dv['DonGia']
                        ]);
                        $this->tonKhoModel->decreaseSoLuongTon($dv['MaDV'], $dv['SoLuong']);
                    }
                }

                $this->sanModel->saveHoaDon([
                    'MaDatSan' => $maDatSan,
                    'MaBaoCao' => null,
                    'NgayLap' => date('Y-m-d H:i:s'),
                    'TongTien' => $datSanInfo['tongTienHoaDon'],
                    'TenPhuongThuc' => $maPT,
                    'GhiChu' => $ghiChuHD
                ]);
            }            
            // --- Xử lý MoMo ---
            if ($maPT === 'MoMo') {
                $orderInfo = "Thanh toán đặt sân tại QuanLySVD";
                $amount = (int)$datSanInfo['tongTienHoaDon'];
                $requestId = time() . "";
                $orderId = "BOOKING_" . (isset($maDatSan) ? $maDatSan : "MULTIPLE") . "_" . time(); 
                $bookingReturnUrl = BASE_URL . "datsan/momoReturn";
                
                $result = MoMoPayment::createPayment($orderId, $orderInfo, $amount, $requestId, $bookingReturnUrl);

                if (isset($result['payUrl'])) {
                    $this->sanModel->commit(); 
                    unset($_SESSION['datSanTam']);
                    header('Location: ' . $result['payUrl']);
                    exit;
                } else {
                    $this->sanModel->rollBack(); 
                    $_SESSION['error_message'] = "Lỗi MoMo: " . ($result['message'] ?? 'Không lấy được link thanh toán');
                    header('Location: ' . BASE_URL . 'xac-nhan-thanh-toan');
                    exit;
                }
            }

            $this->sanModel->commit(); 
            
            if (!$isCoDinh && isset($maDatSan)) {
                $this->sendBookingEmail($maDatSan);
            }
            
            // Đánh dấu đã hoàn tất để trang thành công có thể hiển thị
            $_SESSION['dat_san_thanh_cong'] = true;
            unset($_SESSION['datSanTam']);
            header('Location: ' . BASE_URL . 'thanh-cong');
            exit;
        
        } catch (\Exception $e) {
            if ($this->sanModel->inTransaction()) {
                $this->sanModel->rollBack(); 
            }
            error_log("DATABASE TRANSACTION ERROR: " . $e->getMessage());
            $_SESSION['error_message'] = "Lỗi hệ thống khi lưu đơn hàng: " . $e->getMessage();
            header('Location: ' . BASE_URL . 'xac-nhan-thanh-toan');
            exit;
        }
    }
public function loiThanhToan()
{
    // Hiển thị một View đơn giản thông báo lỗi
    $this->view('datsan/loi_thanh_toan', [
        'title' => 'Lỗi Giao Dịch',
        'message' => 'Rất tiếc, đã có lỗi xảy ra trong quá trình xử lý đơn hàng. Vui lòng thử lại hoặc liên hệ hỗ trợ.'
    ]);
}
public function thanhCong()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Kiểm tra xem khách có thực sự vừa đặt sân xong không
    if (!isset($_SESSION['dat_san_thanh_cong']) || $_SESSION['dat_san_thanh_cong'] !== true) {
        header("Location: " . BASE_URL . "home/index");
        exit;
    }

    $maKH = $_SESSION['user_id'] ?? 'TEMP_USER'; 

    $this->view('datsan/thanhcong', [
        'maKH' => $maKH, 
        'isLoggedIn' => isset($_SESSION['user_id']) // Dùng cờ này để biết là session thật hay giả
    ]);
}

public function momoReturn()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $resultCode = $_GET['resultCode'] ?? -1;
    $orderIdFull = $_GET['orderId'] ?? '';
    // orderIdFull có dạng: BOOKING_123_1623456789
    $parts = explode('_', $orderIdFull);
    $maDatSan = $parts[1] ?? 0;

    if ($resultCode == 0) {
        $_SESSION['success_message'] = "Thanh toán đặt sân qua MoMo thành công!";
        
        // Cập nhật trạng thái thành Đã thanh toán
        $this->datSanModel->updateBookingStatus($maDatSan, 'Đã thanh toán');
        
        // Gửi email thông báo
        $this->sendBookingEmail($maDatSan);
        
        // Cho phép truy cập trang thành công
        $_SESSION['dat_san_thanh_cong'] = true;
        
        header('Location: ' . BASE_URL . 'thanh-cong');
    } else {
        $_SESSION['error_message'] = "Giao dịch MoMo không thành công. (Mã lỗi: $resultCode)";
        header('Location: ' . BASE_URL . 'thanh-cong');
    }
    exit;
}


/**
 * [View] Hiển thị form để đặt sân cố định theo tuần/tháng.
 */    public function showDatCoDinhForm()
    {
        $this->checkAuth();
        $san_id = $_GET['san_id'] ?? null;
        if (!$san_id) {
            // Redirect hoặc hiển thị lỗi nếu không có san_id
            header('Location: ' . BASE_URL . 'dat-san');
            exit;
        }
        $this->view('datsan/dat_co_dinh', [
            'title' => 'Đặt Sân Cố Định'
        ]);
    }

        /**

         * [Controller POST] Lưu tạm thông tin đặt sân cố định vào Session và chuyển đến trang chọn dịch vụ.

         */

        public function luuTamDatCoDinh()

        {
            $this->checkAuth();
            if (session_status() == PHP_SESSION_NONE) {

                session_start();

            }

    

            // 1. Lấy và validate dữ liệu

            $san_id = $_POST['san_id'] ?? null;

            $maKH = $_SESSION['user_id'] ?? null;

            $selected_dow = $_POST['ngay_da'] ?? null;

            $gio_bat_dau = $_POST['gio_bat_dau'] ?? null;

            $gio_ket_thuc = $_POST['gio_ket_thuc'] ?? null;

            $thoi_han_thang = (int)($_POST['thoi_han'] ?? 1);

    

            if (!$san_id || !$maKH || !$selected_dow || !$gio_bat_dau || !$gio_ket_thuc) {

                $_SESSION['error_message'] = "Phiên làm việc hết hạn hoặc thiếu thông tin. Vui lòng thử lại.";

                header('Location: ' . BASE_URL . 'datsan/dat-co-dinh?san_id=' . $san_id);

                exit;

            }

    

            // 2. Tính toán lại ngày và giá (tương tự handleKiemTraCoDinh)

            $weekdays = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];

            $weekday_name = $weekdays[$selected_dow];

            $start_date = new \DateTime("next $weekday_name");

            $today = new \DateTime();

            if ($today->format('N') == $selected_dow) {

                $start_date = new \DateTime("this $weekday_name");

            }

    

            $number_of_weeks = $thoi_han_thang * 4;

            $dates_to_book = [];

            for ($i = 0; $i < $number_of_weeks; $i++) {

                $current_date = clone $start_date;

                $current_date->modify("+$i weeks");

                $dates_to_book[] = $current_date->format('Y-m-d');

            }

    

            $san_info = $this->sanModel->getSanById($san_id);

            $gia_thue_per_hour = $san_info['GiaThue'];

            $timeStart = strtotime($gio_bat_dau);
            $timeEnd = strtotime($gio_ket_thuc);
            if ($gio_ket_thuc === '00:00' || $gio_ket_thuc === '24:00') {
                $timeEnd = strtotime('00:00 +1 day');
            }
            $thoiLuongGio = round(($timeEnd - $timeStart) / 3600, 2);
            $gia_moi_buoi = $gia_thue_per_hour * $thoiLuongGio;

            $tong_tien_san = $gia_moi_buoi * $number_of_weeks;

    

            // 3. Đóng gói dữ liệu vào Session

            $dataDatSan = [

                'is_co_dinh' => true,

                'maSan' => $san_id,

                'tenSan' => $san_info['TenSan'],

                'gioBatDau' => $gio_bat_dau,

                'gioKetThuc' => $gio_ket_thuc,

                'thoiLuongGio' => $thoiLuongGio,

                'dates_to_book' => $dates_to_book,

                'number_of_weeks' => $number_of_weeks,

                'tongTienSan' => $tong_tien_san,

                'trangThai' => 'Đã đặt sân',

                'ghiChu' => 'Đặt sân cố định'

            ];

            

            $_SESSION['datSanTam'] = $dataDatSan;

    

            // 4. Chuyển hướng tới trang chọn dịch vụ

            header('Location: ' . BASE_URL . 'chon-dich-vu');

            exit;

        }

    

        /**

         * [AJAX POST] Xử lý kiểm tra lịch và tính giá cho đặt sân cố định.

         */
    public function handleKiemTraCoDinh()
    {
        // 1. Lấy dữ liệu từ POST
        $san_id = $_POST['san_id'] ?? null;
        $selected_dow = $_POST['ngay_da'] ?? null;
        $gio_bat_dau = $_POST['gio_bat_dau'] ?? null;
        $gio_ket_thuc = $_POST['gio_ket_thuc'] ?? null;
        $thoi_han_thang = (int)($_POST['thoi_han'] ?? 1);

        // 2. Validate
        if (!$san_id || !$selected_dow || !$gio_bat_dau || !$gio_ket_thuc) {
            $this->json(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin.']);
            return;
        }
        
        // Kiểm tra khung giờ hợp lệ (5:00 - 24:00)
        $gioBatDauTimestamp = strtotime($gio_bat_dau);
        $gioKetThucTimestamp = strtotime($gio_ket_thuc);
        if ($gio_ket_thuc === '00:00' || $gio_ket_thuc === '24:00') {
            $gioKetThucTimestamp = strtotime('00:00 +1 day');
        }
        $gioMoCua = strtotime('05:00');
        $gioDongCua = strtotime('00:00 +1 day');

        if ($gioBatDauTimestamp < $gioMoCua || $gioKetThucTimestamp > $gioDongCua) {
            $this->json(['success' => false, 'message' => 'Chỉ được phép đặt sân trong khung giờ từ 5:00 sáng đến 24:00 đêm.']);
            return;
        }

        $timeStart = strtotime($gio_bat_dau);
        $timeEnd = strtotime($gio_ket_thuc);
        if ($gio_ket_thuc === '00:00' || $gio_ket_thuc === '24:00') {
            $timeEnd = strtotime('00:00 +1 day');
        }
        if ($timeStart >= $timeEnd) {
            $this->json(['success' => false, 'message' => 'Giờ kết thúc phải sau giờ bắt đầu.']);
            return;
        }

        // 3. Logic tính toán ngày và kiểm tra
        $weekdays = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
        $weekday_name = $weekdays[$selected_dow];
        
        $start_date = new \DateTime("next $weekday_name");
        $today = new \DateTime();
        if ($today->format('N') == $selected_dow) {
            $start_date = new \DateTime("this $weekday_name");
        }

        $number_of_weeks = $thoi_han_thang * 4;
        $conflicting_dates = [];

        for ($i = 0; $i < $number_of_weeks; $i++) {
            $current_date = clone $start_date;
            $current_date->modify("+$i weeks");
            $formatted_date = $current_date->format('Y-m-d');
            
            if ($this->sanModel->checkSanTrung($san_id, $formatted_date, $gio_bat_dau, $gio_ket_thuc)) {
                $conflicting_dates[] = $current_date->format('d-m-Y');
            }
        }

        if (!empty($conflicting_dates)) {
            $this->json([
                'success' => false,
                'message' => "Các ngày sau đã có người đặt: <br>" . implode('<br>', $conflicting_dates)
            ]);
            return;
        }

        // 4. Nếu không trùng, tính giá
        $san_info = $this->sanModel->getSanById($san_id);
        $gia_thue_per_hour = $san_info['GiaThue'];
        $thoiLuongGio = round(($timeEnd - $timeStart) / 3600, 2);
        $gia_moi_buoi = $gia_thue_per_hour * $thoiLuongGio;
        $tong_tien = $gia_moi_buoi * $number_of_weeks;

        $this->json([
            'success' => true,
            'message' => 'Lịch trình hoàn toàn hợp lệ! Bạn có thể tiến hành xác nhận.',
            'total_price_formatted' => number_format($tong_tien),
            'raw_total_price' => $tong_tien
        ]);
        }

        private function sendBookingEmail($maDatSan) {
        $booking = $this->datSanModel->getBookingDetailsByIdForAdmin($maDatSan);
        if (!$booking) return false;

        $khachHang = $this->khachHangModel->getKhachHangById($booking['MaKH']);
        if (!$khachHang || empty($khachHang['Email'])) return false;

        $services = $this->dichVuModel->getServicesByBookingId($maDatSan);

        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($khachHang['Email'], $khachHang['HoTen']);

            $mail->isHTML(true);
            $mail->Subject = 'Xác nhận đặt sân thành công - #' . $maDatSan;

            // Nội dung Email
            $body = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px;'>";
            $body .= "<h2 style='color: #28a745; text-align: center;'>Đặt Sân Thành Công!</h2>";
            $body .= "<p>Chào <strong>{$khachHang['HoTen']}</strong>,</p>";
            $body .= "<p>Cảm ơn bạn đã tin tưởng và sử dụng dịch vụ của <strong>QuanLySVD</strong>. Lịch đặt sân của bạn đã được hệ thống xác nhận.</p>";
            $body .= "<div style='background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            $body .= "<h3 style='margin-top: 0; border-bottom: 2px solid #28a745; padding-bottom: 5px;'>Chi tiết đặt sân:</h3>";
            $body .= "<table style='width: 100%; border-collapse: collapse;'>";
            $body .= "<tr><td style='padding: 5px 0;'><strong>Mã đặt sân:</strong></td><td>#{$maDatSan}</td></tr>";
            $body .= "<tr><td style='padding: 5px 0;'><strong>Sân:</strong></td><td>{$booking['TenSan']}</td></tr>";
            $body .= "<tr><td style='padding: 5px 0;'><strong>Ngày đặt:</strong></td><td>" . date('d/m/Y', strtotime($booking['NgayDat'])) . "</td></tr>";
            $body .= "<tr><td style='padding: 5px 0;'><strong>Thời gian:</strong></td><td>{$booking['GioBatDau']} - {$booking['GioKetThuc']}</td></tr>";
            $body .= "<tr><td style='padding: 5px 0;'><strong>Tổng tiền:</strong></td><td style='color: #d9534f; font-weight: bold;'>" . number_format($booking['TongTienHoaDon']) . " VNĐ</td></tr>";
            $pttt = !empty($booking['TenPhuongThuc']) ? $booking['TenPhuongThuc'] : 'Chưa xác định';
            $body .= "<tr><td style='padding: 5px 0;'><strong>Thanh toán:</strong></td><td><strong style='color: #007bff;'>{$pttt}</strong></td></tr>";
            $body .= "</table>";
            $body .= "</div>";

            if (!empty($services)) {
                $body .= "<h3>Dịch vụ đính kèm:</h3>";
                $body .= "<ul>";
                foreach ($services as $sv) {
                    $body .= "<li>{$sv['TenDV']} x {$sv['SoLuong']}</li>";
                }
                $body .= "</ul>";
            }

            $body .= "<p style='font-style: italic; color: #666;'>Ghi chú: Vui lòng đến trước giờ đặt 15 phút để làm thủ tục nhận sân.</p>";
            $body .= "<hr style='border: 0; border-top: 1px solid #eee; margin: 20px 0;'>";
            $body .= "<p style='text-align: center; color: #888; font-size: 12px;'>Đây là email tự động, vui lòng không trả lời email này.<br>&copy; " . date('Y') . " QuanLySVD. All rights reserved.</p>";
            $body .= "</div>";

            $mail->Body = $body;
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed for Booking #$maDatSan: " . $mail->ErrorInfo);
            return false;
        }
        }
        }