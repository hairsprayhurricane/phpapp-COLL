<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    
    if (empty($id)) {
        echo json_encode([
            'success' => false,
            'message' => 'ID пользователя обязателен'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    try {
        $pdo = getConnection();
        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Пользователь успешно удалён!'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Ошибка при удалении'
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