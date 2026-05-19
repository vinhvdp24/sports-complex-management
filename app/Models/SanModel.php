<?php
namespace App\Models;

require_once __DIR__ . '/Database.php'; 

use PDO;
use PDOException;

class SanModel extends Database
{
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getTenDichVuByIDs(array $maDichVuArray)
    {
        if (empty($maDichVuArray)) return [];
        $placeholders = implode(',', array_fill(0, count($maDichVuArray), '?'));
        $sql = "SELECT MaDV, TenDV FROM dichvu WHERE MaDV IN ($placeholders)";
        
        try {
            $stmt = $this->query($sql, $maDichVuArray);
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) {
            error_log("Lỗi tra cứu tên DV: " . $e->getMessage());
            return [];
        }
    }

    public function getTenSanByMaSan($maSan)
    {
        $sql = "SELECT TenSan FROM san WHERE MaSan = :maSan";
        try {
            $stmt = $this->query($sql, [':maSan' => $maSan]);
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Lỗi lấy tên sân by MaSan=$maSan: " . $e->getMessage());
            return false;
        }
    }

    public function getAllSan()
    {
        $sql = "SELECT s.MaSan, s.TenSan, s.GiaThue, s.TinhTrang, s.MoTa, ls.TenLoai, s.MaLoai
                FROM san s 
                JOIN loaisan ls ON s.MaLoai = ls.MaLoai";
        try {
            $stmt = $this->query($sql); 
            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            error_log("Lỗi lấy tất cả sân: " . $e->getMessage());
            return [];
        }
    }

    public function getSanByLoai($maLoai)
    {
        $sql = "SELECT s.MaSan, s.TenSan, s.GiaThue, s.TinhTrang, s.MoTa, ls.TenLoai, s.MaLoai
                FROM san s 
                JOIN loaisan ls ON s.MaLoai = ls.MaLoai
                WHERE s.MaLoai = ?";
        try {
            $stmt = $this->query($sql, [$maLoai]); 
            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            error_log("Lỗi lấy sân theo loại $maLoai: " . $e->getMessage());
            return [];
        }
    }

    public function getAllLoaiSan()
    {
        $sql = "SELECT * FROM loaisan";
        try {
            $stmt = $this->query($sql); 
            return $stmt->fetchAll(); 
        } catch (PDOException $e) {
            error_log("Lỗi lấy danh sách loại sân: " . $e->getMessage());
            return [];
        }
    }

    public function checkSanTrung($maSan, $ngayDat, $gioBatDau, $gioKetThuc)
    {
        $start_time = $ngayDat . ' ' . $gioBatDau;
        $end_time = $ngayDat . ' ' . $gioKetThuc;

        $sql = "SELECT COUNT(*) FROM datsan WHERE MaSan = :maSan AND NgayDat = :ngayDat AND (:end_time > CONCAT(NgayDat, ' ', GioBatDau) AND :start_time < CONCAT(NgayDat, ' ', GioKetThuc))";
        $params = [
            ':maSan' => $maSan,
            ':ngayDat' => $ngayDat,
            ':start_time' => $start_time,
            ':end_time' => $end_time
        ];

        try {
            $stmt = $this->query($sql, $params);
            $result = $stmt->fetchColumn();
            return $result > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra sân trùng: " . $e->getMessage());
            return true;
        }
    }

    public function getAllDichVu()
    {
        $sql = "SELECT MaDV, TenDV, DonGia, MoTa, SoLuongTon FROM dichvu ORDER BY TenDV";
        try {
            $stmt = $this->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Lỗi lấy tất cả dịch vụ: " . $e->getMessage());
            return [];
        }
    }
    public function getAllPhuongThucThanhToan()
    {
        return [
            ['MaPT' => 'Thanh toán tại sân', 'TenPT' => 'Thanh toán tại sân'],
            ['MaPT' => 'MoMo', 'TenPT' => 'Thanh toán qua Ví MoMo']
        ];
    }

    public function saveDatSan($data)
    {
        $sql = "INSERT INTO datsan (MaKH, MaSan, NgayDat, GioBatDau, GioKetThuc, TongTien, TrangThai, GhiChu) VALUES (:maKH, :maSan, :ngayDat, :gioBatDau, :gioKetThuc, :tongTien, :trangThai, :ghiChu)";
        $params = [
            ':maKH' => $data['MaKH'], ':maSan' => $data['MaSan'], ':ngayDat' => $data['NgayDat'],
            ':gioBatDau' => $data['GioBatDau'], ':gioKetThuc' => $data['GioKetThuc'], ':tongTien' => $data['TongTien'],
            ':trangThai' => $data['TrangThai'], ':ghiChu' => $data['GhiChu']
        ];

        try {
            $this->query($sql, $params);
            return $this->lastInsertId(); 
        } catch (PDOException $e) {
            error_log("Lỗi INSERT DatSan: " . $e->getMessage());
            return false;
        }
    }

    public function saveChiTietDichVu($data)
    {
        $sql = "INSERT INTO chitietdichvu (MaDatSan, MaDV, SoLuong, DonGia) VALUES (:maDatSan, :maDV, :soLuong, :donGia)";
        $params = [
            ':maDatSan' => $data['MaDatSan'], ':maDV' => $data['MaDV'],
            ':soLuong' => $data['SoLuong'], ':donGia' => $data['DonGia'],
        ];

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->rowCount() > 0; 
        } catch (PDOException $e) {
            error_log("Lỗi INSERT ChiTietDichVu: " . $e->getMessage());
            return false;
        }
    }

    public function saveBaoCao($data)
    {
        $sql = "INSERT INTO baocao (NgayBaoCao, TongTienSan, TongTienDichVu) VALUES (:ngayBaoCao, :tongTienSan, :tongTienDichVu)";
        $params = [
            ':ngayBaoCao' => $data['NgayBaoCao'],
            ':tongTienSan' => $data['TongTienSan'],
            ':tongTienDichVu' => $data['TongTienDichVu']
        ];

        try {
            $this->query($sql, $params);
            return $this->lastInsertId(); 
        } catch (PDOException $e) {
            error_log("Lỗi INSERT BaoCao: " . $e->getMessage());
            return false;
        }
    }

    public function saveHoaDon($data)
    {
        $sql = "INSERT INTO hoadon (MaDatSan, NgayLap, TongTien, GhiChu, MaBaoCao, TenPhuongThuc) VALUES (:maDatSan, :ngayLap, :tongTien, :ghiChu, :maBaoCao, :tenPhuongThuc)";
        $params = [
            ':maDatSan' => $data['MaDatSan'], ':maBaoCao' => $data['MaBaoCao'],
            ':ngayLap' => $data['NgayLap'], ':tongTien' => $data['TongTien'],
            ':tenPhuongThuc' => $data['TenPhuongThuc'], ':ghiChu' => $data['GhiChu']
        ];

        try {
            $this->query($sql, $params);
            return $this->lastInsertId(); 
        } catch (PDOException $e) {
            error_log("Lỗi INSERT HoaDon: " . $e->getMessage());
            return false;
        }
    }

    public function countTotalSan()
    {
        $sql = "SELECT COUNT(*) FROM san";
        try {
            $stmt = $this->query($sql); 
            return (int) $stmt->fetchColumn(); 
        } catch (PDOException $e) {
            error_log("Lỗi đếm tổng số sân: " . $e->getMessage());
            return 0;
        }
    }

    public function insertSan(array $data) 
    {
        $sql = "INSERT INTO san (MaSan, TenSan, MaLoai, GiaThue, TinhTrang, MoTa) VALUES (?, ?, ?, ?, ?, ?)";
        $params = [$data['MaSan'], $data['TenSan'], $data['MaLoai'], $data['GiaThue'], $data['TinhTrang'], $data['MoTa']];
        try {
            $this->query($sql, $params);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi thêm sân: " . $e->getMessage());
            return false;
        }
    }

    public function hasBookings($maSan)
    {
        $sql = "SELECT COUNT(*) FROM datsan WHERE MaSan = ?";
        try {
            $stmt = $this->query($sql, [$maSan]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Lỗi kiểm tra booking của sân $maSan: " . $e->getMessage());
            return false;
        }
    }

    public function deleteSan($maSan)
    {
        $sql = "DELETE FROM san WHERE MaSan = ?";
        try {
            $stmt = $this->query($sql, [$maSan]); 
            return $stmt->rowCount() > 0; 
        } catch (PDOException $e) {
            error_log("Lỗi xóa sân (Mã $maSan): " . $e->getMessage());
            return false; 
        }
    }

    public function getSanById($maSan)
    {
        $sql = "SELECT * FROM san WHERE MaSan = ?";
        try {
            $stmt = $this->query($sql, [$maSan]);
            return $stmt->fetch(PDO::FETCH_ASSOC); 
        } catch (PDOException $e) {
            error_log("Lỗi lấy sân by id=$maSan: " . $e->getMessage());
            return false;
        }
    }

    public function updateSan($maSan, array $data)
    {
        $setClauses = [];
        $params = [];
        foreach ($data as $key => $value) {
            $setClauses[] = "$key = ?";
            $params[] = $value;
        }
        $sql = "UPDATE san SET " . implode(', ', $setClauses) . " WHERE MaSan = ?";
        $params[] = $maSan;

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->rowCount() > 0; 
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật sân (Mã $maSan): " . $e->getMessage());
            return false;
        }
    }

    public function getPitchRevenue($startDate = null, $endDate = null) {
        $query = "SELECT SUM(TongTien) AS total_pitch_revenue FROM datsan WHERE TrangThai = 'Đã đặt sân'";
        $params = [];
        if ($startDate && $endDate) {
            $query .= " AND NgayDat BETWEEN :startDate AND :endDate";
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate;
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_pitch_revenue'] ?? 0;
    }
}