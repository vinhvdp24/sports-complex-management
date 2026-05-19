<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\KhachHangModel;
use App\Models\OrderModel;
use App\Models\KhoHangModel;
use App\Core\MoMoPayment;
use PDOException;

class CheckoutController extends Controller
{
    private $orderModel;
    private $khoHangModel;

    public function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->orderModel = new OrderModel();
        $this->khoHangModel = new KhoHangModel();
    }

    private function isLoggedIn() {
        return isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
    }

    public function index()
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['error_message'] = "Vui lòng đăng nhập để tiến hành thanh toán.";
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        if (empty($_SESSION['cart'])) {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $khachHangModel = new KhachHangModel();
        $maKH = $_SESSION['user_id'] ?? null;
        
        $customerInfo = $khachHangModel->getKhachHangById($maKH);

        if (!$customerInfo) {
            $_SESSION['error_message'] = "Không tìm thấy thông tin khách hàng.";
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $this->view('checkout/index', [
            'pageTitle' => 'Thanh toán',
            'customer' => $customerInfo,
            'cart' => $_SESSION['cart']
        ]);
    }

    public function placeOrder()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isLoggedIn() || empty($_SESSION['cart'])) {
            header('Location: ' . BASE_URL . 'cart');
            exit;
        }

        $khachHangModel = new KhachHangModel();
        $maKH = $_SESSION['user_id'];
        $customer = $khachHangModel->getKhachHangById($maKH);

        if (!$customer) {
            $_SESSION['error_message'] = "Không tìm thấy thông tin khách hàng.";
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $MAX_RETRIES = 3;
        $retry_count = 0;
        $transaction_successful = false;
        $error_message = '';

        while ($retry_count < $MAX_RETRIES && !$transaction_successful) {
            $this->orderModel->beginTransaction();
            try {
                $totalAmount = 0;
                foreach ($_SESSION['cart'] as $item) {
                    $totalAmount += $item['price'] * $item['quantity'];
                }

                $orderData = [
                    'khachhang_id' => $maKH,
                    'customer_name' => $customer['HoTen'],
                    'customer_sdt' => $customer['SDT'],
                    'customer_email' => $customer['Email'],
                    'customer_address' => $customer['DiaChi'],
                    'total_amount' => $totalAmount,
                    'payment_method' => $_POST['payment_method'] ?? 'Thanh toán trực tiếp',
                    'status' => 'Chờ xử lý'
                ];

                // Sort cart by variant_id to prevent deadlocks by ensuring a consistent locking order
                usort($_SESSION['cart'], function($a, $b) {
                    return $a['variant_id'] <=> $b['variant_id'];
                });

                // Step 1: Lock all items and verify stock availability
                foreach ($_SESSION['cart'] as $item) {
                    $isStockAvailable = $this->khoHangModel->checkAndLockVariantStock($item['variant_id'], $item['quantity']);
                    if (!$isStockAvailable) {
                        throw new \Exception("Rất tiếc, sản phẩm '{$item['name']}' (Size: {$item['size']}) không còn đủ số lượng tồn kho.");
                    }
                }

                // Step 2: Create the order (since all stocks are verified and locked)
                $orderId = $this->orderModel->createOrder($orderData);
                if (!$orderId) {
                    throw new \Exception("Không thể tạo đơn hàng.");
                }

                // Step 3: Prepare and insert all order items in a batch
                $itemsToInsert = [];
                foreach ($_SESSION['cart'] as $item) {
                    $itemsToInsert[] = [
                        'product_id' => $item['product_id'],
                        'variant_id' => $item['variant_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ];
                }
                $this->orderModel->createOrderItems($orderId, $itemsToInsert);

                // Step 4: Decrement stock for all items
                foreach ($_SESSION['cart'] as $item) {
                    $this->khoHangModel->decrementVariantStock($item['variant_id'], $item['quantity']);
                }

                // Xử lý thanh toán MoMo nếu chọn
                if ($orderData['payment_method'] === 'MoMo') {
                    $orderInfo = "Thanh toán đơn hàng #" . $orderId . " tại QuanLySVD";
                    $amount = (int)$totalAmount;
                    $requestId = time() . "";
                    $result = MoMoPayment::createPayment($orderId, $orderInfo, $amount, $requestId);

                    if (!isset($result['payUrl'])) {
                        throw new \Exception("Lỗi MoMo: " . ($result['message'] ?? 'Không thể kết nối với cổng thanh toán MoMo. Vui lòng thử lại sau.'));
                    }
                }

                // If all good, commit the transaction
                $this->orderModel->commit();
                $transaction_successful = true;

                // Clear the cart
                unset($_SESSION['cart']);

                // If MoMo, redirect to payUrl now that cart is cleared
                if ($orderData['payment_method'] === 'MoMo' && isset($result['payUrl'])) {
                    header('Location: ' . $result['payUrl']);
                    exit;
                }

                // Set success message and redirect to success page
                $_SESSION['success_message'] = "Đặt hàng thành công! Đơn hàng của bạn đang được xử lý.";
                header('Location: ' . BASE_URL . 'checkout/success?order_id=' . $orderId);
                exit;

            } catch (PDOException $e) {
                $this->orderModel->rollBack();
                error_log("Deadlock detected or PDO error during order placement (attempt " . ($retry_count + 1) . "): " . $e->getMessage());
                if ($e->getCode() === '40001' && $retry_count < $MAX_RETRIES - 1) { // SQLSTATE for deadlock
                    $retry_count++;
                    sleep(0.5 * $retry_count); // Exponential backoff for retries
                    continue; // Retry the transaction
                } else {
                    $error_message = "Đã có lỗi xảy ra trong quá trình đặt hàng: " . $e->getMessage();
                    break; // Exit loop, it's not a deadlock or max retries reached
                }
            } catch (\Exception $e) {
                $this->orderModel->rollBack();
                $error_message = "Đã có lỗi xảy ra trong quá trình đặt hàng: " . $e->getMessage();
                break; // Exit loop for non-PDO exceptions
            }
        }

        if (!$transaction_successful) {
            $_SESSION['error_message'] = $error_message ?: "Đã có lỗi không xác định xảy ra trong quá trình đặt hàng.";
            header('Location: ' . BASE_URL . 'checkout');
            exit;
        }
    }


    public function success()
    {
        if (!$this->isLoggedIn()) {
            header('Location: ' . BASE_URL . 'auth/hienThiDangNhap');
            exit;
        }

        $orderId = $_GET['order_id'] ?? null;
        if (!$orderId) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $maKH = $_SESSION['user_id'];
        
        $order = $this->orderModel->getOrderById($orderId, $maKH);

        // Kiểm tra bảo mật: đảm bảo đơn hàng tồn tại và thuộc về người dùng đã đăng nhập
        if (!$order) {
            $_SESSION['error_message'] = "Không tìm thấy đơn hàng hợp lệ.";
            header('Location: ' . BASE_URL . 'user/invoice_history'); // Redirect to their order history
            exit;
        }

        $orderItems = $this->orderModel->getOrderItemsByOrderId($orderId);

        $this->view('checkout/success', [
            'pageTitle' => 'Đặt Hàng Thành Công',
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    public function momoReturn()
    {
        $resultCode = $_GET['resultCode'] ?? -1;
        $orderIdFull = $_GET['orderId'] ?? '';
        $orderId = explode('_', $orderIdFull)[0]; // Lấy lại ID đơn hàng gốc (phần trước dấu _)

        if ($resultCode == 0) {
            // Thanh toán thành công
            $this->orderModel->updateOrderStatus($orderId, 'Paid');
            $_SESSION['success_message'] = "Thanh toán qua MoMo thành công!";
            header('Location: ' . BASE_URL . 'checkout/success?order_id=' . $orderId);
        } else {
            // Thanh toán thất bại hoặc bị hủy
            $_SESSION['error_message'] = "Giao dịch MoMo không thành công. (Mã lỗi: $resultCode)";
            header('Location: ' . BASE_URL . 'checkout/success?order_id=' . $orderId);
        }
        exit;
    }
}