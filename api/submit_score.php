<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit;
}

require_once '../config/database.php';

try {
    // Get POST data
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate required fields
    if (!isset($input['judge_id']) || !isset($input['user_id']) || !isset($input['points'])) {
        throw new Exception('Missing required fields: judge_id, user_id, and points are required');
    }
    
    $judge_id = (int)$input['judge_id'];
    $user_id = (int)$input['user_id'];
    $points = (int)$input['points'];
    $comments = isset($input['comments']) ? trim($input['comments']) : '';
    
    // Validate data
    if ($judge_id <= 0 || $user_id <= 0) {
        throw new Exception('Invalid judge_id or user_id');
    }
    
    if ($points < 0 || $points > 100) {
        throw new Exception('Points must be between 0 and 100');
    }
    
    // Connect to database
    $database = new Database();
    $db = $database->getConnection();
    
    // Start transaction
    $db->beginTransaction();
    
    // Check if judge exists and is active
    $judge_check = "SELECT id, username, display_name FROM judges WHERE id = ? AND is_active = 1";
    $stmt = $db->prepare($judge_check);
    $stmt->execute([$judge_id]);
    $judge = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$judge) {
        throw new Exception('Invalid or inactive judge');
    }
    
    // Check if participant exists and is active
    $user_check = "SELECT id, username, full_name FROM users WHERE id = ? AND is_active = 1";
    $stmt = $db->prepare($user_check);
    $stmt->execute([$user_id]);
    $participant = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$participant) {
        throw new Exception('Invalid or inactive participant');
    }
    
    // Check if judge has already scored this participant
    $existing_score = "SELECT id FROM scores WHERE judge_id = ? AND user_id = ?";
    $stmt = $db->prepare($existing_score);
    $stmt->execute([$judge_id, $user_id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        // Update existing score
        $update_query = "UPDATE scores SET 
                        points = ?, 
                        comments = ?, 
                        updated_at = CURRENT_TIMESTAMP 
                        WHERE judge_id = ? AND user_id = ?";
        $stmt = $db->prepare($update_query);
        $stmt->execute([$points, $comments, $judge_id, $user_id]);
        
        $action = 'updated';
        $score_id = $existing['id'];
    } else {
        // Insert new score
        $insert_query = "INSERT INTO scores (judge_id, user_id, points, comments, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
        $stmt = $db->prepare($insert_query);
        $stmt->execute([$judge_id, $user_id, $points, $comments]);
        
        $action = 'submitted';
        $score_id = $db->lastInsertId();
    }
    
    // Commit transaction
    $db->commit();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => "Score {$action} successfully",
        'data' => [
            'score_id' => $score_id,
            'judge' => $judge['display_name'],
            'participant' => $participant['full_name'],
            'points' => $points,
            'comments' => $comments,
            'action' => $action,
            'timestamp' => time()
        ]
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($db) && $db->inTransaction()) {
        $db->rollback();
    }
    
    // Return error response
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting score: ' . $e->getMessage(),
        'timestamp' => time()
    ]);
}
?>