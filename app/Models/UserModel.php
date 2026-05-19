<?php
namespace App\Models;

use PDO;
use Exception;
use PDOException;

require_once 'Database.php';

class UserModel extends Database 
{
    private $taiKhoanTable = 'taikhoan';
    private $khachHangTable = 'khachhang';
    private $resetTable = 'passwordreset';

    public function registerUser($username, $password, $hoTen, $sdt, $email, $diaChi) 
    {
        $this->beginTransaction();

        try {
            // 1. INSERT vào bảng TaiKhoan
            $queryTaiKhoan = "INSERT INTO {$this->taiKhoanTable} (TenDangNhap, MatKhau, LoaiTK) VALUES (:username, :password, 'user')";
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $this->query($queryTaiKhoan, [
                ':username' => $username,
                ':password' => $hashedPassword
            ]);

            $maTK = $this->lastInsertId();

            if (!$maTK) {
                throw new Exception("Lỗi: Không lấy được MaTK vừa tạo.");
            }

            // 2. INSERT vào bảng KhachHang
            $queryKhachHang = "INSERT INTO {$this->khachHangTable} (HoTen, SDT, Email, DiaChi, MaTK) VALUES (:hoTen, :sdt, :email, :diaChi, :maTK)";
            $this->query($queryKhachHang, [
                ':hoTen' => $hoTen,
                ':sdt' => $sdt,
                ':email' => $email,
                ':diaChi' => $diaChi,
                ':maTK' => $maTK
            ]);

            $this->commit();
            return true;

        } catch (PDOException | Exception $e) {
            $this->rollBack();
            error_log("Lỗi đăng ký người dùng: " . $e->getMessage());
            return false; 
        }
    }
    
    public function getUserByUsername($username) 
    {
        $sql = "
            SELECT tk.*, kh.MaKH, kh.HoTen, kh.SDT, kh.Email, kh.DiaChi
            FROM {$this->taiKhoanTable} tk
            LEFT JOIN {$this->khachHangTable} kh ON tk.MaTK = kh.MaTK
            WHERE tk.TenDangNhap = :username
            LIMIT 1
        ";
        try {
            $stmt = $this->query($sql, [':username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn người dùng: " . $e->getMessage());
            return null;
        }
    }

    public function getUserByUsernameOrEmail($loginId) 
    {
        $sql = "
            SELECT tk.*, kh.MaKH, kh.HoTen, kh.SDT, kh.Email, kh.DiaChi
            FROM {$this->taiKhoanTable} tk
            LEFT JOIN {$this->khachHangTable} kh ON tk.MaTK = kh.MaTK
            WHERE tk.TenDangNhap = :loginId OR kh.Email = :loginId
            LIMIT 1
        ";
        try {
            $stmt = $this->query($sql, [':loginId' => $loginId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Lỗi truy vấn người dùng: " . $e->getMessage());
            return null;
        }
    }

    public function getUserByEmail($email) 
    {
        $sql = "
            SELECT kh.MaKH, tk.MaTK, tk.TenDangNhap 
            FROM {$this->khachHangTable} kh 
            JOIN {$this->taiKhoanTable} tk ON kh.MaTK = tk.MaTK 
            WHERE kh.Email = :email
        ";
        $stmt = $this->query($sql, [':email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getUserByMaKH($maKH) 
    {
        $sql = "
            SELECT kh.*, tk.TenDangNhap, tk.LoaiTK, tk.MatKhau, tk.MaTK 
            FROM {$this->khachHangTable} kh 
            JOIN {$this->taiKhoanTable} tk ON kh.MaTK = tk.MaTK 
            WHERE kh.MaKH = :maKH
        ";
        $stmt = $this->query($sql, [':maKH' => $maKH]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function deleteExistingResetTokenByMaKH($maKH) 
    {
        $sql = "DELETE FROM {$this->resetTable} WHERE MaKH = :maKH";
        $this->query($sql, [':maKH' => $maKH]);
    }

    public function savePasswordResetToken($maKH, $token, $expiry) 
    {
        $this->deleteExistingResetTokenByMaKH($maKH); 

        $sql = "INSERT INTO {$this->resetTable} (MaKH, token, expires_at) VALUES (:maKH, :token, :expiry)";
        return $this->query($sql, [
            ':maKH' => $maKH,
            ':token' => $token,
            ':expiry' => $expiry 
        ]);
    }

    public function validateResetToken($token) 
    {
        $sql = "SELECT MaKH FROM {$this->resetTable} WHERE token = :token AND expires_at >= NOW()";
        $stmt = $this->query($sql, [':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    public function getUserByResetToken($token) 
    {
        $resetInfo = $this->validateResetToken($token);
        if (!$resetInfo) return null;

        return $this->getUserByMaKH($resetInfo['MaKH']); 
    }

    public function deleteResetToken($token) 
    {
        $sql = "DELETE FROM {$this->resetTable} WHERE token = :token";
        $this->query($sql, [':token' => $token]);
    }

    public function updatePassword($maTK, $newPassword) 
    {
        $sql = "UPDATE {$this->taiKhoanTable} SET MatKhau = :password WHERE MaTK = :maTK";
        return $this->query($sql, [
            ':password' => password_hash($newPassword, PASSWORD_DEFAULT),
            ':maTK' => $maTK
        ]);
    }

    public function checkPassword($maKH, $password) 
    {
        $user = $this->getUserByMaKH($maKH);
        if ($user && isset($user['MatKhau'])) {
            return password_verify($password, $user['MatKhau']);
        }
        return false;
    }

    public function updateProfile($maKH, $data) 
    {
        $sql = "UPDATE {$this->khachHangTable} SET HoTen = :HoTen, SDT = :SDT, Email = :Email, DiaChi = :DiaChi WHERE MaKH = :MaKH";
        try {
            $this->query($sql, [
                ':HoTen' => $data['HoTen'],
                ':SDT' => $data['SDT'],
                ':Email' => $data['Email'],
                ':DiaChi' => $data['DiaChi'],
                ':MaKH' => $maKH
            ]);
            return true;
        } catch (PDOException $e) {
            error_log("Lỗi cập nhật hồ sơ: " . $e->getMessage());
            return false;
        }
    }

    // --- QUẢN LÝ TÀI KHOẢN (CHO OWNER) ---

    public function getAllStaff()
    {
        $sql = "SELECT MaTK, TenDangNhap, LoaiTK FROM {$this->taiKhoanTable} WHERE LoaiTK = 'admin' OR LoaiTK = 'owner' ORDER BY LoaiTK ASC";
        return $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createAccount($username, $password, $role)
    {
        $sql = "INSERT INTO {$this->taiKhoanTable} (TenDangNhap, MatKhau, LoaiTK) VALUES (:username, :password, :role)";
        try {
            return $this->query($sql, [
                ':username' => $username,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':role' => $role
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi tạo tài khoản: " . $e->getMessage());
            return false;
        }
    }

    public function deleteAccount($maTK)
    {
        // Không cho phép xóa chính mình (logic này sẽ được check ở Controller)
        // Lưu ý: Nếu có bảng liên quan (như khachhang), cần xử lý xóa hoặc set null ngoại lai.
        // Ở đây ta chỉ xóa tài khoản admin/owner nên ít ảnh hưởng đến bảng khách hàng (thường là 'user')
        $sql = "DELETE FROM {$this->taiKhoanTable} WHERE MaTK = :maTK";
        return $this->query($sql, [':maTK' => $maTK]);
    }
}
?>