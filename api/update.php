<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    if (empty($id) || empty($name)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID и имя пользователя обязательны'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    try {
        $pdo = getConnection();
        
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name = :name, password = :password, message = :message WHERE id = :id");
            $stmt->bindParam(':password', $hashedPassword);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, message = :message WHERE id = :id");
        }
        
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Пользователь успешно обновлен'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при обновлении ЧМО'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка сервера ЛОХ: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>