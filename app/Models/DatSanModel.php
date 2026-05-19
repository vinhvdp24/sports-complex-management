<?php
namespace App\Models;
use PDO;
    
require_once 'Database.php';
    
         class DatSanModel {
                 private $conn;
                 private $table = 'datsan'; // Tên bảng đặt sân của bạn
   
           public function __construct() {
                    $database = new Database();
                    $this->conn = $database->connect();
                }

    // --- Các hàm phục vụ Admin ---

    /**
     * Lấy danh sách đặt sân trong khoảng ngày (startDate đến endDate)
     */
    public function getBookingsByDateRange($startDate, $endDate)
    {
        $query = "SELECT ds.*, kh.HoTen as TenKhachHang, s.TenSan, hd.TenPhuongThuc
                  FROM " . $this->table . " ds
                  JOIN khachhang kh ON ds.MaKH = kh.MaKH
                  JOIN san s ON ds.MaSan = s.MaSan
                  LEFT JOIN hoadon hd ON ds.MaDatSan = hd.MaDatSan
                  WHERE ds.NgayDat BETWEEN :startDate AND :endDate
                  ORDER BY ds.NgayDat DESC, ds.GioBatDau DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':startDate', $startDate);
            $stmt->bindParam(':endDate', $endDate);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getBookingsByDateRange: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết một lịch đặt sân dựa trên ID
     */
    public function getBookingDetailsByIdForAdmin($maDatSan)
    {
        $query = "SELECT ds.*, kh.HoTen as TenKhachHang, kh.SDT, kh.Email, s.TenSan, s.GiaThue, 
                         hd.NgayLap, hd.TongTien as TongTienHoaDon, hd.TenPhuongThuc, hd.GhiChu as GhiChuHoaDon
                  FROM " . $this->table . " ds
                  JOIN khachhang kh ON ds.MaKH = kh.MaKH
                  JOIN san s ON ds.MaSan = s.MaSan
                  LEFT JOIN hoadon hd ON ds.MaDatSan = hd.MaDatSan
                  WHERE ds.MaDatSan = :maDatSan";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maDatSan', $maDatSan);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getBookingDetailsByIdForAdmin: " . $e->getMessage());
            return false;
        }
    }

    public function huyLichDat($maDatSan, $maKH, $role = 'Khách hàng')
    {
        $trangThai = "Đã hủy bởi " . $role;
        $ghiChu = "Đã hủy bởi " . $role;

        try {
            $this->conn->beginTransaction();
            
            // Cập nhật trạng thái trong bảng datsan
            if ($maKH) {
                $queryDatSan = "UPDATE " . $this->table . " SET TrangThai = :trangThai WHERE MaDatSan = :maDatSan AND MaKH = :maKH";
                $stmtDatSan = $this->conn->prepare($queryDatSan);
                $stmtDatSan->bindParam(':trangThai', $trangThai);
                $stmtDatSan->bindParam(':maDatSan', $maDatSan);
                $stmtDatSan->bindParam(':maKH', $maKH);
            } else {
                $queryDatSan = "UPDATE " . $this->table . " SET TrangThai = :trangThai WHERE MaDatSan = :maDatSan";
                $stmtDatSan = $this->conn->prepare($queryDatSan);
                $stmtDatSan->bindParam(':trangThai', $trangThai);
                $stmtDatSan->bindParam(':maDatSan', $maDatSan);
            }
            $stmtDatSan->execute();

            if ($stmtDatSan->rowCount() > 0) {
                // Cập nhật ghi chú trong bảng hoadon
                $queryHoaDon = "UPDATE hoadon SET GhiChu = :ghiChu WHERE MaDatSan = :maDatSan";
                $stmtHoaDon = $this->conn->prepare($queryHoaDon);
                $stmtHoaDon->bindParam(':ghiChu', $ghiChu);
                $stmtHoaDon->bindParam(':maDatSan', $maDatSan);
                $stmtHoaDon->execute();

                $this->conn->commit();
                return true;
            } else {
                $this->conn->rollBack();
                return false;
            }
        } catch (\PDOException $e) {
            $this->conn->rollBack();
            error_log("Error in huyLichDat: " . $e->getMessage());
            return false;
        }
    }

    public function getBookingCountByDateRange($startDate, $endDate)
    {
        // 1. Get pitch bookings
        $query = "SELECT NgayDat, COUNT(*) as booking_count 
                  FROM datsan 
                  WHERE NgayDat BETWEEN :startDate AND :endDate 
                  GROUP BY NgayDat";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([':startDate' => $startDate, ':endDate' => $endDate]);
        $pitchBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 2. Combine or process as needed (e.g., if you have other types of bookings)
        // Hiện tại, chúng ta chỉ trả về định dạng đặt sân
        $mergedData = [];
        
        // Populate with pitch bookings
        foreach ($pitchBookings as $row) {
            $mergedData[$row['NgayDat']] = (int)$row['booking_count'];
        }

        // 3. Ensure all dates in range are present with 0 if no bookings (optional but good for charts)
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $interval = new \DateInterval('P1D');
        $period = new \DatePeriod($start, $interval, $end->modify('+1 day'));

        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            if (!isset($mergedData[$d])) {
                $mergedData[$d] = 0;
            }
        }
        ksort($mergedData);

        // 4. Format the final array for the chart
        $finalData = [];
        foreach ($mergedData as $date => $count) {
            $finalData[] = [
                'NgayDat' => $date,
                'booking_count' => $count
            ];
        }

        return $finalData;
    }
           
    public function getCancelledRevenue($startDate = null, $endDate = null)
    {
        $query = "SELECT SUM(TongTien) AS total_cancelled_revenue FROM " . $this->table . " WHERE TrangThai LIKE 'Đã hủy%'";
        $params = [];
        if ($startDate && $endDate) {
            $query .= " AND NgayDat BETWEEN :startDate AND :endDate";
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate;
        }

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (float)($result['total_cancelled_revenue'] ?? 0);
        } catch (\PDOException $e) {
            error_log("Error in getCancelledRevenue: " . $e->getMessage());
            return 0;
        }
    }

    public function getBookedSlots($maSan, $ngayDat)
    {
        $query = "SELECT GioBatDau, GioKetThuc FROM " . $this->table . " 
                  WHERE MaSan = :maSan AND NgayDat = :ngayDat AND TrangThai NOT LIKE 'Đã hủy%'";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maSan', $maSan);
            $stmt->bindParam(':ngayDat', $ngayDat);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching booked slots: " . $e->getMessage());
            return [];
        }
    }

    public function updateBookingStatus($maDatSan, $status)
    {
        $query = "UPDATE " . $this->table . " SET TrangThai = :status WHERE MaDatSan = :maDatSan";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':maDatSan', $maDatSan);
            return $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Error updating booking status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy lịch sử đặt sân của một khách hàng cụ thể
     */
    public function getLichSuDatSanByMaKH($maKH)
    {
        $query = "SELECT ds.*, s.TenSan, hd.TongTien as TongTienHoaDon
                  FROM " . $this->table . " ds
                  JOIN san s ON ds.MaSan = s.MaSan
                  LEFT JOIN hoadon hd ON ds.MaDatSan = hd.MaDatSan
                  WHERE ds.MaKH = :maKH
                  ORDER BY ds.NgayDat DESC, ds.GioBatDau DESC";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maKH', $maKH);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getLichSuDatSanByMaKH: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy chi tiết đặt sân cho phía khách hàng
     */
    public function getBookingDetailsById($maDatSan, $maKH)
    {
        $query = "SELECT ds.*, s.TenSan, s.GiaThue, 
                         hd.NgayLap, hd.TongTien as TongTienHoaDon, hd.TenPhuongThuc, hd.GhiChu as GhiChuHoaDon
                  FROM " . $this->table . " ds
                  JOIN san s ON ds.MaSan = s.MaSan
                  LEFT JOIN hoadon hd ON ds.MaDatSan = hd.MaDatSan
                  WHERE ds.MaDatSan = :maDatSan AND ds.MaKH = :maKH";
        
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':maDatSan', $maDatSan);
            $stmt->bindParam(':maKH', $maKH);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error in getBookingDetailsById: " . $e->getMessage());
            return false;
        }
    }
}
 ?>