<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireAdmin();

$message = '';
$error = '';

// Handle answer submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['answer'])) {
    $qa_id = intval($_POST['qa_id']);
    $answer = $conn->real_escape_string($_POST['answer']);
    $answered_by = $_SESSION['user_id'];
    
    $query = "UPDATE qa SET answer = '$answer', answered_by = $answered_by, status = 'answered', answered_at = NOW() WHERE qa_id = $qa_id";
    
    if ($conn->query($query)) {
        $message = 'Answer posted successfully';
    } else {
        $error = 'Failed to post answer';
    }
}

// Handle question deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM qa WHERE qa_id = $id";
    if ($conn->query($delete_query)) {
        $message = 'Question deleted successfully';
    } else {
        $error = 'Failed to delete question';
    }
}

// Get pending questions
$pending_query = "SELECT q.*, u.first_name, u.last_name, u.email 
                  FROM qa q 
                  JOIN users u ON q.user_id = u.user_id 
                  WHERE q.status = 'pending' 
                  ORDER BY q.created_at DESC";
$pending_result = $conn->query($pending_query);

// Get answered questions
$answered_query = "SELECT q.*, u1.first_name as asker_first, u1.last_name as asker_last,
                   u2.first_name as answerer_first, u2.last_name as answerer_last
                   FROM qa q 
                   JOIN users u1 ON q.user_id = u1.user_id
                   LEFT JOIN users u2 ON q.answered_by = u2.user_id
                   WHERE q.status = 'answered' 
                   ORDER BY q.answered_at DESC";
$answered_result = $conn->query($answered_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Q&A - Meducare Admin</title>
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
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-home"></i> Overview</a>
                <a href="manage_medicines.php"><i class="fas fa-pills"></i> Medicines</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Users</a>
                <a href="manage_qa.php" class="active"><i class="fas fa-question-circle"></i> Q&A Moderation</a>
                <a href="manage_firstaid.php"><i class="fas fa-heartbeat"></i> First Aid</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Q&A Moderation</h1>
                    <p>Answer pending questions and manage Q&A content</p>
                </div>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Pending Questions -->
            <h3 style="margin: 2rem 0 1rem;">Pending Questions (<?php echo $pending_result->num_rows; ?>)</h3>
            <div class="qa-moderation">
                <?php if($pending_result->num_rows > 0): ?>
                    <?php while($qa = $pending_result->fetch_assoc()): ?>
                    <div class="qa-item" id="qa-<?php echo $qa['qa_id']; ?>">
                        <div class="qa-question">
                            <strong>Question:</strong> <?php echo htmlspecialchars($qa['question']); ?>
                        </div>
                        <div class="qa-meta">
                            <span><i class="fas fa-user"></i> Asked by: <?php echo htmlspecialchars($qa['first_name'] . ' ' . $qa['last_name']); ?></span>
                            <span><i class="fas fa-tag"></i> Category: <?php echo htmlspecialchars($qa['category']); ?></span>
                            <span><i class="fas fa-clock"></i> <?php echo date('M d, Y h:i A', strtotime($qa['created_at'])); ?></span>
                        </div>
                        <div class="qa-answer">
                            <form method="POST" onsubmit="return validateAnswer(this)">
                                <input type="hidden" name="qa_id" value="<?php echo $qa['qa_id']; ?>">
                                <textarea name="answer" rows="4" placeholder="Type your answer here..." required></textarea>
                                <div class="qa-actions">
                                    <button type="submit" name="answer" class="edit-btn">
                                        <i class="fas fa-paper-plane"></i> Submit Answer
                                    </button>
                                    <a href="?delete=<?php echo $qa['qa_id']; ?>" class="delete-btn" onclick="return confirm('Delete this question?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="color: #4b6a7c; text-align: center; padding: 2rem;">No pending questions.</p>
                <?php endif; ?>
            </div>

            <!-- Answered Questions -->
            <h3 style="margin: 3rem 0 1rem;">Answered Questions</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Question</th>
                        <th>Asked By</th>
                        <th>Answered By</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($answered_result->num_rows > 0): ?>
                        <?php while($qa = $answered_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(substr($qa['question'], 0, 50)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($qa['asker_first'] . ' ' . $qa['asker_last']); ?></td>
                            <td>Dr. <?php echo htmlspecialchars($qa['answerer_first'] . ' ' . $qa['answerer_last']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($qa['answered_at'])); ?></td>
                            <td class="table-actions">
                                <button class="view-btn" onclick="viewAnswer(<?php echo $qa['qa_id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="?delete=<?php echo $qa['qa_id']; ?>" class="delete-btn" onclick="return confirm('Delete this Q&A?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center; color: #4b6a7c;">No answered questions yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Answer Modal -->
    <div class="modal" id="viewAnswerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Question & Answer</h2>
                <button class="close-modal" onclick="closeModal('viewAnswerModal')">&times;</button>
            </div>
            <div id="fullAnswerContent"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function validateAnswer(form) {
        const answer = form.answer.value.trim();
        if (answer.length < 10) {
            alert('Answer must be at least 10 characters long');
            return false;
        }
        return confirm('Submit this answer?');
    }

    async function viewAnswer(id) {
        try {
            const response = await fetch(`../api/get_qa.php?id=${id}`);
            const data = await response.json();
            
            document.getElementById('fullAnswerContent').innerHTML = `
                <div class="full-question" style="margin-bottom: 2rem;">
                    <h3>Question:</h3>
                    <p style="background: #f8fafc; padding: 1rem; border-radius: 8px;">${data.question}</p>
                    <small>Asked by: ${data.asked_by} on ${data.date}</small>
                </div>
                <div class="full-answer">
                    <h3>Answer:</h3>
                    <p style="background: #ecf6fc; padding: 1rem; border-radius: 8px; border-left: 4px solid #116466;">${data.answer.replace(/\n/g, '<br>')}</p>
                    <small>Answered by: Dr. ${data.answered_by} on ${data.answered_date}</small>
                </div>
                <div style="margin-top: 1rem;">
                    <p><strong>Category:</strong> ${data.category}</p>
                    <p><strong>Views:</strong> ${data.views} | <strong>Likes:</strong> ${data.likes}</p>
                </div>
            `;
            
            openModal('viewAnswerModal');
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load answer');
        }
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>