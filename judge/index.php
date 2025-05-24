<?php
$page_title = 'Judge Portal - Score Participants';
$css_path = '../assets/css/style.css';

require_once '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Handle score submission
$message = '';
$message_type = '';

if ($_POST && isset($_POST['submit_score'])) {
    $judge_id = (int)$_POST['judge_id'];
    $user_id = (int)$_POST['user_id'];
    $points = (int)$_POST['points'];
    $comments = trim($_POST['comments']);
    
    if ($judge_id && $user_id && $points >= 0 && $points <= 100) {
        try {
            // Check if score already exists
            $check_query = "SELECT id FROM scores WHERE judge_id = ? AND user_id = ?";
            $check_stmt = $db->prepare($check_query);
            $check_stmt->execute([$judge_id, $user_id]);
            
            if ($check_stmt->rowCount() > 0) {
                // Update existing score
                $update_query = "UPDATE scores SET points = ?, comments = ?, updated_at = CURRENT_TIMESTAMP WHERE judge_id = ? AND user_id = ?";
                $update_stmt = $db->prepare($update_query);
                $update_stmt->execute([$points, $comments, $judge_id, $user_id]);
                $message = "Score updated successfully!";
            } else {
                // Insert new score
                $insert_query = "INSERT INTO scores (judge_id, user_id, points, comments) VALUES (?, ?, ?, ?)";
                $insert_stmt = $db->prepare($insert_query);
                $insert_stmt->execute([$judge_id, $user_id, $points, $comments]);
                $message = "Score submitted successfully!";
            }
            $message_type = "success";
        } catch (PDOException $e) {
            $message = "Error submitting score: " . $e->getMessage();
            $message_type = "danger";
        }
    } else {
        $message = "Please fill in all required fields with valid values!";
        $message_type = "warning";
    }
}

// Get all judges
$judges_query = "SELECT * FROM judges WHERE is_active = 1 ORDER BY display_name";
$judges_stmt = $db->prepare($judges_query);
$judges_stmt->execute();
$judges = $judges_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all users
$users_query = "SELECT * FROM users WHERE is_active = 1 ORDER BY full_name";
$users_stmt = $db->prepare($users_query);
$users_stmt->execute();
$users = $users_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get existing scores for selected judge (if any)
$selected_judge_id = isset($_GET['judge_id']) ? (int)$_GET['judge_id'] : 0;
$existing_scores = [];

if ($selected_judge_id) {
    $scores_query = "SELECT s.*, u.full_name, u.username 
                     FROM scores s 
                     JOIN users u ON s.user_id = u.id 
                     WHERE s.judge_id = ? 
                     ORDER BY s.updated_at DESC";
    $scores_stmt = $db->prepare($scores_query);
    $scores_stmt->execute([$selected_judge_id]);
    $existing_scores = $scores_stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="../">
            <i class="fas fa-trophy me-2"></i>Judge Scoring System
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="../admin/">
                <i class="fas fa-cog me-1"></i>Admin Panel
            </a>
            <a class="nav-link active" href="#">
                <i class="fas fa-gavel me-1"></i>Judge Portal
            </a>
            <a class="nav-link" href="../">
                <i class="fas fa-chart-line me-1"></i>Scoreboard
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="main-container">
        <div class="text-center mb-4">
            <h1 class="display-4 mb-3">
                <i class="fas fa-gavel text-primary me-3"></i>
                Judge Portal
            </h1>
            <p class="lead text-muted">Score participants and manage evaluations</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-custom alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'danger' ? 'exclamation-circle' : 'exclamation-triangle'); ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (empty($judges) || empty($users)): ?>
            <div class="alert alert-warning alert-custom">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Setup Required:</strong> 
                <?php if (empty($judges)): ?>
                    No judges have been added yet. 
                <?php endif; ?>
                <?php if (empty($users)): ?>
                    No participants have been added yet. 
                <?php endif; ?>
                Please contact the administrator to set up the system.
            </div>
        <?php else: ?>
            <div class="row">
                <!-- Score Submission Form -->
                <div class="col-lg-8 mb-4">
                    <div class="card card-custom">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">
                                <i class="fas fa-star me-2"></i>Submit Score
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="POST" id="scoreForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="judge_id" class="form-label">Select Judge *</label>
                                        <select class="form-select form-control-custom" id="judge_id" name="judge_id" required onchange="updateJudgeScores()">
                                            <option value="">Choose a judge...</option>
                                            <?php foreach ($judges as $judge): ?>
                                                <option value="<?php echo $judge['id']; ?>" 
                                                        <?php echo $selected_judge_id == $judge['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($judge['display_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="user_id" class="form-label">Select Participant *</label>
                                        <select class="form-select form-control-custom" id="user_id" name="user_id" required onchange="loadExistingScore()">
                                            <option value="">Choose a participant...</option>
                                            <?php foreach ($users as $user): ?>
                                                <option value="<?php echo $user['id']; ?>">
                                                    <?php echo htmlspecialchars($user['full_name']); ?> (@<?php echo htmlspecialchars($user['username']); ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="points" class="form-label">Score (0-100) *</label>
                                    <div class="input-group">
                                        <input type="range" class="form-range" id="pointsRange" 
                                               min="0" max="100" value="0" oninput="updatePointsInput()">
                                        <input type="number" class="form-control form-control-custom" 
                                               id="points" name="points" min="0" max="100" required 
                                               oninput="updatePointsRange()" style="max-width: 100px;">
                                    </div>
                                    <div class="form-text">
                                        <span id="scoreDescription">Enter a score between 0 and 100</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="comments" class="form-label">Comments (Optional)</label>
                                    <textarea class="form-control form-control-custom" id="comments" name="comments" 
                                              rows="3" placeholder="Add any comments about the participant's performance..."></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" name="submit_score" class="btn btn-primary-custom btn-custom">
                                        <i class="fas fa-save me-2"></i>Submit Score
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Judge's Previous Scores -->
                <div class="col-lg-4 mb-4">
                    <div class="card card-custom">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Previous Scores
                            </h5>
                        </div>
                        <div class="card-body" id="previousScores">
                            <?php if ($selected_judge_id && !empty($existing_scores)): ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($existing_scores as $score): ?>
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($score['full_name']); ?></h6>
                                                <small class="text-muted">@<?php echo htmlspecialchars($score['username']); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-primary fs-6"><?php echo $score['points']; ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-3">
                                    <i class="fas fa-clipboard-list fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Select a judge to view previous scores</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- All Participants Quick View -->
            <div class="card card-custom">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-users me-2"></i>All Participants
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($users as $user): ?>
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="card border-0 bg-light">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
                                        <h6 class="card-title"><?php echo htmlspecialchars($user['full_name']); ?></h6>
                                        <p class="card-text text-muted">@<?php echo htmlspecialchars($user['username']); ?></p>
                                        <button class="btn btn-sm btn-outline-primary" 
                                                onclick="selectParticipant(<?php echo $user['id']; ?>)">
                                            <i class="fas fa-star me-1"></i>Score
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function updatePointsInput() {
    const range = document.getElementById('pointsRange');
    const input = document.getElementById('points');
    const description = document.getElementById('scoreDescription');
    
    input.value = range.value;
    
    // Update description based on score
    const score = parseInt(range.value);
    if (score >= 90) {
        description.textContent = 'Excellent performance (90-100)';
        description.className = 'form-text text-success';
    } else if (score >= 75) {
        description.textContent = 'Good performance (75-89)';
        description.className = 'form-text text-info';
    } else if (score >= 60) {
        description.textContent = 'Average performance (60-74)';
        description.className = 'form-text text-warning';
    } else if (score > 0) {
        description.textContent = 'Below average performance (1-59)';
        description.className = 'form-text text-danger';
    } else {
        description.textContent = 'No score assigned (0)';
        description.className = 'form-text text-muted';
    }
}

function updatePointsRange() {
    const input = document.getElementById('points');
    const range = document.getElementById('pointsRange');
    
    if (input.value >= 0 && input.value <= 100) {
        range.value = input.value;
        updatePointsInput();
    }
}

function selectParticipant(userId) {
    document.getElementById('user_id').value = userId;
    document.getElementById('user_id').scrollIntoView({behavior: 'smooth'});
}

function updateJudgeScores() {
    const judgeId = document.getElementById('judge_id').value;
    if (judgeId) {
        window.location.href = '?judge_id=' + judgeId;
    }
}

function loadExistingScore() {
    const judgeId = document.getElementById('judge_id').value;
    const userId = document.getElementById('user_id').value;
    
    if (judgeId && userId) {
        fetch('../api/get_existing_score.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'judge_id=' + judgeId + '&user_id=' + userId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.score) {
                document.getElementById('points').value = data.score.points;
                document.getElementById('pointsRange').value = data.score.points;
                document.getElementById('comments').value = data.score.comments || '';
                updatePointsInput();
            } else {
                document.getElementById('points').value = '';
                document.getElementById('pointsRange').value = 0;
                document.getElementById('comments').value = '';
                updatePointsInput();
            }
        })
        .catch(error => {
            console.error('Error loading existing score:', error);
        });
    }
}

// Initialize score description
document.addEventListener('DOMContentLoaded', function() {
    updatePointsInput();
});
</script>

<?php include '../includes/footer.php'; ?>