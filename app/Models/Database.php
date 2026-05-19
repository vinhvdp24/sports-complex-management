<?php
namespace App\Models;

use \PDO;
use \PDOException; 

require_once __DIR__ . '/../../config.php';

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private static $conn;

    /**
     * Phương thức kết nối và trả về đối tượng PDO
     * @return PDO|null Đối tượng PDO nếu kết nối thành công, ngược lại là null
     */
    public function connect() {
        // Nếu kết nối đã tồn tại, trả về kết nối đó (tránh kết nối lại nhiều lần)
        if (self::$conn !== null) {
            return self::$conn;
        }

        self::$conn = null;
        $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8";


        try {
            // Cần chắc chắn rằng PDO đã được import (use \PDO;)
            self::$conn = new PDO($dsn, $this->username, $this->password);
            
            // Thiết lập chế độ báo lỗi
            self::$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch(PDOException $exception) {
            echo "Lỗi kết nối CSDL: " . $exception->getMessage();
            throw $exception;
        }

        return self::$conn;
    }
    
    /**
     * Thực thi truy vấn SQL với Prepared Statement.
     * Phương thức này được gọi bởi các Model con (như SanBongModel).
     * @param string $sql Câu lệnh SQL
     * @param array $params Mảng các tham số cho prepared statement
     * @return \PDOStatement|null 
     */
public function query($sql, $params = []) {
    try {
        $pdo = $this->connect();
        $stmt = $pdo->prepare($sql);

        if (!empty($params)) {
            $firstKey = array_key_first($params);

            if (is_string($firstKey)) {
                // Named placeholder :param
                foreach ($params as $param => $value) {
                    $stmt->bindValue($param, $value, \PDO::PARAM_STR);
                }
                $stmt->execute();
            } else {
                // Positional placeholder ?
                $stmt->execute($params);
            }
        } else {
            $stmt->execute();
        }

        return $stmt;
    } catch (\PDOException $e) {
        error_log("SQL Error: " . $e->getMessage());
        throw $e;
    }
}

    public function beginTransaction() {
        return $this->connect()->beginTransaction();
    }

    public function commit() {
        return $this->connect()->commit();
    }

    public function rollBack() {
        return $this->connect()->rollBack();
    }
    
    public function inTransaction() {
        return $this->connect()->inTransaction();
    }

    public function lastInsertId() {
        return $this->connect()->lastInsertId();
    }
}