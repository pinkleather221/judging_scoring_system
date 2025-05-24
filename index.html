<?php
$page_title = 'Public Scoreboard - Judge Scoring System';
$css_path = 'assets/css/style.css';
$custom_js = 'assets/js/scoreboard.js';

require_once 'config/database.php';
include 'includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Get scoreboard data with total points
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
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="#">
            <i class="fas fa-trophy me-2"></i>Judge Scoring System
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link" href="admin/">
                <i class="fas fa-cog me-1"></i>Admin Panel
            </a>
            <a class="nav-link" href="judge/">
                <i class="fas fa-gavel me-1"></i>Judge Portal
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="main-container">
        <div class="text-center mb-4">
            <h1 class="display-4 mb-3">
                <i class="fas fa-trophy text-warning me-3"></i>
                Live Scoreboard
            </h1>
            <p class="lead text-muted">Real-time participant rankings</p>
            <div class="d-flex justify-content-center align-items-center">
                <span class="badge bg-success me-2">
                    <i class="fas fa-sync-alt me-1"></i>Auto-refresh: 30s
                </span>
                <span class="badge bg-info">
                    Last Updated: <span id="lastUpdate"><?php echo date('H:i:s'); ?></span>
                </span>
            </div>
        </div>

        <div id="scoreboardContainer">
            <?php if (empty($scoreboard)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">No participants yet</h3>
                    <p class="text-muted">Participants will appear here once judges start scoring.</p>
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($scoreboard as $index => $participant): ?>
                        <div class="col-lg-6 mb-3">
                            <div class="scoreboard-item animate-in" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                <div class="rank-badge rank-<?php echo min($index + 1, 3); ?>">
                                    #<?php echo $index + 1; ?>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="participant-info">
                                        <h4 class="mb-1 fw-bold text-primary">
                                            <?php echo htmlspecialchars($participant['full_name']); ?>
                                        </h4>
                                        <p class="mb-1 text-muted">
                                            <i class="fas fa-user me-1"></i>
                                            @<?php echo htmlspecialchars($participant['username']); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="fas fa-gavel me-1"></i>
                                            Scored by <?php echo $participant['total_judges']; ?> judge(s)
                                        </small>
                                    </div>
                                    
                                    <div class="text-end">
                                        <div class="points-display" data-points="<?php echo $participant['total_points']; ?>">
                                            <?php echo $participant['total_points']; ?>
                                        </div>
                                        <small class="text-muted">Total Points</small>
                                    </div>
                                </div>
                                
                                <div class="progress mt-3" style="height: 8px;">
                                    <div class="progress-bar bg-gradient" 
                                         role="progressbar" 
                                         style="width: <?php echo $scoreboard[0]['total_points'] > 0 ? ($participant['total_points'] / $scoreboard[0]['total_points']) * 100 : 0; ?>%">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="text-center mt-4">
            <button class="btn btn-primary-custom btn-custom" onclick="refreshScoreboard()">
                <i class="fas fa-sync-alt me-2"></i>Refresh Now
            </button>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
