<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '88888888');
define('DB_NAME', 'user_management');

function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $params = session_get_cookie_params();
        $params = array_replace($params, [
            'httponly' => true,
            'secure' => $secure,
            'samesite' => 'Lax',
        ]);
        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params($params);
        } else {
            session_set_cookie_params($params['lifetime'], $params['path'] . '; samesite=' . $params['samesite'], $params['domain'], $params['secure'], $params['httponly']);
        }
        if (!headers_sent()) {
            @session_start();
        }
    }
}

function cookie_set($name, $value, $ttl = 0, $path = '/', $domain = '', $secure = null, $httponly = true, $samesite = 'Lax') {
    $secure = $secure ?? ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443));
    $expires = $ttl > 0 ? (time() + (int)$ttl) : 0;
    if (PHP_VERSION_ID >= 70300) {
        return setcookie($name, $value, [
            'expires' => $expires,
            'path' => $path,
            'domain' => $domain ?: '',
            'secure' => (bool)$secure,
            'httponly' => (bool)$httponly,
            'samesite' => $samesite,
        ]);
    } else {
        return setcookie($name, $value, $expires, $path . '; samesite=' . $samesite, $domain ?: '', (bool)$secure, (bool)$httponly);
    }
}

function cookie_get($name, $default = null) {
    return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
}

function cookie_delete($name, $path = '/', $domain = '') {
    return cookie_set($name, '', -3600, $path, $domain);
}

initSession();

function initDatabase() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $pdo->exec("USE " . DB_NAME);
        
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            message TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $pdo->exec($sql);
        
        return true;
    } catch (PDOException $e) {
        error_log("Database initialization error: " . $e->getMessage());
        return false;
    }
}

function getConnection() {
    try {
        initDatabase();
        
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
        
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
    } catch (PDOException $e) {
        throw new Exception("Connection failed: " . $e->getMessage());
    }
}
?>