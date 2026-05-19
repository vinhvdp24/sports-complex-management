<?php
namespace App\Models;


require_once __DIR__ . '/Database.php'; 


use App\Models\Database;


class DichVuModel extends Database
{
    private $conn;
    private $table = 'dichvu';

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function countTotalDichVu()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->query($sql); 
        return (int) $stmt->fetchColumn(); 
    }

    public function getAllDichVu()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY TenDV";
        $stmt = $this->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function getDichVuById($maDV)
    {
        $sql = "SELECT * FROM {$this->table} WHERE MaDV = :maDV";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':maDV' => $maDV]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function addDichVu($data)
    {
        $sql = "INSERT INTO {$this->table} (TenDV, DonGia, MoTa) VALUES (:TenDV, :DonGia, :MoTa)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':TenDV' => $data['TenDV'],
            ':DonGia' => $data['DonGia'],
            ':MoTa' => $data['MoTa']
        ]);
    }

    public function updateDichVu($maDV, $data)
    {
        $sql = "UPDATE {$this->table} SET TenDV = :TenDV, DonGia = :DonGia, MoTa = :MoTa WHERE MaDV = :MaDV";
        $params = [
            ':TenDV' => $data['TenDV'],
            ':DonGia' => $data['DonGia'],
            ':MoTa' => $data['MoTa'],
            ':MaDV' => $maDV
        ];
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteDichVu($maDV)
    {
        $sql = "DELETE FROM {$this->table} WHERE MaDV = :maDV";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':maDV' => $maDV]);
    }

    public function getServicesByBookingId($maDatSan) {
        $query = "SELECT dv.TenDV, ctdv.SoLuong, ctdv.DonGia
                  FROM chitietdichvu ctdv
                  JOIN dichvu dv ON ctdv.MaDV = dv.MaDV
                  WHERE ctdv.MaDatSan = :maDatSan";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':maDatSan', $maDatSan);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getServiceRevenue($startDate = null, $endDate = null) {
        $query = "SELECT SUM(TongTienDichVu) AS total_service_revenue FROM baocao";
        $params = [];
        if ($startDate && $endDate) {
            $query .= " WHERE NgayBaoCao BETWEEN :startDate AND :endDate";
            $params[':startDate'] = $startDate;
            $params[':endDate'] = $endDate; // Corrected this line
        }
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result['total_service_revenue'] ?? 0;
    }
}