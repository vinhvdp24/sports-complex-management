<?php
namespace App\Models;

require_once 'Database.php';

use PDO;
use Exception;

class KhoHangModel extends Database
{
    /**
     * Lấy tất cả sản phẩm cùng với các biến thể của chúng.
     * Dữ liệu trả về sẽ gom các biến thể vào một mảng cho mỗi sản phẩm.
     */
    public function getAllProductsWithVariants()
    {
        $sql = "SELECT p.MaSanPham as id, p.TenSanPham as name, p.MoTa as description, p.DonGia as price, 
                       p.DanhMuc as category, p.HinhAnh as image_url, p.NgayTao as created_at,
                       v.MaBienThe as variant_id, v.KichThuoc as size, v.SoLuongTon as stock 
                FROM sanpham p 
                LEFT JOIN bienthesanpham v ON p.MaSanPham = v.MaSanPham
                ORDER BY p.MaSanPham, v.KichThuoc ASC";
        
        try {
            $stmt = $this->query($sql);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $products = [];
            foreach ($results as $row) {
                $productId = $row['id'];
                if (!isset($products[$productId])) {
                    $products[$productId] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'price' => $row['price'],
                        'category' => $row['category'],
                        'image_url' => $row['image_url'],
                        'created_at' => $row['created_at'],
                        'variants' => []
                    ];
                }
                if ($row['variant_id']) {
                    $products[$productId]['variants'][] = [
                        'id' => $row['variant_id'],
                        'size' => $row['size'],
                        'stock' => $row['stock']
                    ];
                }
            }
            return array_values($products);
        } catch (Exception $e) {
            error_log("Error fetching products with variants: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy thông tin một sản phẩm và các biến thể bằng ID.
     */
    public function findProductWithVariantsById($productId)
    {
        $sql = "SELECT MaSanPham as id, TenSanPham as name, MoTa as description, DonGia as price, DanhMuc as category, HinhAnh as image_url, NgayTao as created_at FROM sanpham WHERE MaSanPham = ?";
        $product = $this->query($sql, [$productId])->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $sqlVariants = "SELECT MaBienThe as id, MaSanPham as product_id, KichThuoc as size, SoLuongTon as stock FROM bienthesanpham WHERE MaSanPham = ?";
            $product['variants'] = $this->query($sqlVariants, [$productId])->fetchAll(PDO::FETCH_ASSOC);
        }
        return $product;
    }

    /**
     * Tạo sản phẩm mới cùng với các biến thể của nó.
     * Sử dụng transaction để đảm bảo toàn vẹn dữ liệu.
     */
    public function createProduct($productData, $variantsData)
    {
        $this->beginTransaction();
        try {
            // Thêm sản phẩm vào bảng `sanpham`
            $sqlProduct = "INSERT INTO sanpham (TenSanPham, MoTa, DonGia, DanhMuc, HinhAnh) VALUES (?, ?, ?, ?, ?)";
            $this->query($sqlProduct, [
                $productData['name'],
                $productData['description'],
                $productData['price'],
                $productData['category'],
                $productData['image_url']
            ]);
            
            $productId = $this->lastInsertId();

            // Thêm các biến thể vào bảng `bienthesanpham`
            if (!empty($variantsData)) {
                $sqlVariant = "INSERT INTO bienthesanpham (MaSanPham, KichThuoc, SoLuongTon) VALUES (?, ?, ?)";
                foreach ($variantsData as $variant) {
                    $this->query($sqlVariant, [
                        $productId,
                        $variant['size'],
                        $variant['stock']
                    ]);
                }
            }

            $this->commit();
            return $productId;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error creating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cập nhật thông tin sản phẩm và các biến thể của nó.
     * Sử dụng transaction.
     */
    public function updateProduct($productId, $productData, $variantsData)
    {
        $this->beginTransaction();
        try {
            // Cập nhật bảng `sanpham`
            $sqlProduct = "UPDATE sanpham SET TenSanPham = ?, MoTa = ?, DonGia = ?, DanhMuc = ?, HinhAnh = ? WHERE MaSanPham = ?";
            $this->query($sqlProduct, [
                $productData['name'],
                $productData['description'],
                $productData['price'],
                $productData['category'],
                $productData['image_url'],
                $productId
            ]);

            // Xóa các biến thể cũ
            $this->query("DELETE FROM bienthesanpham WHERE MaSanPham = ?", [$productId]);

            // Thêm các biến thể mới
            if (!empty($variantsData)) {
                $sqlVariant = "INSERT INTO bienthesanpham (MaSanPham, KichThuoc, SoLuongTon) VALUES (?, ?, ?)";
                foreach ($variantsData as $variant) {
                    $this->query($sqlVariant, [
                        $productId,
                        $variant['size'],
                        $variant['stock']
                    ]);
                }
            }

            $this->commit();
            return true;
        } catch (Exception $e) {
            $this->rollBack();
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Xóa một sản phẩm.
     */
    public function deleteProduct($productId)
    {
        try {
            $sql = "DELETE FROM sanpham WHERE MaSanPham = ?";
            $this->query($sql, [$productId]);
            return true;
        } catch (Exception $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Locks a variant row and checks if stock is sufficient.
     */
    public function checkAndLockVariantStock($variantId, $requiredQuantity)
    {
        $sql = "SELECT SoLuongTon as stock FROM bienthesanpham WHERE MaBienThe = ? FOR UPDATE";
        try {
            $stmt = $this->query($sql, [$variantId]);
            $variant = $stmt->fetch();

            if ($variant && $variant['stock'] >= $requiredQuantity) {
                return true; // Stock is sufficient and row is locked
            }
            return false; // Stock is insufficient or variant not found
        } catch (PDOException $e) {
            error_log("Error locking stock for variant ID $variantId: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Decrements the stock for a given variant.
     */
    public function decrementVariantStock($variantId, $quantity)
    {
        $sql = "UPDATE bienthesanpham SET SoLuongTon = SoLuongTon - ? WHERE MaBienThe = ?";
        
        try {
            $stmt = $this->query($sql, [$quantity, $variantId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error decrementing stock for variant ID $variantId: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Đếm tổng số sản phẩm.
     */
    public function countTotalProducts()
    {
        $sql = "SELECT COUNT(MaSanPham) as total FROM sanpham";
        try {
            $stmt = $this->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (Exception $e) {
            error_log("Error counting products: " . $e->getMessage());
            return 0;
        }
    }
}
