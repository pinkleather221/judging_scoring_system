<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT u.id, u.username, u.full_name, 
                     COALESCE(SUM(s.points), 0) as total_points,
                     COUNT(s.id) as total_judges
              FROM users u 
              LEFT JOIN scores s ON u.id = s.user_id 
              WHERE u.is_active = 1
              GROUP BY u.id, u.username, u.full_name 
              ORDER BY total_points DESC, u.full_name ASC";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $scoreboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'scoreboard' => $scoreboard,
        'timestamp' => time()
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching scoreboard data: ' . $e->getMessage()
    ]);
}
?>