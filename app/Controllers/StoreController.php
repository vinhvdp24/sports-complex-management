<?php

namespace App\Controllers;

use App\Core\Controller;

class StoreController extends Controller
{
    public function index($category = null)
    {
        $productModel = new \App\Models\ProductModel();
        
        if ($category) {
            $products = $productModel->getProductsByCategory(urldecode($category));
            $pageTitle = 'Cửa Hàng - ' . urldecode($category);
        } else {
            $products = $productModel->getAllProducts();
            $pageTitle = 'Cửa Hàng';
        }

        $categories = $productModel->getDistinctCategories();
        
        $this->view('store/index', [
            'products' => $products,
            'categories' => $categories,
            'pageTitle' => $pageTitle,
            'currentCategory' => $category ? urldecode($category) : 'Tất cả sản phẩm'
        ]);
    }

    public function productDetail($id)
    {
        $productModel = new \App\Models\ProductModel();
        $product = $productModel->getProductById($id);
        $variants = $productModel->getVariantsByProductId($id);

        if (!$product) {
            // Có thể hiển thị trang 404 hoặc thông báo lỗi
            $this->pageNotFound("Sản phẩm không tìm thấy.");
            return;
        }

        $pageTitle = $product['name'];
        
        $this->view('store/detail', [
            'product' => $product,
            'variants' => $variants,
            'pageTitle' => $pageTitle
        ]);
    }
}
