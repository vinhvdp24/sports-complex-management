<?php
namespace App\Core;

class Session
{
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Thiết lập một giá trị vào Session.
     * @param string $key Khóa.
     * @param mixed $value Giá trị.
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Lấy giá trị từ Session.
     * @param string $key Khóa.
     * @param mixed $default Giá trị mặc định nếu khóa không tồn tại.
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Kiểm tra xem một khóa có tồn tại trong Session hay không.
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Xóa một khóa khỏi Session.
     */
    public static function remove($key)
    {
        if (self::has($key)) {
            unset($_SESSION[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * Xóa toàn bộ dữ liệu Session và kết thúc phiên.
     */
    public static function destroy()
    {
        session_unset();
        session_destroy();
    }
}