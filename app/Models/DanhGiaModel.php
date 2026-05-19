<?php
namespace App\Models;

require_once 'Database.php';

class DanhGiaModel {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    /**
     * Adds a new review to the database.
     *
     * @param int $maKH Customer ID
     * @param string $maSan Pitch ID
     * @param int $maDatSan Booking ID
     * @param int $diem Rating score (1-5)
     * @param string $noiDung Review content
     * @return bool True on success, false on failure.
     */
    public function addReview(int $maKH, string $maSan, int $maDatSan, int $diem, string $noiDung): bool {
        // Đầu tiên, kiểm tra xem đã có đánh giá cho đơn đặt này chưa.
        if ($this->hasReviewForBooking($maDatSan)) {
            $_SESSION['error_message'] = "Bạn đã đánh giá lịch đặt này rồi.";
            return false;
        }

        $sql = "INSERT INTO danhgia (MaKH, MaSan, MaDatSan, Diem, NoiDung, NgayDanhGia) VALUES (:maKH, :maSan, :maDatSan, :diem, :noiDung, NOW())";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':maKH' => $maKH,
                ':maSan' => $maSan,
                ':maDatSan' => $maDatSan,
                ':diem' => $diem,
                ':noiDung' => $noiDung
            ]);
        } catch (\PDOException $e) {
            // It's possible the MaDatSan column doesn't exist yet.
            // Provide a more specific error message if that's the case.
            if ($e->getCode() == '42S22') { // Column not found
                 error_log("SQL Error: " . $e->getMessage());
                 $_SESSION['error_message'] = "Lỗi cơ sở dữ liệu: Cột 'MaDatSan' có thể chưa được thêm vào bảng 'danhgia'. Vui lòng liên hệ quản trị viên.";
            } else {
                 error_log("SQL Error: " . $e->getMessage());
                 $_SESSION['error_message'] = "Lỗi cơ sở dữ liệu khi thêm đánh giá.";
            }
            return false;
        }
    }

    /**
     * Checks if a review for a specific booking has already been submitted.
     *
     * @param int $maDatSan Booking ID
     * @return bool True if a review exists, false otherwise.
     */
    public function hasReviewForBooking(int $maDatSan): bool {
        $sql = "SELECT COUNT(*) FROM danhgia WHERE MaDatSan = :maDatSan";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':maDatSan' => $maDatSan]);
            return $stmt->fetchColumn() > 0;
        } catch (\PDOException $e) {
            // If the column doesn't exist, no review can exist.
             error_log("SQL Error checking for review: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets the most recent reviews from the database.
     *
     * @param int $limit The number of reviews to fetch.
     * @return array An array of recent reviews.
     */
    public function getRecentReviews(int $limit = 3): array {
        $sql = "SELECT
                    dg.Diem,
                    dg.NoiDung,
                    dg.NgayDanhGia,
                    kh.HoTen,
                    sb.TenSan,
                    ds.NgayDat,
                    ds.GioBatDau
                FROM
                    danhgia AS dg
                JOIN
                    khachhang AS kh ON dg.MaKH = kh.MaKH
                JOIN
                    datsan AS ds ON dg.MaDatSan = ds.MaDatSan
                JOIN
                    san AS sb ON ds.MaSan = sb.MaSan
                ORDER BY
                    dg.NgayDanhGia DESC
                LIMIT :limit";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            error_log("SQL Error fetching recent reviews: " . $e->getMessage());
            return []; // Return an empty array on error
        }
    }
}
