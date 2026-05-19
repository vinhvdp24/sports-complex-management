<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class NhapHangModel extends Database {
    
    public function insertNhapHang($maDV, $soLuongNhap, $ghiChu) {
        $ngayNhap = date('Y-m-d H:i:s');
        $query = "INSERT INTO nhaphang (MaDV, SoLuongNhap, NgayNhap, GhiChu) VALUES (:maDV, :soLuongNhap, :ngayNhap, :ghiChu)";
        $params = [
            ':maDV' => $maDV,
            ':soLuongNhap' => $soLuongNhap,
            ':ngayNhap' => $ngayNhap,
            ':ghiChu' => $ghiChu
        ];
        $stmt = $this->query($query, $params);
        return $stmt->rowCount() > 0;
    }
}
