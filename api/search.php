<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $searchName = isset($_POST['name']) ? trim($_POST['name']) : '';
    $searchPassword = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    try {
        $pdo = getConnection();
        
        if (!empty($searchName) || !empty($searchPassword)) {
            $sql = "SELECT id, name, message, created_at, password FROM users WHERE 1=1";
            $params = [];
            
            if (!empty($searchName)) {
                $sql .= " AND name LIKE :name";
                $params[':name'] = "%" . $searchName . "%";
            }
            
            $stmt = $pdo->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $results = $stmt->fetchAll();
            
            $users = [];
            foreach ($results as $row) {
                if (!empty($searchPassword)) {
                    if (password_verify($searchPassword, $row['password'])) {
                        unset($row['password']);
                        $users[] = $row;
                    }
                } else {
                    unset($row['password']);
                    $users[] = $row;
                }
            }
        } else {
            $stmt = $pdo->query("SELECT id, name, message, created_at FROM users ORDER BY created_at DESC");
            $users = $stmt->fetchAll();
        }
        
        echo json_encode([
            'success' => true,
            'users' => $users
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Ошибка сервера: ' . $e->getMessage(),
            'users' => []
        ], JSON_UNESCAPED_UNICODE);
    }
}
?>