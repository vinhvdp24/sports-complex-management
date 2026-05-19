<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class KhachHangModel extends Database {
    
    /**
     * Lấy tất cả khách hàng từ database.
     * @return array Danh sách khách hàng.
     */
    public function getAllKhachHang() {
        $query = "SELECT MaKH, HoTen, SDT, Email FROM khachhang ORDER BY HoTen";
        $stmt = $this->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tìm kiếm khách hàng theo tên.
     * @param string $searchTerm Tên khách hàng cần tìm.
     * @return array Danh sách khách hàng phù hợp.
     */
    public function searchKhachHang($searchTerm) {
        $query = "SELECT MaKH, HoTen, SDT, Email FROM khachhang WHERE HoTen LIKE :searchTerm ORDER BY HoTen";
        $params = [':searchTerm' => '%' . $searchTerm . '%'];
        $stmt = $this->query($query, $params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông tin chi tiết của một khách hàng bằng ID.
     * @param int $maKH ID của khách hàng.
     * @return array|false Mảng thông tin khách hàng hoặc false nếu không tìm thấy.
     */
    public function getKhachHangById($maKH) {
        if (!$maKH) return false;
        
        $sql = "SELECT * FROM khachhang WHERE MaKH = :maKH";
        try {
            $stmt = $this->query($sql, [':maKH' => $maKH]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Lỗi khi lấy khách hàng theo ID '$maKH': " . $e->getMessage());
            return false;
        }
    }
}
