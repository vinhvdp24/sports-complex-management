<?php
namespace App\Models;

require_once __DIR__ . '/Database.php';

use App\Models\Database;

class ProductModel extends Database
{
    /**
     * Lấy danh sách tất cả các sản phẩm.
     */
    public function getAllProducts()
    {
        $sql = "SELECT MaSanPham as id, TenSanPham as name, MoTa as description, DonGia as price, DanhMuc as category, HinhAnh as image_url FROM sanpham ORDER BY TenSanPham ASC";
        
        try {
            $stmt = $this->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Lỗi khi lấy danh sách sản phẩm: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách các danh mục sản phẩm duy nhất.
     */
    public function getDistinctCategories()
    {
        $sql = "SELECT DISTINCT DanhMuc FROM sanpham ORDER BY DanhMuc ASC";
        
        try {
            $stmt = $this->query($sql);
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\Exception $e) {
            error_log("Lỗi khi lấy danh sách danh mục: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy danh sách sản phẩm theo danh mục.
     */
    public function getProductsByCategory($category)
    {
        $sql = "SELECT MaSanPham as id, TenSanPham as name, MoTa as description, DonGia as price, DanhMuc as category, HinhAnh as image_url FROM sanpham WHERE DanhMuc = :category ORDER BY TenSanPham ASC";
        $params = [':category' => $category];

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Lỗi khi lấy sản phẩm theo danh mục '$category': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin chi tiết của một sản phẩm theo ID.
     */
    public function getProductById($id)
    {
        $sql = "SELECT MaSanPham as id, TenSanPham as name, MoTa as description, DonGia as price, DanhMuc as category, HinhAnh as image_url FROM sanpham WHERE MaSanPham = :id";
        $params = [':id' => $id];

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Lỗi khi lấy sản phẩm theo ID '$id': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy danh sách các biến thể (size, stock) của một sản phẩm.
     */
    public function getVariantsByProductId($productId)
    {
        $sql = "SELECT MaBienThe as id, KichThuoc as size, SoLuongTon as stock FROM bienthesanpham WHERE MaSanPham = :product_id ORDER BY KichThuoc ASC";
        $params = [':product_id' => $productId];

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Lỗi khi lấy biến thể sản phẩm theo product_id '$productId': " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin chi tiết của một biến thể theo ID.
     */
    public function getVariantById($variantId)
    {
        $sql = "SELECT MaBienThe as id, MaSanPham as product_id, KichThuoc as size, SoLuongTon as stock FROM bienthesanpham WHERE MaBienThe = :variant_id";
        $params = [':variant_id' => $variantId];

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Lỗi khi lấy biến thể theo ID '$variantId': " . $e->getMessage());
            return false;
        }
    }

    /**
     * Tìm kiếm sản phẩm theo tên.
     */
    public function searchProductsByName($keyword)
    {
        $sql = "SELECT MaSanPham as id, TenSanPham as name, MoTa as description, DonGia as price, DanhMuc as category, HinhAnh as image_url FROM sanpham WHERE TenSanPham LIKE :keyword ORDER BY TenSanPham ASC";
        $params = [':keyword' => '%' . $keyword . '%'];

        try {
            $stmt = $this->query($sql, $params);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Lỗi khi tìm kiếm sản phẩm với từ khóa '$keyword': " . $e->getMessage());
            return [];
        }
    }
}
