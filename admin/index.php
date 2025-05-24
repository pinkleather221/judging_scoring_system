<?php
$page_title = 'Admin Panel - Judge Management';
$css_path = '../assets/css/style.css';

require_once '../config/database.php';
include '../includes/header.php';

$database = new Database();
$db = $database->getConnection();

// Handle form submissions
$message = '';
$message_type = '';

if ($_POST) {
    if (isset($_POST['add_judge'])) {
        $username = trim($_POST['username']);
        $display_name = trim($_POST['display_name']);
        
        if (!empty($username) && !empty($display_name)) {
            try {
                $query = "INSERT INTO judges (username, display_name) VALUES (?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$username, $display_name]);
                
                $message = "Judge added successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Username already exists!";
                    $message_type = "danger";
                } else {
                    $message = "Error adding judge: " . $e->getMessage();
                    $message_type = "danger";
                }
            }
        } else {
            $message = "Please fill in all fields!";
            $message_type = "warning";
        }
    }
    
    if (isset($_POST['add_user'])) {
        $username = trim($_POST['user_username']);
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        
        if (!empty($username) && !empty($full_name)) {
            try {
                $query = "INSERT INTO users (username, full_name, email) VALUES (?, ?, ?)";
                $stmt = $db->prepare($query);
                $stmt->execute([$username, $full_name, $email]);
                
                $message = "Participant added successfully!";
                $message_type = "success";
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $message = "Username already exists!";
                    $message_type = "danger";
                } else {
                    $message = "Error adding participant: " . $e->getMessage();
                    $message_type = "danger";
                }
            }
        } else {
            $message = "Please fill in required fields!";
            $message_type = "warning";
        }
    }
}

// Get all judges
$query = "SELECT * FROM judges ORDER BY created_at DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$judges = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all users
$query = "SELECT * FROM users ORDER BY registration_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
    <div class="container">
        <a class="navbar-brand" href="../">
            <i class="fas fa-trophy me-2"></i>Judge Scoring System
        </a>
        <div class="navbar-nav ms-auto">
            <a class="nav-link active" href="#">
                <i class="fas fa-cog me-1"></i>Admin Panel
            </a>
            <a class="nav-link" href="../judge/">
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
                <i class="fas fa-cog text-primary me-3"></i>
                Admin Panel
            </h1>
            <p class="lead text-muted">Manage judges and participants</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-custom alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : ($message_type == 'danger' ? 'exclamation-circle' : 'exclamation-triangle'); ?> me-2"></i>
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Add Judge Form -->
            <div class="col-lg-6 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Add New Judge
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username *</label>
                                <input type="text" class="form-control form-control-custom" 
                                       id="username" name="username" required 
                                       placeholder="e.g., judge4">
                            </div>
                            <div class="mb-3">
                                <label for="display_name" class="form-label">Display Name *</label>
                                <input type="text" class="form-control form-control-custom" 
                                       id="display_name" name="display_name" required 
                                       placeholder="e.g., Dr. John Smith">
                            </div>
                            <button type="submit" name="add_judge" class="btn btn-primary-custom btn-custom w-100">
                                <i class="fas fa-plus me-2"></i>Add Judge
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Participant Form -->
            <div class="col-lg-6 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-user-plus me-2"></i>Add New Participant
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="user_username" class="form-label">Username *</label>
                                <input type="text" class="form-control form-control-custom" 
                                       id="user_username" name="user_username" required 
                                       placeholder="e.g., participant6">
                            </div>
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name *</label>
                                <input type="text" class="form-control form-control-custom" 
                                       id="full_name" name="full_name" required 
                                       placeholder="e.g., John Doe">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control form-control-custom" 
                                       id="email" name="email" 
                                       placeholder="john.doe@email.com">
                            </div>
                            <button type="submit" name="add_user" class="btn btn-success btn-custom w-100">
                                <i class="fas fa-plus me-2"></i>Add Participant
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Current Judges -->
            <div class="col-lg-6 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-info text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-gavel me-2"></i>Current Judges (<?php echo count($judges); ?>)
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($judges)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-gavel fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No judges added yet</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($judges as $judge): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($judge['display_name']); ?></h6>
                                            <small class="text-muted">@<?php echo htmlspecialchars($judge['username']); ?></small>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?php echo $judge['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $judge['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Current Participants -->
            <div class="col-lg-6 mb-4">
                <div class="card card-custom">
                    <div class="card-header bg-warning text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-users me-2"></i>Current Participants (<?php echo count($users); ?>)
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php if (empty($users)): ?>
                            <div class="text-center py-3">
                                <i class="fas fa-users fa-2x text-muted mb-3"></i>
                                <p class="text-muted">No participants added yet</p>
                            </div>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($users as $user): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($user['full_name']); ?></h6>
                                            <small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                            <?php if ($user['email']): ?>
                                                <br><small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="badge bg-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card card-custom">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>System Statistics
                </h4>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="bg-primary text-white p-3 rounded">
                            <i class="fas fa-gavel fa-2x mb-2"></i>
                            <h3><?php echo count($judges); ?></h3>
                            <p class="mb-0">Total Judges</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="bg-success text-white p-3 rounded">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h3><?php echo count($users); ?></h3>
                            <p class="mb-0">Total Participants</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="bg-warning text-white p-3 rounded">
                            <i class="fas fa-star fa-2x mb-2"></i>
                            <h3>
                                <?php
                                $score_query = "SELECT COUNT(*) as total_scores FROM scores";
                                $score_stmt = $db->prepare($score_query);
                                $score_stmt->execute();
                                $total_scores = $score_stmt->fetch(PDO::FETCH_ASSOC)['total_scores'];
                                echo $total_scores;
                                ?>
                            </h3>
                            <p class="mb-0">Total Scores</p>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="bg-info text-white p-3 rounded">
                            <i class="fas fa-trophy fa-2x mb-2"></i>
                            <h3>
                                <?php
                                $avg_query = "SELECT AVG(points) as avg_score FROM scores";
                                $avg_stmt = $db->prepare($avg_query);
                                $avg_stmt->execute();
                                $avg_score = $avg_stmt->fetch(PDO::FETCH_ASSOC)['avg_score'];
                                echo $avg_score ? number_format($avg_score, 1) : '0';
                                ?>
                            </h3>
                            <p class="mb-0">Average Score</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>