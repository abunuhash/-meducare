<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle new question submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ask_question'])) {
    $question = trim($_POST['question']);
    $category = trim($_POST['category']);
    $is_anonymous = isset($_POST['is_anonymous']) ? 1 : 0;
    
    // Validate
    if (empty($question)) {
        $error = 'Please enter your question';
    } elseif (strlen($question) < 10) {
        $error = 'Question must be at least 10 characters long';
    } elseif (empty($category)) {
        $error = 'Please select a category';
    } else {
        $question = $conn->real_escape_string($question);
        $category = $conn->real_escape_string($category);
        
        $query = "INSERT INTO user_questions (user_id, question, category, is_anonymous) 
                  VALUES ($user_id, '$question', '$category', $is_anonymous)";
        
        // Also insert into qa table for admin answering
        $qa_query = "INSERT INTO qa (user_id, category, question, status) 
                     VALUES ($user_id, '$category', '$question', 'pending')";
        
        if ($conn->query($query) && $conn->query($qa_query)) {
            $message = 'Your question has been submitted successfully';
        } else {
            $error = 'Failed to submit question. Please try again.';
        }
    }
}

// Handle question deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Delete from both tables
    $conn->query("DELETE FROM user_questions WHERE question_id = $id AND user_id = $user_id");
    $conn->query("DELETE FROM qa WHERE user_id = $user_id AND qa_id = $id");
    
    $message = 'Question deleted successfully';
}

// Get user's questions
$questions_query = "SELECT q.*, 
                   (SELECT answer FROM qa WHERE user_id = q.user_id AND question = q.question LIMIT 1) as answer,
                   (SELECT answered_by FROM qa WHERE user_id = q.user_id AND question = q.question LIMIT 1) as answered_by,
                   (SELECT first_name FROM users WHERE user_id = answered_by) as answerer_first,
                   (SELECT last_name FROM users WHERE user_id = answered_by) as answerer_last,
                   (SELECT answered_at FROM qa WHERE user_id = q.user_id AND question = q.question LIMIT 1) as answered_at
                   FROM user_questions q 
                   WHERE q.user_id = $user_id 
                   ORDER BY q.created_at DESC";
$questions_result = $conn->query($questions_query);

// Get categories for dropdown
$categories = ['General Medicine', 'Respiratory', 'Gastrointestinal', 'Cardiovascular', 'Neurological', 'Dermatological', 'Mental Health', 'Women\'s Health', 'Pediatrics', 'Infectious Diseases', 'Eye Care', 'Ear Care'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Questions - Meducare Patient</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/patient-dashboard.css">
    <style>
        .question-form {
            background: #f8fafc;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e2eef5;
            display: none;
        }
        
        .question-form.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }
        
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid #e2eef5;
            border-radius: 10px;
            font-size: 1rem;
            transition: 0.2s;
            font-family: inherit;
        }
        
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #116466;
            box-shadow: 0 0 0 3px rgba(17,100,102,0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            color: #4b6a7c;
        }
        
        .hint {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.3rem;
            display: block;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .submit-btn {
            background: #116466;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 1rem;
        }
        
        .submit-btn:hover {
            background: #0d4f51;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(17,100,102,0.3);
        }
        
        .cancel-btn {
            background: #6c757d;
            color: white;
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .cancel-btn:hover {
            background: #5a6268;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .question-item {
            background: white;
            border: 1px solid #e2eef5;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: 0.3s;
        }
        
        .question-item:hover {
            box-shadow: 0 10px 20px rgba(17,100,102,0.1);
        }
        
        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .question-category {
            background: #e1f2f7;
            color: #116466;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .question-date {
            color: #a0c0d0;
            font-size: 0.9rem;
        }
        
        .question-content {
            margin-bottom: 1rem;
        }
        
        .question-content p {
            color: #0a3142;
            line-height: 1.6;
        }
        
        .answer-content {
            background: #f8fafc;
            padding: 1.2rem;
            border-radius: 8px;
            margin: 1rem 0;
            border-left: 4px solid #116466;
        }
        
        .answer-content p {
            color: #4b6a7c;
            margin-bottom: 0.5rem;
            line-height: 1.6;
        }
        
        .answer-content small {
            color: #78b9c5;
            font-size: 0.85rem;
        }
        
        .pending-answer {
            background: #fff3cd;
            color: #856404;
            padding: 0.8rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 1rem 0;
        }
        
        .question-actions {
            margin-top: 1rem;
            text-align: right;
        }
        
        .delete-link {
            color: #dc3545;
            text-decoration: none;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .delete-link:hover {
            background: #f8d7da;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <?php include '../includes/navbar.php'; ?>

    <div class="container dashboard">
        <!-- Sidebar -->
        <div class="dashboard-sidebar">
            <div class="user-profile">
                <div class="user-avatar">
                    <i class="fas fa-user-circle"></i>
                </div>
                <div class="user-name"><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></div>
                <div class="user-role">Patient</div>
                <div class="user-id">ID: P-<?php echo $_SESSION['user_id']; ?></div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-home"></i> Overview</a>
                <a href="medical_history.php"><i class="fas fa-notes-medical"></i> Medical History</a>
                <a href="prescriptions.php"><i class="fas fa-prescription"></i> Prescriptions</a>
                <a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
                <a href="my_questions.php" class="active"><i class="fas fa-question-circle"></i> My Questions</a>
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>My Questions</h1>
                    <p>Ask health-related questions and get answers from professionals</p>
                </div>
                <button class="btn-solid" onclick="toggleQuestionForm()">
                    <i class="fas fa-plus"></i> Ask New Question
                </button>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <!-- Ask Question Form -->
            <div class="question-form" id="questionForm">
                <h3 style="margin-bottom: 1.5rem; color: #0a3142;">Ask a New Question</h3>
                <form method="POST" action="" onsubmit="return validateQuestionForm()">
                    <div class="form-group">
                        <label for="category">Category *</label>
                        <select name="category" id="category" required>
                            <option value="">Select a category</option>
                            <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="question">Your Question *</label>
                        <textarea name="question" id="question" rows="5" placeholder="Type your health-related question here..." required></textarea>
                        <small class="hint">Minimum 10 characters. Be specific about your symptoms or concerns.</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_anonymous" id="is_anonymous"> Ask anonymously
                        </label>
                        <small class="hint">Your name will not be displayed publicly</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="ask_question" class="submit-btn">
                            <i class="fas fa-paper-plane"></i> Submit Question
                        </button>
                        <button type="button" class="cancel-btn" onclick="toggleQuestionForm()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Questions List -->
            <h3 style="margin: 2rem 0 1rem;">My Questions</h3>
            <div class="questions-list">
                <?php if($questions_result && $questions_result->num_rows > 0): ?>
                    <?php while($q = $questions_result->fetch_assoc()): ?>
                    <div class="question-item">
                        <div class="question-header">
                            <span class="question-category"><?php echo htmlspecialchars($q['category']); ?></span>
                            <span class="question-date"><?php echo date('M d, Y', strtotime($q['created_at'])); ?></span>
                        </div>
                        <div class="question-content">
                            <p><strong>Q:</strong> <?php echo nl2br(htmlspecialchars($q['question'])); ?></p>
                        </div>
                        
                        <?php if(!empty($q['answer'])): ?>
                        <div class="answer-content">
                            <p><strong>A:</strong> <?php echo nl2br(htmlspecialchars($q['answer'])); ?></p>
                            <small>Answered by: Dr. <?php echo htmlspecialchars($q['answerer_first'] . ' ' . $q['answerer_last']); ?> on <?php echo date('M d, Y', strtotime($q['answered_at'])); ?></small>
                        </div>
                        <?php else: ?>
                        <div class="pending-answer">
                            <i class="fas fa-clock"></i> Awaiting answer from our healthcare team. You'll be notified when answered.
                        </div>
                        <?php endif; ?>
                        
                        <?php if(empty($q['answer'])): ?>
                        <div class="question-actions">
                            <a href="?delete=<?php echo $q['question_id']; ?>" class="delete-link" onclick="return confirm('Are you sure you want to delete this question?')">
                                <i class="fas fa-trash"></i> Delete Question
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem;">
                        <i class="fas fa-question-circle" style="font-size: 4rem; color: #cde1ed;"></i>
                        <h3 style="margin: 1rem 0; color: #4b6a7c;">No questions yet</h3>
                        <p>Click the "Ask New Question" button to ask your first health-related question.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function toggleQuestionForm() {
        const form = document.getElementById('questionForm');
        form.classList.toggle('active');
        if (form.classList.contains('active')) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function validateQuestionForm() {
        const category = document.getElementById('category').value;
        const question = document.getElementById('question').value.trim();
        
        if (!category) {
            alert('Please select a category');
            return false;
        }
        
        if (question.length < 10) {
            alert('Question must be at least 10 characters long');
            return false;
        }
        
        return true;
    }

    // Auto-hide alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>