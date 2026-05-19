<?php
namespace App\Core; 

class Controller
{
    protected function view($viewPath, $data = [])
    {
        // Chuyển mảng $data thành các biến cục bộ trong View và Layouts
        // Ví dụ: $data = ['title' => '...', 'sanBongList' => [...]] -> tạo biến $title, $sanBongList
        extract($data); 
        
        // 1. Định nghĩa Base Path (đi từ app/Core/ lên thư mục gốc, rồi vào views/)
        // __DIR__ . '/../../views/' là đường dẫn TỪ App/Core/ đến views/ (ngang hàng với app/)
        $basePath = __DIR__ . '/../../views/'; 
        $viewFile = $basePath . $viewPath . '.php'; // Đường dẫn đầy đủ đến View (vd: .../views/datsan/chonsan.php)

        // 2. Nạp Header Layout (views/layouts/header.php)
        $headerFile = $basePath . 'layouts/header.php';
        if (file_exists($headerFile)) {
            require_once $headerFile;
        } else {
            // Lỗi nghiêm trọng nếu không tìm thấy header, giúp debug dễ hơn
            die("Lỗi: Không tìm thấy file Layout Header tại: $headerFile");
        }

    
        if (file_exists($viewFile)) {
            require_once $viewFile; 
        } else {
          
            die("Lỗi Fatal: View '$viewPath' không tồn tại. Đã tìm kiếm tại đường dẫn: $viewFile");
        }

   
        $footerFile = $basePath . 'layouts/footer.php';
        if (file_exists($footerFile)) {
            require_once $footerFile;
        } else {

            die("Lỗi: Không tìm thấy file Layout Footer tại: $footerFile");
        }
    }
    
    /**
     * Hàm dùng để trả về JSON (sử dụng trong AJAX)
     * @param array $data Dữ liệu cần mã hóa thành JSON
     */
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}