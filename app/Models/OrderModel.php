<?php
namespace App\Models;

use PDOException;

require_once 'Database.php';

class OrderModel extends Database
{
    /**
     * Creates a new order in the database.
     */
    public function createOrder(array $orderData)
    {
        $sql = "INSERT INTO donhang (MaKH, TenKH, SDT, Email, DiaChi, TongTien, PhuongThucThanhToan, TrangThai)
                VALUES (:khachhang_id, :customer_name, :customer_sdt, :customer_email, :customer_address, :total_amount, :payment_method, :status)";
        
        try {
            $this->query($sql, [
                ':khachhang_id' => $orderData['khachhang_id'],
                ':customer_name' => $orderData['customer_name'],
                ':customer_sdt' => $orderData['customer_sdt'],
                ':customer_email' => $orderData['customer_email'],
                ':customer_address' => $orderData['customer_address'],
                ':total_amount' => $orderData['total_amount'],
                ':payment_method' => $orderData['payment_method'],
                ':status' => $orderData['status'] ?? 'Chờ xử lý'
            ]);
            return $this->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating order: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Creates multiple order items in a single batch query.
     */
    public function createOrderItems(int $orderId, array $items)
    {
        if (empty($items)) {
            return true;
        }

        $sql = "INSERT INTO chitietdonhang (MaDonHang, MaSanPham, MaBienThe, SoLuong, DonGia) VALUES ";
        $placeholders = [];
        $params = [];

        foreach ($items as $item) {
            $placeholders[] = "(?, ?, ?, ?, ?)";
            $params[] = $orderId;
            $params[] = $item['product_id'];
            $params[] = $item['variant_id'];
            $params[] = $item['quantity'];
            $params[] = $item['price'];
        }

        $sql .= implode(', ', $placeholders);

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error creating batch order items: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Retrieves a specific order by its ID, ensuring it belongs to the logged-in user.
     */
    public function getOrderById($orderId, $khachhang_id)
    {
        $sql = "SELECT MaDonHang as id, TenKH as customer_name, SDT as customer_sdt, Email as customer_email, 
                       DiaChi as customer_address, TongTien as total_amount, PhuongThucThanhToan as payment_method, 
                       TrangThai as status, NgayTao as created_at 
                FROM donhang WHERE MaDonHang = :order_id AND MaKH = :khachhang_id";
        try {
            $stmt = $this->query($sql, [
                ':order_id' => $orderId,
                ':khachhang_id' => $khachhang_id
            ]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error fetching order by ID: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Retrieves all items for a specific order.
     */
    public function getOrderItemsByOrderId($orderId)
    {
        $sql = "SELECT oi.SoLuong as quantity, oi.DonGia as price, p.TenSanPham as name, v.KichThuoc as size, p.HinhAnh as image_url
                FROM chitietdonhang oi
                JOIN sanpham p ON oi.MaSanPham = p.MaSanPham
                JOIN bienthesanpham v ON oi.MaBienThe = v.MaBienThe
                WHERE oi.MaDonHang = :order_id";
        try {
            $stmt = $this->query($sql, [':order_id' => $orderId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching order items: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves all orders for a specific customer.
     */
    public function getOrdersByCustomerId($khachhang_id)
    {
        $sql = "SELECT MaDonHang as id, TongTien as total_amount, PhuongThucThanhToan as payment_method, TrangThai as status, NgayTao as created_at 
                FROM donhang WHERE MaKH = :khachhang_id ORDER BY NgayTao DESC";
        try {
            $stmt = $this->query($sql, [':khachhang_id' => $khachhang_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching orders by customer ID: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Retrieves all orders for the admin view.
     */
    public function getAllOrders()
    {
        $sql = "SELECT MaDonHang as id, MaKH, TenKH as customer_name, SDT as customer_sdt, 
                       Email as customer_email, DiaChi as customer_address, 
                       TongTien as total_amount, PhuongThucThanhToan as payment_method,
                       TrangThai as status, NgayTao as created_at 
                FROM donhang ORDER BY NgayTao DESC";
        try {
            $stmt = $this->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching all orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Updates the status of a specific order.
     */
    public function updateOrderStatus($orderId, $status)
    {
        $sql = "UPDATE donhang SET TrangThai = :status WHERE MaDonHang = :order_id";
        try {
            $stmt = $this->query($sql, [
                ':status' => $status,
                ':order_id' => $orderId
            ]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Counts the number of orders with a 'Chờ xử lý' status.
     */
    public function getPendingOrderCount()
    {
        $sql = "SELECT COUNT(MaDonHang) as total FROM donhang WHERE TrangThai = 'Chờ xử lý'";
        try {
            $stmt = $this->query($sql);
            $result = $stmt->fetch();
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error counting pending orders: " . $e->getMessage());
            return 0;
        }
    }

    public function getProductRevenue($startDate = null, $endDate = null) {
        $query = "SELECT SUM(TongTien) AS total_product_revenue FROM donhang WHERE TrangThai NOT IN ('Đã hủy', 'Hoàn tiền')";
        $params = [];
        if ($startDate && $endDate) {
            $query .= " AND NgayTao BETWEEN :startDate AND :endDate";
            $params[':startDate'] = $startDate . ' 00:00:00';
            $params[':endDate'] = $endDate . ' 23:59:59';
        }
        try {
            $stmt = $this->query($query, $params);
            $result = $stmt->fetch();
            return $result['total_product_revenue'] ?? 0;
        } catch (PDOException $e) {
            error_log("Error fetching product revenue: " . $e->getMessage());
            return 0;
        }
    }
}
