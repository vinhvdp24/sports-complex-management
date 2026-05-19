<?php
namespace App\Controllers; // <-- BẮT BUỘC PHẢI CÓ DÒNG NÀY

// Đảm bảo Controller base class được nạp (để dùng Controller::view)
require_once __DIR__ . '/../Core/Controller.php'; 

use App\Core\Controller; 
use App\Models\DanhGiaModel;

/**
 * Controller xử lý các tác vụ liên quan đến Trang Chủ (Home).
 */
class HomeController extends Controller { // <-- Bắt buộc phải kế thừa Controller
    
    /**
     * Phương thức mặc định: Hiển thị trang chủ.
     */
    public function index() {
        // Lấy các đánh giá gần đây
        $danhGiaModel = new DanhGiaModel();
        $recentReviews = $danhGiaModel->getRecentReviews(3);

        // Sử dụng phương thức view() của Controller base để tự động nạp Header/Footer
        $this->view('home/index', [
            'title' => 'Trang Chủ Hệ Thống Quản Lý Sân Vận Động',
            'recentReviews' => $recentReviews
        ]);
    }
    
    // Bạn có thể thêm các phương thức khác ở đây (ví dụ: about(), contact()...)
}