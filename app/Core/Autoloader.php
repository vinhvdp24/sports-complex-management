<?php

spl_autoload_register(function ($className) {
    // 1. Loại bỏ Namespace gốc (ví dụ: 'App\Controllers\' -> 'Controllers\')
    $prefix = 'App\\'; 
    $baseDir = __DIR__ . '/../'; // Đường dẫn gốc của thư mục 'app/'

  
    $len = strlen($prefix);

    // Kiểm tra xem class có sử dụng namespace khớp với prefix đã định nghĩa không
    if (strncmp($prefix, $className, $len) !== 0) {
        // Nếu không khớp, bỏ qua
        return;
    }

    // 2. Lấy tên Class tương đối (ví dụ: Controllers\DatsanController)
    $relativeClass = substr($className, $len);

    // 3. Chuyển đổi tên class thành đường dẫn file (Thay thế '\' bằng '/')
    // Ví dụ: Controllers\DatsanController -> Controllers/DatsanController.php
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // 4. Nạp file (include) nếu file tồn tại
    if (file_exists($file)) {
        require_once $file;
    }
});