<?php
namespace App\Controllers;

use App\Controllers\AuthController;
use App\Core\Controller;
use App\Models\SanModel; 
use App\Models\DichVuModel;
use App\Models\KhachHangModel;
use App\Models\NhapHangModel;
use App\Models\TonKhoModel;
use App\Models\KhoHangModel;
use App\Models\OrderModel;
use App\Models\DatSanModel;
use App\Models\UserModel;


class AdminController extends Controller
{
    private $sanModel; 
    private $dichVuModel;
    private $khachHangModel;
    private $nhapHangModel;
    private $tonKhoModel;
    private $khoHangModel;
    private $orderModel;
    private $datSanModel;
    private $userModel;
    
    public function __construct() {
        $this->sanModel = new SanModel(); 
        $this->dichVuModel = new DichVuModel();
        $this->khachHangModel = new KhachHangModel();
        $this->nhapHangModel = new NhapHangModel();
        $this->tonKhoModel = new TonKhoModel();
        $this->khoHangModel = new KhoHangModel();
        $this->orderModel = new OrderModel();
        $this->datSanModel = new DatSanModel();
        $this->userModel = new UserModel();
    }

    private function isAdmin()
    {
        return (isset($_SESSION['user_role']) && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'owner')); 
    }

    private function isOwner()
    {
        return (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'owner');
    }

    private function getRevenueStatistics($startDate = null, $endDate = null)
    {
        // Get pitch revenue
        $pitchRevenue = $this->sanModel->getPitchRevenue($startDate, $endDate);
        
        // Get service revenue
        $serviceRevenue = $this->dichVuModel->getServiceRevenue($startDate, $endDate);
        
        // Get product revenue
        $productRevenue = $this->orderModel->getProductRevenue($startDate, $endDate);

        $totalRevenue = $pitchRevenue + $serviceRevenue + $productRevenue;

        return [
            'pitchRevenue' => $pitchRevenue,
            'serviceRevenue' => $serviceRevenue,
            'productRevenue' => $productRevenue,
            'totalRevenue' => $totalRevenue
        ];
    }

    public function hienThiDashboard()
    {
       
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
 
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index'); 
            exit;
        }

        $totalSan = $this->sanModel->countTotalSan();
        $totalDichVu = $this->dichVuModel->countTotalDichVu();
        $totalProducts = $this->khoHangModel->countTotalProducts();
                $pendingOrdersCount = $this->orderModel->getPendingOrderCount();
                
        
                $this->view('admin/dashboard', [
                    'title' => 'Admin Dashboard',
                    'username' => $_SESSION['username'] ?? 'Quản trị viên', 
                    'role' => $_SESSION['user_role'] ?? 'admin',
                    'totalSan' => $totalSan,
                    'totalDichVu' => $totalDichVu,
                    'totalProducts' => $totalProducts,
                    'pendingOrdersCount' => $pendingOrdersCount,
                ]);    }
        
    public function hienThiDoanhThu()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $endDate = $_GET['endDate'] ?? date('Y-m-d');
        $startDate = $_GET['startDate'] ?? date('Y-m-d', strtotime('-6 days', strtotime($endDate)));


        $revenueData = $this->getRevenueStatistics($startDate, $endDate);
        $bookingCountData = $this->datSanModel->getBookingCountByDateRange($startDate, $endDate);
        $cancelledRevenue = $this->datSanModel->getCancelledRevenue($startDate, $endDate);


        $this->view('admin/revenue', [
            'title' => 'Thống Kê Doanh Thu',
            'username' => $_SESSION['username'] ?? 'Quản trị viên',
            'revenueData' => $revenueData,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'bookingCountData' => $bookingCountData,
            'cancelledRevenue' => $cancelledRevenue
        ]);
    }
    public function quanLyDonHang()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $orders = $this->orderModel->getAllOrders();

        $this->view('admin/donhang/index', [
            'title' => 'Quản Lý Đơn Hàng',
            'orders' => $orders
        ]);
    }

    public function xuLyCapNhatTrangThaiDonHang()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/don-hang');
            exit;
        }

        $orderId = $_POST['order_id'] ?? null;
        $status = $_POST['status'] ?? null;

        if ($orderId && $status) {
            if ($this->orderModel->updateOrderStatus($orderId, $status)) {
                $_SESSION['admin_success'] = "Cập nhật trạng thái đơn hàng #{$orderId} thành công.";
            } else {
                $_SESSION['admin_error'] = "Cập nhật trạng thái đơn hàng #{$orderId} thất bại.";
            }
        } else {
            $_SESSION['admin_error'] = "Dữ liệu không hợp lệ.";
        }

        header('Location: ' . BASE_URL . 'admin/don-hang');
        exit;
    }

    public function quanLySan()
    {
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }
        $sanBongList = $this->sanModel->getAllSan(); 
        $this->view('admin/san', [
            'sanBongList' => $sanBongList
        ]);
    }
public function hienThiThemSan()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!$this->isOwner()) {
        $_SESSION['admin_error'] = "Chỉ có Chủ sân (Owner) mới có quyền thêm sân mới.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }
    
    $error = $_SESSION['admin_error'] ?? null;
    $success = $_SESSION['admin_success'] ?? null;
    
    unset($_SESSION['admin_error']); 
    unset($_SESSION['admin_success']); 
    
    $loaiSanList = $this->sanModel->getAllLoaiSan();
 
    $this->view('admin/san/them', [
        'title' => 'Thêm Sân Mới',
        'error' => $error,   
        'success' => $success,
        'loaiSanList' => $loaiSanList
    ]);
}
public function xuLyThemSan()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    if (!$this->isOwner()) {
        $_SESSION['admin_error'] = "Chỉ có Chủ sân (Owner) mới có quyền thực hiện thao tác này.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    $data = [
        'MaSan' => trim($_POST['MaSan'] ?? ''),
        'TenSan' => trim($_POST['TenSan'] ?? ''),
        'MaLoai' => $_POST['MaLoai'] ?? '',
        'GiaThue' => floatval($_POST['GiaThue'] ?? 0), 
        'TinhTrang' => $_POST['TinhTrang'] ?? 'Trống',
        'MoTa' => trim($_POST['MoTa'] ?? ''),
    ];

    // Validation chi tiết
    if (empty($data['MaSan'])) {
        $_SESSION['admin_error'] = "Vui lòng nhập Mã Sân.";
        header('Location: ' . BASE_URL . 'form-them-san');
        exit;
    }
    if (empty($data['TenSan'])) {
        $_SESSION['admin_error'] = "Tên sân không được để trống.";
        header('Location: ' . BASE_URL . 'form-them-san');
        exit;
    }
    if (empty($data['MaLoai'])) {
        $_SESSION['admin_error'] = "Vui lòng chọn loại sân.";
        header('Location: ' . BASE_URL . 'form-them-san');
        exit;
    }
    if ($data['GiaThue'] <= 0) {
        $_SESSION['admin_error'] = "Giá thuê phải lớn hơn 0.";
        header('Location: ' . BASE_URL . 'form-them-san');
        exit;
    }


    $result = $this->sanModel->insertSan($data); 

    if ($result) {
        $_SESSION['admin_success'] = "Đã thêm sân '" . $data['TenSan'] . "' thành công.";
        header('Location: ' . BASE_URL . 'admin-san');
    } else {
        $_SESSION['admin_error'] = "Lỗi Database: Không thể thêm sân mới. Có thể Mã Sân đã tồn tại.";
        header('Location: ' . BASE_URL . 'form-them-san');
    }
    exit;
}
public function xuLyXoaSan()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isOwner()) {
        $_SESSION['admin_error'] = "Chỉ có Chủ sân (Owner) mới có quyền xóa sân.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    $maSan = $_POST['MaSan'] ?? null;

    if (empty($maSan)) {
        $_SESSION['admin_error'] = "Thiếu thông tin Mã Sân cần xóa.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    // Proactive check for existing bookings
    if ($this->sanModel->hasBookings($maSan)) {
        $_SESSION['admin_error'] = "Không thể xóa sân (Mã: $maSan) vì đã có lịch đặt tồn tại.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    $result = $this->sanModel->deleteSan($maSan); 

    if ($result) {
        $_SESSION['admin_success'] = "Đã xóa sân có Mã: $maSan thành công.";
    } else {
        $_SESSION['admin_error'] = "Lỗi: Không thể xóa sân (Mã: $maSan). Đã có lỗi xảy ra từ phía cơ sở dữ liệu.";
    }

    header('Location: ' . BASE_URL . 'admin-san');
    exit;
}
// Trong class AdminController
public function hienThiSuaSan()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!$this->isOwner()) {
        $_SESSION['admin_error'] = "Chỉ có Chủ sân (Owner) mới có quyền truy cập trang này.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    } 
    
    // 1. Lấy MaSan từ URL Query String
    $maSan = $_GET['MaSan'] ?? null;

    if (empty($maSan)) {
        $_SESSION['admin_error'] = "Thiếu Mã Sân cần sửa.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    // 2. Lấy dữ liệu sân hiện tại từ Model
    $san = $this->sanModel->getSanById($maSan);

    if (!$san) {
        $_SESSION['admin_error'] = "Không tìm thấy sân có Mã: " . $maSan;
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    // Đọc và xóa thông báo lỗi (nếu có)
    $error = $_SESSION['admin_error'] ?? null;
    unset($_SESSION['admin_error']);

    $loaiSanList = $this->sanModel->getAllLoaiSan();

    // 3. Load View Form Sửa
    $this->view('admin/san/sua', [
        'title' => 'Sửa Sân: ' . $san['TenSan'],
        'san' => $san, 
        'error' => $error,
        'loaiSanList' => $loaiSanList
    ]);
}
// Trong class AdminController
public function xuLySuaSan()
{
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->isOwner()) {
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }

    $maSan = $_POST['MaSan'] ?? null;
    $data = [
        'TenSan' => trim($_POST['TenSan'] ?? ''),
        'MaLoai' => $_POST['MaLoai'] ?? '',
        'GiaThue' => floatval($_POST['GiaThue'] ?? 0), 
        'TinhTrang' => $_POST['TinhTrang'] ?? 'Trống',
        'MoTa' => trim($_POST['MoTa'] ?? ''),
    ];

    // Validation chi tiết
    if (empty($maSan)) {
        $_SESSION['admin_error'] = "Thiếu Mã Sân cần sửa.";
        header('Location: ' . BASE_URL . 'admin-san');
        exit;
    }
    if (empty($data['TenSan'])) {
        $_SESSION['admin_error'] = "Tên sân không được để trống.";
        header('Location: ' . BASE_URL . 'admin-san-sua?MaSan=' . $maSan);
        exit;
    }
    if (empty($data['MaLoai'])) {
        $_SESSION['admin_error'] = "Vui lòng chọn loại sân.";
        header('Location: ' . BASE_URL . 'admin-san-sua?MaSan=' . $maSan);
        exit;
    }
    if ($data['GiaThue'] <= 0) {
        $_SESSION['admin_error'] = "Giá thuê phải lớn hơn 0.";
        header('Location: ' . BASE_URL . 'admin-san-sua?MaSan=' . $maSan);
        exit;
    }

    $result = $this->sanModel->updateSan($maSan, $data); 

    if ($result) {
        $_SESSION['admin_success'] = "Đã cập nhật sân (Mã: $maSan) thành công.";
        header('Location: ' . BASE_URL . 'admin-san');
    } else {
        $_SESSION['admin_error'] = "Không có thay đổi nào được lưu hoặc lỗi Database.";
        header('Location: ' . BASE_URL . 'admin-san-sua?MaSan=' . $maSan);
    }
    exit;
}

public function khoHang()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $products = $this->khoHangModel->getAllProductsWithVariants();

        $this->view('admin/khohang/index', [
            'title' => 'Quản Lý Kho Hàng',
            'products' => $products
        ]);
    }

    public function themSanPham()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner()) {
            $_SESSION['admin_error'] = "Bạn không có quyền thêm sản phẩm.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        $this->view('admin/khohang/them', [
            'title' => 'Thêm Sản Phẩm Mới'
        ]);
    }

    public function xuLyThemSanPham()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        // Dữ liệu sản phẩm chính
        $productData = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'price' => filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'category' => trim($_POST['category']),
            'image_url' => '' // Sẽ cập nhật sau khi upload file
        ];

        // Dữ liệu biến thể
        $variantsData = [];
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            foreach ($_POST['variants'] as $variant) {
                if (!empty($variant['size']) && is_numeric($variant['stock'])) {
                    $variantsData[] = [
                        'size' => trim($variant['size']),
                        'stock' => (int)$variant['stock']
                    ];
                }
            }
        }
        
        // Xử lý upload ảnh
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'public/images/products/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // --- BẮT ĐẦU SANITIZE FILENAME ---
            $originalName = basename($_FILES['image']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            
            // Chuyển thành chữ thường và thay dấu cách bằng gạch ngang
            $safeFilename = strtolower($filenameWithoutExt);
            $safeFilename = str_replace(' ', '-', $safeFilename);

            // Bỏ dấu tiếng Việt
            $vietnameseChars = ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ','ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự','ý','ỳ','ỷ','ỹ','ỵ'];
            $asciiChars =    ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y'];
            $safeFilename = str_replace($vietnameseChars, $asciiChars, $safeFilename);
            
            // Xóa các ký tự không hợp lệ khác
            $safeFilename = preg_replace('/[^a-z0-9\-]/', '', $safeFilename);
            $safeFilename = preg_replace('/-+/', '-', $safeFilename); // Thay thế nhiều dấu gạch ngang bằng một

            $fileName = $safeFilename . '.' . $extension;
            // --- KẾT THÚC SANITIZE FILENAME ---

            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $productData['image_url'] = $targetFile;
                // Ensure forward slashes for web paths
                $productData['image_url'] = str_replace('\\', '/', $productData['image_url']);
            } else {
                $_SESSION['admin_error'] = "Lỗi khi tải ảnh lên.";
                header('Location: ' . BASE_URL . 'admin/kho-hang/them');
                exit;
            }
        } else {
            $_SESSION['admin_error'] = "Vui lòng chọn một hình ảnh cho sản phẩm.";
            header('Location: ' . BASE_URL . 'admin/kho-hang/them');
            exit;
        }
        
        // Gọi model để tạo sản phẩm
        $result = $this->khoHangModel->createProduct($productData, $variantsData);

        if ($result) {
            $_SESSION['admin_success'] = "Sản phẩm '{$productData['name']}' đã được thêm thành công.";
        } else {
            $_SESSION['admin_error'] = "Đã xảy ra lỗi. không thể thêm sản phẩm.";
            // Xóa file ảnh đã upload nếu có lỗi DB
            if (file_exists($productData['image_url'])) {
                unlink($productData['image_url']);
            }
        }

        header('Location: ' . BASE_URL . 'admin/kho-hang');
        exit;
    }

    public function xuLyXoaSanPham()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['admin_error'] = "Bạn không có quyền xóa sản phẩm.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        $productId = $_POST['id'] ?? null;
        if (!$productId) {
            $_SESSION['admin_error'] = "ID sản phẩm không hợp lệ.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        // Lấy thông tin sản phẩm để có thể xóa ảnh
        $product = $this->khoHangModel->findProductWithVariantsById($productId);
        if (!$product) {
            $_SESSION['admin_error'] = "Không tìm thấy sản phẩm để xóa.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        // Xóa sản phẩm khỏi DB (variants sẽ bị xóa theo nhờ ON DELETE CASCADE)
        $result = $this->khoHangModel->deleteProduct($productId);

        if ($result) {
            // Nếu xóa DB thành công, xóa file ảnh
            if (!empty($product['image_url']) && file_exists($product['image_url'])) {
                unlink($product['image_url']);
            }
            $_SESSION['admin_success'] = "Sản phẩm đã được xóa thành công.";
        } else {
            $_SESSION['admin_error'] = "Đã xảy ra lỗi khi xóa sản phẩm.";
        }

        header('Location: ' . BASE_URL . 'admin/kho-hang');
        exit;
    }

    public function suaSanPham()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner()) {
            $_SESSION['admin_error'] = "Bạn không có quyền sửa sản phẩm.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        $productId = $_GET['id'] ?? null;
        if (!$productId) {
            $_SESSION['admin_error'] = "ID sản phẩm không được cung cấp.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        $product = $this->khoHangModel->findProductWithVariantsById($productId);
        if (!$product) {
            $_SESSION['admin_error'] = "Không tìm thấy sản phẩm.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        $this->view('admin/khohang/sua', [
            'title' => 'Sửa Sản Phẩm: ' . htmlspecialchars($product['name']),
            'product' => $product
        ]);
    }

    public function xuLySuaSanPham()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        $productId = $_POST['id'] ?? null;
        if (!$productId) {
            $_SESSION['admin_error'] = "ID sản phẩm không hợp lệ.";
            header('Location: ' . BASE_URL . 'admin/kho-hang');
            exit;
        }

        // Lấy dữ liệu sản phẩm hiện tại để xử lý ảnh
        $existingProduct = $this->khoHangModel->findProductWithVariantsById($productId);
        
        $productData = [
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']),
            'price' => filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION),
            'category' => trim($_POST['category']),
            'image_url' => $existingProduct['image_url'] // Giữ ảnh cũ mặc định
        ];

        // Xử lý upload ảnh mới (nếu có)
        if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $uploadDir = 'public/images/products/';
            
            // --- BẮT ĐẦU SANITIZE FILENAME ---
            $originalName = basename($_FILES['image']['name']);
            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
            $filenameWithoutExt = pathinfo($originalName, PATHINFO_FILENAME);
            
            $safeFilename = strtolower($filenameWithoutExt);
            $safeFilename = str_replace(' ', '-', $safeFilename);

            $vietnameseChars = ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ','ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự','ý','ỳ','ỷ','ỹ','ỵ'];
            $asciiChars =    ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y'];
            $safeFilename = str_replace($vietnameseChars, $asciiChars, $safeFilename);
            
            $safeFilename = preg_replace('/[^a-z0-9\-]/', '', $safeFilename);
            $safeFilename = preg_replace('/-+/', '-', $safeFilename);

            $fileName = $safeFilename . '.' . $extension;
            // --- KẾT THÚC SANITIZE FILENAME ---

            $targetFile = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                // Xóa ảnh cũ nếu upload thành công
                if (!empty($existingProduct['image_url']) && file_exists($existingProduct['image_url'])) {
                    unlink($existingProduct['image_url']);
                }
                $productData['image_url'] = $targetFile;
                // Ensure forward slashes for web paths
                $productData['image_url'] = str_replace('\\', '/', $productData['image_url']);
            } 
        }

        $variantsData = [];
        if (isset($_POST['variants']) && is_array($_POST['variants'])) {
            foreach ($_POST['variants'] as $variant) {
                if (!empty($variant['size']) && isset($variant['stock']) && is_numeric($variant['stock'])) {
                    $variantsData[] = [
                        'size' => trim($variant['size']),
                        'stock' => (int)$variant['stock']
                    ];
                }
            }
        }
        
        $result = $this->khoHangModel->updateProduct($productId, $productData, $variantsData);

        if ($result) {
            $_SESSION['admin_success'] = "Sản phẩm '{$productData['name']}' đã được cập nhật thành công.";
        } else {
            $_SESSION['admin_error'] = "Không có thay đổi nào được lưu hoặc đã xảy ra lỗi.";
        }

        header('Location: ' . BASE_URL . 'admin/kho-hang');
        exit;
    }

    public function dichvu()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $dichVuList = $this->dichVuModel->getAllDichVu();
        $tonKhoDichVu = $this->tonKhoModel->getTonKhoDichVu();

        $this->view('admin/dichvu', [
            'dichVuList' => $dichVuList,
            'tonKhoDichVu' => $tonKhoDichVu,
            'title' => 'Quản Lý Dịch Vụ'
        ]);
    }

    public function hienThiThemDichVu() {
        if (!$this->isOwner()) {
            $_SESSION['admin_error'] = "Bạn không có quyền thêm dịch vụ.";
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }
        $this->view('admin/dichvu/them', ['title' => 'Thêm Dịch Vụ Mới']);
    }

    public function xuLyThemDichVu() {
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }

        $data = [
            'TenDV' => $_POST['TenDV'],
            'DonGia' => $_POST['DonGia'],
            'MoTa' => $_POST['MoTa'] ?? ''
        ];

        // Handle file upload
        if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['error'] == 0) {
            $uploadDir = 'public/images/dichvu/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $fileName = uniqid() . '-' . basename($_FILES['HinhAnh']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $targetFile)) {
                $data['HinhAnh'] = $fileName;
            }
        }

        if ($this->dichVuModel->addDichVu($data)) {
            $_SESSION['success_message'] = "Thêm dịch vụ thành công.";
        } else {
            $_SESSION['error_message'] = "Thêm dịch vụ thất bại.";
        }
        header('Location: ' . BASE_URL . 'admin/dichvu');
        exit;
    }

    public function hienThiSuaDichVu() {
        if (!$this->isOwner()) {
            $_SESSION['admin_error'] = "Bạn không có quyền sửa dịch vụ.";
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }
        $maDV = $_GET['id'] ?? null;
        if (!$maDV) {
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }
        $dichVu = $this->dichVuModel->getDichVuById($maDV);
        $this->view('admin/dichvu/sua', ['title' => 'Sửa Dịch Vụ', 'dichVu' => $dichVu]);
    }

    public function xuLySuaDichVu() {
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }

        $maDV = $_POST['MaDV'];
        $data = [
            'TenDV' => $_POST['TenDV'],
            'DonGia' => $_POST['DonGia'],
            'MoTa' => $_POST['MoTa'] ?? ''
        ];

        // Handle file upload
        if (isset($_FILES['HinhAnh']) && $_FILES['HinhAnh']['error'] == 0) {
            $uploadDir = 'public/images/dichvu/';
            $fileName = uniqid() . '-' . basename($_FILES['HinhAnh']['name']);
            $targetFile = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['HinhAnh']['tmp_name'], $targetFile)) {
                $data['HinhAnh'] = $fileName;
            }
        }

        if ($this->dichVuModel->updateDichVu($maDV, $data)) {
            $_SESSION['success_message'] = "Cập nhật dịch vụ thành công.";
        } else {
            $_SESSION['error_message'] = "Cập nhật dịch vụ thất bại.";
        }
        header('Location: ' . BASE_URL . 'admin/dichvu');
        exit;
    }

    public function xuLyXoaDichVu() {
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['admin_error'] = "Bạn không có quyền xóa dịch vụ.";
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }

        $maDV = $_POST['MaDV'];
        if ($this->dichVuModel->deleteDichVu($maDV)) {
            $_SESSION['success_message'] = "Xóa dịch vụ thành công.";
        } else {
            $_SESSION['error_message'] = "Xóa dịch vụ thất bại.";
        }
        header('Location: ' . BASE_URL . 'admin/dichvu');
        exit;
    }


    public function hienThiDatSanTheoNgay()
    {
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }
        $startDate = $_GET['startDate'] ?? date('Y-m-d');
        $endDate = $_GET['endDate'] ?? date('Y-m-d');
        
        $danhSachDatSan = $this->datSanModel->getBookingsByDateRange($startDate, $endDate); 
       
        $this->view('admin/datsan_by_date', [
            'title' => 'Danh sách đặt sân từ ' . $startDate . ' đến ' . $endDate,
            'danhSachDatSan' => $danhSachDatSan,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function xuLyHuyDatSan()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $redirectUrl = BASE_URL . 'admin/datsan-theo-ngay';
        $startDate = $_POST['startDate'] ?? '';
        $endDate = $_POST['endDate'] ?? '';
        if ($startDate && $endDate) {
            $redirectUrl .= '?startDate=' . $startDate . '&endDate=' . $endDate;
        }

        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }

        $maDatSan = $_POST['MaDatSan'] ?? null;

        if (!$maDatSan) {
            $_SESSION['error_message'] = "Yêu cầu không hợp lệ.";
            header('Location: ' . $redirectUrl);
            exit;
        }

        $booking = $this->datSanModel->getBookingDetailsByIdForAdmin($maDatSan);

        if (!$booking) {
            $_SESSION['error_message'] = "Không tìm thấy lịch đặt sân để hủy.";
            header('Location: ' . $redirectUrl);
            exit;
        }

        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $thoiGianBatDauDatSan = new \DateTime($booking['NgayDat'] . ' ' . $booking['GioBatDau']);
        $thoiGianHienTai = new \DateTime();

        if (strpos($booking['TrangThai'], 'Đã hủy') === 0) {
            $_SESSION['error_message'] = "Lịch đặt sân này đã được hủy trước đó.";
        } elseif ($thoiGianBatDauDatSan <= $thoiGianHienTai) {
            $_SESSION['error_message'] = "Không thể hủy lịch đặt sân đã diễn ra.";
        } else {
            $role = ucfirst($_SESSION['user_role'] ?? 'Admin');
            if ($this->datSanModel->huyLichDat($maDatSan, $booking['MaKH'], $role)) {
                $_SESSION['success_message'] = "Đã hủy thành công lịch đặt sân #" . $maDatSan;
            } else {
                $_SESSION['error_message'] = "Lỗi: Không thể hủy lịch đặt sân. Vui lòng thử lại.";
            }
        }
        
        header('Location: ' . $redirectUrl);
        exit;
    }

    public function xuLyXacNhanThanhToan()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $redirectUrl = $_POST['redirect'] ?? (BASE_URL . 'admin/datsan-theo-ngay');
        $startDate = $_POST['startDate'] ?? '';
        $endDate = $_POST['endDate'] ?? '';
        
        if (!str_contains($redirectUrl, '?') && $startDate && $endDate) {
            $redirectUrl .= '?startDate=' . $startDate . '&endDate=' . $endDate;
        }

        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }

        $maDatSan = $_POST['MaDatSan'] ?? null;
        if (!$maDatSan) {
            header('Location: ' . $redirectUrl);
            exit;
        }

        if ($this->datSanModel->updateBookingStatus($maDatSan, 'Đã thanh toán')) {
            $_SESSION['admin_success'] = "Đã xác nhận thanh toán cho lịch đặt sân #{$maDatSan} thành công.";
        } else {
            $_SESSION['admin_error'] = "Có lỗi xảy ra khi cập nhật trạng thái.";
        }

        header('Location: ' . $redirectUrl);
        exit;
    }


    public function bookingDetails()
    {
        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $maDatSan = $_GET['id'] ?? null;
        if (!$maDatSan) {
            $_SESSION['admin_error'] = "Yêu cầu không hợp lệ. Thiếu mã đặt sân.";
            header('Location: ' . BASE_URL . 'admin/datsan-theo-ngay');
            exit;
        }

        $booking = $this->datSanModel->getBookingDetailsByIdForAdmin($maDatSan);

        if (!$booking) {
            $_SESSION['admin_error'] = "Không tìm thấy lịch đặt sân.";
            header('Location: ' . BASE_URL . 'admin/datsan-theo-ngay');
            exit;
        }

        $services = $this->dichVuModel->getServicesByBookingId($maDatSan);

        $this->view('admin/booking_details', [
            'title' => 'Chi Tiết Đặt Sân #' . $booking['MaDatSan'],
            'booking' => $booking,
            'services' => $services
        ]);
    }
    
    public function quanLyKhachHang()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAdmin()) {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $searchTerm = $_POST['search'] ?? '';
        $khachHangList = [];

        if (!empty($searchTerm)) {
            $khachHangList = $this->khachHangModel->searchKhachHang($searchTerm);
        } else {
            $khachHangList = $this->khachHangModel->getAllKhachHang();
        }

        $this->view('admin/khachhang', [
            'title' => 'Quản Lý Khách Hàng',
            'khachHangList' => $khachHangList,
            'searchTerm' => $searchTerm
        ]);
    }



    public function xuLyNhapHang()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!$this->isAdmin() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'home/index');
            exit;
        }

        $maDV = $_POST['MaDV'] ?? null;
        $soLuongNhap = $_POST['SoLuongNhap'] ?? null;
        $ghiChu = $_POST['GhiChu'] ?? '';

        if (empty($maDV) || empty($soLuongNhap) || !is_numeric($soLuongNhap) || $soLuongNhap <= 0) {
            $_SESSION['admin_error'] = "Vui lòng chọn dịch vụ và nhập số lượng hợp lệ.";
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }

        // Bắt đầu transaction
        $this->nhapHangModel->beginTransaction();

        try {
            // 1. Thêm vào bảng NhapHang
            $this->nhapHangModel->insertNhapHang($maDV, $soLuongNhap, $ghiChu);

            // 2. Cập nhật bảng TonKho
            $this->tonKhoModel->increaseSoLuongTon($maDV, $soLuongNhap);

            // Commit transaction
            $this->nhapHangModel->commit();

            $_SESSION['admin_success'] = "Nhập hàng thành công.";
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;

        } catch (\Exception $e) {
            // Rollback transaction nếu có lỗi
            $this->nhapHangModel->rollBack();
            error_log($e->getMessage());
            $_SESSION['admin_error'] = "Lỗi hệ thống: Không thể nhập hàng.";
            header('Location: ' . BASE_URL . 'admin/dichvu');
            exit;
        }
    }

    // ===============================================
    //           QUẢN LÝ NHÂN VIÊN (OWNER ONLY)
    // ===============================================

    public function quanLyNhanVien()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner()) {
            $_SESSION['admin_error'] = "Bạn không có quyền quản lý nhân viên.";
            header('Location: ' . BASE_URL . 'admin/dashboard');
            exit;
        }

        $staffList = $this->userModel->getAllStaff();
        $this->view('admin/nhanvien/index', [
            'title' => 'Quản Lý Nhân Viên',
            'staffList' => $staffList
        ]);
    }

    public function hienThiThemNhanVien()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner()) {
            header('Location: ' . BASE_URL . 'admin/dashboard');
            exit;
        }

        $this->view('admin/nhanvien/them', [
            'title' => 'Thêm Nhân Viên Mới'
        ]);
    }

    public function xuLyThemNhanVien()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/nhan-vien');
            exit;
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = $_POST['role'] ?? 'admin';

        if (empty($username) || empty($password)) {
            $_SESSION['admin_error'] = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu.";
            header('Location: ' . BASE_URL . 'admin/nhan-vien/them');
            exit;
        }

        // Check trùng username
        if ($this->userModel->getUserByUsername($username)) {
            $_SESSION['admin_error'] = "Tên đăng nhập đã tồn tại.";
            header('Location: ' . BASE_URL . 'admin/nhan-vien/them');
            exit;
        }

        if ($this->userModel->createAccount($username, $password, $role)) {
            $_SESSION['admin_success'] = "Đã tạo tài khoản nhân viên thành công.";
            header('Location: ' . BASE_URL . 'admin/nhan-vien');
        } else {
            $_SESSION['admin_error'] = "Có lỗi xảy ra khi tạo tài khoản.";
            header('Location: ' . BASE_URL . 'admin/nhan-vien/them');
        }
        exit;
    }

    public function xuLyXoaNhanVien()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!$this->isOwner() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'admin/nhan-vien');
            exit;
        }

        $maTK = $_POST['maTK'] ?? null;
        
        // Không cho phép xóa chính mình
        $currentUser = $this->userModel->getUserByUsername($_SESSION['username']);
        if ($currentUser && $currentUser['MaTK'] == $maTK) {
            $_SESSION['admin_error'] = "Bạn không thể xóa chính mình.";
            header('Location: ' . BASE_URL . 'admin/nhan-vien');
            exit;
        }

        if ($this->userModel->deleteAccount($maTK)) {
            $_SESSION['admin_success'] = "Đã xóa tài khoản nhân viên.";
        } else {
            $_SESSION['admin_error'] = "Có lỗi xảy ra khi xóa tài khoản.";
        }
        header('Location: ' . BASE_URL . 'admin/nhan-vien');
        exit;
    }
}


