<?php
namespace App\Core;

class Router {
    private $routes = []; // Mảng chứa các đường dẫn đã đăng ký

    // 1. Hàm đăng ký đường dẫn POST (Giữ nguyên)
    public function post($url, $action) {
        $url = trim($url, '/');
        $this->routes['POST'][$url] = $action;
    }

    // 2. Hàm đăng ký đường dẫn GET (Giữ nguyên)
    public function get($url, $action) {
        $url = trim($url, '/');
        $this->routes['GET'][$url] = $action;
    }

    // 3. Hàm xử lý điều hướng chính
    public function dieuHuong() {
        // Lấy URL từ .htaccess truyền qua (?url=...)
        $url = isset($_GET['url']) ? trim($_GET['url'], '/') : 'home/index'; // Đổi mặc định thành home/index
        $method = $_SERVER['REQUEST_METHOD']; // GET hoặc POST

        // **********************************************************
        // *** TẢI CÁC LỚP CORE & MODELS TRƯỚC (CHO ROUTER ĐỘNG VÀ THỦ CÔNG) ***
        // *******************************************************************
        require_once __DIR__ . '/Session.php';
        require_once __DIR__ . '/Controller.php';
        require_once __DIR__ . '/../Models/Database.php';
        require_once __DIR__ . '/../Models/SanModel.php';


        // ==========================================================
        // *** KIỂM TRA 1: Nếu URL đã được đăng ký thủ công (ƯU TIÊN TUYỆT ĐỐI) ***
        // ==========================================================
        if (isset($this->routes[$method][$url])) {
            $action = $this->routes[$method][$url];
            
            // Xử lý action
            if (is_array($action) && count($action) == 2) {
                $className = is_string($action[0]) ? $action[0] : '';
                $methodName = $action[1];

                $controllerNameParts = explode('\\', $className);
                $controllerName = end($controllerNameParts);
                $controllerFile = __DIR__ . "/../Controllers/" . $controllerName . ".php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile; 
                }

                if (class_exists($className)) {
                    $controller = new $className();
                    if (method_exists($controller, $methodName)) {
                        call_user_func([$controller, $methodName]);
                        return; // *** THOÁT NGAY NẾU ROUTE THỦ CÔNG KHỚP ***
                    }
                }
            } elseif (is_string($action) && strpos($action, '@') !== false) {
                // Xử lý action dạng 'Controller@method'
                list($controllerName, $methodName) = explode('@', $action);
                $className = "App\\Controllers\\" . $controllerName;
                $controllerFile = __DIR__ . "/../Controllers/" . $controllerName . ".php";

                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                }

                if (class_exists($className)) {
                    $controller = new $className();
                    if (method_exists($controller, $methodName)) {
                        call_user_func([$controller, $methodName]);
                        return; // *** THOÁT NGAY NẾU ROUTE THỦ CÔNG KHỚP ***
                    }
                }
            }
        }

        // ==========================================================
        // *** KIỂM TRA 2: XỬ LÝ CUSTOM URL GÂY LỖI CHO ROUTER ĐỘNG (Ví dụ: forgot-password) ***
        // ==========================================================
        $actionFound = false;
        $controllerClass = null;
        $actionName = null;
        $params = []; // Initialize params array

        // ⚠️ Logic gán cứng các route tùy chỉnh 
        if ($url === 'forgot-password' || $url === 'reset-password') {
            $controllerName = 'AuthController';
            $controllerClass = "App\\Controllers\\" . $controllerName;
            
            // Gán cứng Action dựa trên URL và Method
            if ($url === 'forgot-password') {
                $actionName = $method === 'POST' ? 'guiEmailReset' : 'hienThiQuenMatKhau';
            } else { // reset-password
                $actionName = $method === 'POST' ? 'datLaiMatKhau' : 'hienThiDatLaiMatKhau';
            }
            $actionFound = true;
            
        } elseif (strpos($url, '-') !== false && strpos($url, '/') === false) {
            // Logic cho URL dạng controller-action (Ví dụ: user-indexUser)
            $urlParts = explode('-', $url);
            $controllerName = ucfirst($urlParts[0]) . 'Controller';
            $actionName = $urlParts[1] ?? 'index'; // Mặc định là 'index' nếu thiếu phần action
            $controllerClass = "App\\Controllers\\" . $controllerName;
            $actionFound = true;
        } elseif (strpos($url, 'store/product/') === 0) {
            $urlParts = explode('/', $url);
            if (count($urlParts) >= 3 && $urlParts[0] === 'store' && $urlParts[1] === 'product') {
                $controllerName = 'StoreController';
                $actionName = 'productDetail'; 
                $params = [urldecode($urlParts[2])]; // Pass the product ID as a parameter
                $controllerClass = "App\\Controllers\\" . $controllerName;
                $actionFound = true;
            } else {
                // Fallback to generic Controller/Action if it's 'store/' but not 'store/product/'
                $urlParts = explode('/', $url);
                $controllerName = ucfirst($urlParts[0]) . 'Controller'; 
                $actionName = $urlParts[1] ?? 'index';
                $controllerClass = "App\\Controllers\\" . $controllerName;
                $actionFound = true;
            }
        } elseif (strpos($url, 'store/category/') === 0) {
            $urlParts = explode('/', $url);
            if (count($urlParts) >= 3 && $urlParts[0] === 'store' && $urlParts[1] === 'category') {
                $controllerName = 'StoreController';
            $actionName = 'index'; // Call the index method
                $params = [urldecode($urlParts[2])]; // Pass the category as a parameter
                $controllerClass = "App\\Controllers\\" . $controllerName;
                $actionFound = true;
            }
        } elseif (strpos($url, 'user/order-details/') === 0) {
            $urlParts = explode('/', $url);
            if (count($urlParts) >= 3) {
                $controllerName = 'UserController';
                $actionName = 'orderDetails'; 
                $params = [$urlParts[2]]; // Pass the order ID
                $controllerClass = "App\\Controllers\\" . $controllerName;
                $actionFound = true;
            }
        } elseif (strpos($url, '/') !== false) {
            // Đây là logic phân tích Router Động mặc định [Controller/Action]
            $urlParts = explode('/', $url);
            $controllerName = ucfirst($urlParts[0]) . 'Controller'; 
            $actionName = $urlParts[1] ?? 'index';
            $controllerClass = "App\\Controllers\\" . $controllerName;
            $actionFound = true;

        } else {
            // URL chỉ có một thành phần duy nhất (ví dụ: /home)
            // Chạy logic Router Động gốc
            $urlChuanHoa = str_replace('-', ' ', $url); 
            $urlChuanHoa = ucwords($urlChuanHoa);
            $urlChuanHoa = str_replace(' ', '', $urlChuanHoa);
            
            $urlArr = explode('/', $urlChuanHoa);
            
            $controllerName = ucfirst($urlArr[0] ?? 'Home') . 'Controller'; 
            $actionName = $urlArr[1] ?? 'index'; 
            $controllerClass = "App\\Controllers\\" . $controllerName;
            $actionFound = true;
        }

        // ==========================================================
        // *** THỰC THI ROUTER ĐỘNG (FALLBACK) ***
        // ==========================================================
        
        if ($actionFound) {
            $controllerFile = __DIR__ . "/../Controllers/" . $controllerName . ".php";
            
            if (file_exists($controllerFile)) {
                require_once $controllerFile; // Tải Controller Admin, Auth, Home
    
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $actionName)) {
                        call_user_func_array([$controller, $actionName], $params); // Changed to call_user_func_array
                        return; // *** THOÁT NGAY ***
                    } else {
                         // Lỗi 404 Action
                         $this->pageNotFound("Không tìm thấy hàm '$actionName' trong '$controllerName'.");
                    }
                } else {
                     // Lỗi 404 Controller Class
                     $this->pageNotFound("Tên class '$controllerClass' không tồn tại. (Kiểm tra Namespace)");
                }
            }
        }
        
        // Nếu không có logic nào khớp và không tìm thấy file Controller
        $this->pageNotFound("Không tìm thấy Controller '$controllerName'. URL: " . $url);
    }
    
    
    /**
     * Hàm hiển thị trang lỗi 404.
     * @param string $message Lời nhắn tùy chỉnh (không bắt buộc)
     */
    protected function pageNotFound($message = 'Trang bạn yêu cầu không tồn tại.') {
        http_response_code(404);
        // Bạn có thể ghi log lại $message ở đây nếu muốn
        // error_log($message);
        require_once __DIR__ . '/../../views/404_not_found.php';
        exit();
    }
}