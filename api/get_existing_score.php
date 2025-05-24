<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../config/database.php';

if ($_POST && isset($_POST['judge_id']) && isset($_POST['user_id'])) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $judge_id = (int)$_POST['judge_id'];
        $user_id = (int)$_POST['user_id'];
        
        $query = "SELECT * FROM scores WHERE judge_id = ? AND user_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$judge_id, $user_id]);
        $score = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'score' => $score
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error fetching score: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>