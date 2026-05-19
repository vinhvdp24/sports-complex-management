<?php
namespace App\Models;

use App\Models\Database;
use PDO;

class TonKhoModel extends Database {
    
    public function getSoLuongTon($maDV) {
        $query = "SELECT SoLuongTon FROM dichvu WHERE MaDV = :maDV";
        $params = [':maDV' => $maDV];
        $stmt = $this->query($query, $params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['SoLuongTon'] : 0;
    }

    public function decreaseSoLuongTon($maDV, $soLuongGiam) {
        $query = "UPDATE dichvu SET SoLuongTon = SoLuongTon - :soLuongGiam WHERE MaDV = :maDV";
        $params = [
            ':soLuongGiam' => $soLuongGiam,
            ':maDV' => $maDV
        ];
        $stmt = $this->query($query, $params);
        return $stmt->rowCount() > 0;
    }
    
    public function increaseSoLuongTon($maDV, $soLuongNhap) {
        $query = "UPDATE dichvu SET SoLuongTon = SoLuongTon + :soLuongNhap WHERE MaDV = :maDV";
        $params = [
            ':soLuongNhap' => $soLuongNhap,
            ':maDV' => $maDV
        ];
        $stmt = $this->query($query, $params);
        return $stmt->rowCount() > 0;
    }

    public function getTonKhoDichVu() {
        $query = "SELECT TenDV, SoLuongTon FROM dichvu ORDER BY TenDV";
        $stmt = $this->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
