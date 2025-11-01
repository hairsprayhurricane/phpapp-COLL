<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($name) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Имя пользователя и пароль обязательны'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    try {
        $pdo = getConnection();
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, password, message) VALUES (:name, :password, :message)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':message', $message);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Пользователь зарегистрирован'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при регистрации, попробуйте УБИТЬ СЕБЯ'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка сервера: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>