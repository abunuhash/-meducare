<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireAdmin();


$stats = [];

// Total users
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'patient'");
$stats['total_users'] = $result->fetch_assoc()['count'];

// Total medicines
$result = $conn->query("SELECT COUNT(*) as count FROM medicines");
$stats['total_medicines'] = $result->fetch_assoc()['count'];

// Total questions
$result = $conn->query("SELECT COUNT(*) as count FROM user_questions");
$stats['total_questions'] = $result->fetch_assoc()['count'];

// Pending questions
$result = $conn->query("SELECT COUNT(*) as count FROM qa WHERE status = 'pending'");
$stats['pending_qa'] = $result->fetch_assoc()['count'];

// Recent users
$recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5");

// Recent questions
$recent_questions = $conn->query("SELECT q.*, u.first_name, u.last_name 
                                 FROM user_questions q 
                                 JOIN users u ON q.user_id = u.user_id 
                                 ORDER BY q.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container dashboard">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user-md"></i>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
                <div class="user-role">Administrator</div>
                <div class="user-id">ID: A-<?php echo $_SESSION['user_id']; ?></div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Overview</a>
                <a href="manage_medicines.php"><i class="fas fa-pills"></i> Medicines</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Users</a>
                <a href="manage_qa.php"><i class="fas fa-question-circle"></i> Q&A Moderation</a>
                <a href="manage_firstaid.php"><i class="fas fa-heartbeat"></i> First Aid</a>
                <a href="#"><i class="fas fa-chart-line"></i> Analytics</a>
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Admin Dashboard</h1>
                    <p>Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</p>
                </div>
                <div class="date-display">
                    <i class="fas fa-calendar"></i> <?php echo date('F j, Y'); ?>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="manage_medicines.php?action=add" class="action-card">
                    <i class="fas fa-plus-circle"></i>
                    <h4>Add Medicine</h4>
                    <p>Add new medicine to database</p>
                </a>
                <a href="manage_medicines.php" class="action-card">
                    <i class="fas fa-edit"></i>
                    <h4>Update Stock</h4>
                    <p>Manage inventory</p>
                </a>
                <a href="manage_users.php" class="action-card">
                    <i class="fas fa-users"></i>
                    <h4>Manage Users</h4>
                    <p>View & edit user profiles</p>
                </a>
                <a href="manage_qa.php" class="action-card">
                    <i class="fas fa-question"></i>
                    <h4>Pending Q&A</h4>
                    <p><?php echo $stats['pending_qa']; ?> questions to answer</p>
                </a>
            </div>

            <!-- Analytics -->
            <div class="analytics-grid">
                <div class="analytics-card">
                    <div class="analytics-header">
                        <span class="analytics-title">Total Users</span>
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="analytics-value"><?php echo number_format($stats['total_users']); ?></div>
                </div>

                <div class="analytics-card">
                    <div class="analytics-header">
                        <span class="analytics-title">Medicines</span>
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="analytics-value"><?php echo number_format($stats['total_medicines']); ?></div>
                </div>

                <div class="analytics-card">
                    <div class="analytics-header">
                        <span class="analytics-title">Total Questions</span>
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="analytics-value"><?php echo $stats['total_questions']; ?></div>
                </div>

                <div class="analytics-card">
                    <div class="analytics-header">
                        <span class="analytics-title">Pending</span>
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="analytics-value"><?php echo $stats['pending_qa']; ?></div>
                </div>
            </div>

            <!-- Recent Users -->
            <h3 style="margin: 2rem 0 1rem;">Recent Users</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $recent_users->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo ucfirst($user['role']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td class="table-actions">
                            <button class="edit-btn" onclick="editUser(<?php echo $user['user_id']; ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="view-btn" onclick="viewUser(<?php echo $user['user_id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Recent Questions -->
            <h3 style="margin: 2rem 0 1rem;">Recent Questions</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Asked By</th>
                        <th>Category</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($q = $recent_questions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(substr($q['question'], 0, 50)) . '...'; ?></td>
                        <td><?php echo htmlspecialchars($q['first_name'] . ' ' . $q['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($q['category']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($q['created_at'])); ?></td>
                        <td class="table-actions">
                            <button class="edit-btn" onclick="answerQuestion(<?php echo $q['question_id']; ?>)">
                                <i class="fas fa-reply"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function editUser(id) {
        window.location.href = `manage_users.php?action=edit&id=${id}`;
    }

    function viewUser(id) {
        window.location.href = `manage_users.php?action=view&id=${id}`;
    }

    function answerQuestion(id) {
        window.location.href = `manage_qa.php?answer=${id}`;
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>