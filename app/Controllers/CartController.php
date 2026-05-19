<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ProductModel;
use App\Core\Session;

class CartController extends Controller
{
    public function __construct()
    {
        // Đảm bảo session đã được bắt đầu
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Block admin access to the entire controller
        if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin') {
            header('Location: ' . BASE_URL . 'admin-dashboard');
            exit();
        }

        // Initialize cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    public function add()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
            exit();
        }

        $variant_id = filter_input(INPUT_POST, 'variant_id', FILTER_VALIDATE_INT);
        $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

        if (!$variant_id || !$quantity || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu sản phẩm không hợp lệ.']);
            exit();
        }

        $productModel = new ProductModel();
        $variant = $productModel->getVariantById($variant_id);

        if (!$variant) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            exit();
        }

        $product = $productModel->getProductById($variant['product_id']);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại.']);
            exit();
        }
        
        $cartItemId = $variant['product_id'] . '_' . $variant_id;
        $quantityInCart = $_SESSION['cart'][$cartItemId]['quantity'] ?? 0;

        // Kiểm tra kho
        if (($quantity + $quantityInCart) > $variant['stock']) {
            echo json_encode(['success' => false, 'message' => 'Số lượng yêu cầu vượt quá tồn kho.']);
            exit();
        }
        
        if (isset($_SESSION['cart'][$cartItemId])) {
            $_SESSION['cart'][$cartItemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$cartItemId] = [
                'product_id' => $variant['product_id'],
                'variant_id' => $variant_id,
                'name' => $product['name'],
                'price' => $product['price'],
                'image_url' => $product['image_url'],
                'size' => $variant['size'],
                'quantity' => $quantity,
                'max_quantity' => $variant['stock']
            ];
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Thêm vào giỏ hàng thành công!',
            'variantId' => $variant_id,
            'newStock' => $variant['stock'] - $_SESSION['cart'][$cartItemId]['quantity']
        ]);
        exit();
    }

    public function index()
    {
        // For now, just a placeholder. Will be implemented in Phase 3.
        $cartItems = $_SESSION['cart'] ?? [];
        $totalQuantity = 0;
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalQuantity += $item['quantity'];
            $totalPrice += $item['quantity'] * $item['price'];
        }

        $this->view('cart/index', [
            'cartItems' => $cartItems,
            'totalQuantity' => $totalQuantity,
            'totalPrice' => $totalPrice,
            'pageTitle' => 'Giỏ Hàng Của Bạn'
        ]);
    }

    // You will add update() and remove() methods here later

    public function getCartCount()
    {
        header('Content-Type: application/json');
        $totalCount = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $totalCount += $item['quantity'];
            }
        }
        echo json_encode(['count' => $totalCount]);
        exit();
    }

    public function update()
    {
        // Xóa tất cả output buffer trước đó để tránh lỗi JSON
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ.']);
            exit();
        }

        // Sử dụng $_POST trực tiếp để ổn định hơn
        $cartItemId = $_POST['cart_item_id'] ?? null;
        $newQuantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : -1;

        if (!$cartItemId || $newQuantity < 0) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu cập nhật không hợp lệ.']);
            exit();
        }

        if (!isset($_SESSION['cart'][$cartItemId])) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không có trong giỏ.']);
            exit();
        }
        
        $itemSubtotal = 0;
        if ($newQuantity == 0) {
            unset($_SESSION['cart'][$cartItemId]);
            $message = 'Sản phẩm đã được xóa khỏi giỏ hàng.';
        } else {
            if ($newQuantity > $_SESSION['cart'][$cartItemId]['max_quantity']) {
                echo json_encode(['success' => false, 'message' => 'Số lượng vượt quá tồn kho.']);
                exit();
            }
            $_SESSION['cart'][$cartItemId]['quantity'] = $newQuantity;
            $itemSubtotal = $_SESSION['cart'][$cartItemId]['price'] * $newQuantity;
            $message = 'Cập nhật giỏ hàng thành công.';
        }

        // Recalculate totals for the response
        $totalPrice = 0;
        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true, 
            'message' => $message,
            'itemSubtotal' => number_format($itemSubtotal, 0, ',', '.') . ' VNĐ',
            'totalPrice' => number_format($totalPrice, 0, ',', '.') . ' VNĐ',
            'totalQuantity' => $totalQuantity
        ]);
        exit();
    }

    public function remove()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
            exit();
        }

        $cartItemId = filter_input(INPUT_POST, 'cart_item_id', FILTER_SANITIZE_STRING);

        if (!$cartItemId || !isset($_SESSION['cart'][$cartItemId])) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ.']);
            exit();
        }

        unset($_SESSION['cart'][$cartItemId]);
        
        // Recalculate totals
        $totalPrice = 0;
        $totalQuantity = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalPrice += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }
        
        echo json_encode([
            'success' => true, 
            'message' => 'Sản phẩm đã được xóa khỏi giỏ.',
            'totalPrice' => number_format($totalPrice) . ' VNĐ',
            'totalQuantity' => $totalQuantity
        ]);
        exit();
    }
}
