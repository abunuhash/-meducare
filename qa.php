<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT q.*, u.first_name, u.last_name, 
          (SELECT COUNT(*) FROM qa WHERE status = 'pending') as pending_count
          FROM qa q 
          JOIN users u ON q.user_id = u.user_id 
          WHERE q.status = 'answered'";

if ($category && $category != 'All') {
    $query .= " AND q.category = '" . $conn->real_escape_string($category) . "'";
}

if ($search) {
    $search_term = $conn->real_escape_string($search);
    $query .= " AND (q.question LIKE '%$search_term%' OR q.answer LIKE '%$search_term%')";
}

$query .= " ORDER BY q.created_at DESC";
$result = $conn->query($query);

// Get categories for sidebar
$categories = ['Respiratory', 'General Medicine', 'Gastrointestinal', 'Infectious', 'Eye Care', 'Neurological', 'Cardiovascular'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q&A Forum - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/qa.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container">
        <div class="page-header">
            <h1>Health Q&A Forum</h1>
            <p>Ask questions, get answers from healthcare professionals</p>
        </div>

        <div class="qa-container">
            <div class="qa-sidebar">
                <h3>Categories</h3>
                <ul class="category-list">
                    <li>
                        <a href="qa.php" class="category-item <?php echo $category == 'All' ? 'active' : ''; ?>">
                                                        <i class="fas fa-heartbeat"></i>
                            All Questions
                        </a>
                    </li>
                    <?php foreach($categories as $cat): ?>
                    <li>
                        <a href="qa.php?category=<?php echo urlencode($cat); ?>" 
                           class="category-item <?php echo $category == $cat ? 'active' : ''; ?>">
                            <i class="fas fa-<?php 
                                echo $cat == 'Respiratory' ? 'lungs' : 
                                    ($cat == 'General Medicine' ? 'stethoscope' : 
                                    ($cat == 'Gastrointestinal' ? 'stomach' : 
                                    ($cat == 'Infectious' ? 'virus' : 
                                    ($cat == 'Eye Care' ? 'eye' : 
                                    ($cat == 'Neurological' ? 'brain' : 'heart'))))); 
                            ?>"></i>
                            <?php echo $cat; ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                <button class="ask-question-btn" onclick="openAskQuestionModal()">
                    <i class="fas fa-plus"></i> Ask a Question
                </button>
                <?php else: ?>
                <a href="login.php" class="ask-question-btn" style="text-align: center; text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Login to Ask
                </a>
                <?php endif; ?>
            </div>

            <div class="qa-main">
                <div class="qa-search">
                    <form method="GET" action="qa.php" style="display: flex; gap: 1rem; width: 100%;">
                        <input type="text" name="search" placeholder="Search questions..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>

                <div id="qaList">
                    <?php if($result->num_rows > 0): ?>
                        <?php while($qa = $result->fetch_assoc()): ?>
                        <div class="question-card">
                            <div class="question-header">
                                <span class="question-category"><?php echo htmlspecialchars($qa['category']); ?></span>
                                <span class="question-date"><?php echo date('M d, Y', strtotime($qa['created_at'])); ?></span>
                            </div>
                            <h3 class="question-title"><?php echo htmlspecialchars($qa['question']); ?></h3>
                            <div class="question-meta">
                                <span><i class="fas fa-user"></i> Asked by: <?php echo htmlspecialchars($qa['first_name'] . ' ' . $qa['last_name']); ?></span>
                                <span><i class="fas fa-eye"></i> <?php echo $qa['views']; ?> views</span>
                                <span><i class="fas fa-heart"></i> <?php echo $qa['likes']; ?> likes</span>
                            </div>
                            <div class="answer-preview">
                                <p><?php echo nl2br(htmlspecialchars(substr($qa['answer'], 0, 200))); ?>...</p>
                                <div class="answer-meta">
                                    <span><i class="fas fa-check-circle" style="color: #116466;"></i> Answered by Dr. <?php echo htmlspecialchars($qa['first_name']); ?></span>
                                    <a href="#" class="view-full-answer" onclick="viewFullAnswer(<?php echo $qa['qa_id']; ?>)">View full answer →</a>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #4b6a7c; padding: 2rem;">No questions found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Ask Question Modal -->
    <div class="modal" id="askQuestionModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Ask a Question</h2>
                <button class="close-modal" onclick="closeModal('askQuestionModal')">&times;</button>
            </div>
            <form id="questionForm" onsubmit="submitQuestion(event)">
                <div class="form-group">
                    <label for="questionCategory">Category</label>
                    <select id="questionCategory" required>
                        <option value="">Select a category</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?php echo $cat; ?>"><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="questionText">Your Question</label>
                    <textarea id="questionText" rows="5" placeholder="Type your question here..." required></textarea>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="anonymous"> Ask anonymously
                    </label>
                </div>
                <button type="submit" class="submit-btn">Submit Question</button>
            </form>
        </div>
    </div>

    <!-- View Answer Modal -->
    <div class="modal" id="viewAnswerModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="answerModalTitle">Question</h2>
                <button class="close-modal" onclick="closeModal('viewAnswerModal')">&times;</button>
            </div>
            <div id="answerContent"></div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    function openAskQuestionModal() {
        openModal('askQuestionModal');
    }

    async function submitQuestion(event) {
        event.preventDefault();
        
        const formData = {
            category: document.getElementById('questionCategory').value,
            question: document.getElementById('questionText').value,
            anonymous: document.getElementById('anonymous').checked
        };
        
        try {
            const response = await fetch('api/post_question.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('Question submitted successfully!', 'success');
                closeModal('askQuestionModal');
                document.getElementById('questionForm').reset();
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(result.error || 'Failed to submit question', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        }
    }

    async function viewFullAnswer(qaId) {
        try {
            const response = await fetch(`api/get_qa.php?id=${qaId}`);
            const data = await response.json();
            
            document.getElementById('answerModalTitle').textContent = 'Question & Answer';
            document.getElementById('answerContent').innerHTML = `
                <div class="full-question">
                    <h3>Question:</h3>
                    <p>${data.question}</p>
                    <small>Asked by: ${data.asked_by} | ${data.date}</small>
                </div>
                <div class="full-answer">
                    <h3>Answer:</h3>
                    <p>${data.answer.replace(/\n/g, '<br>')}</p>
                    <small>Answered by: Dr. ${data.answered_by} | ${data.answered_date}</small>
                </div>
            `;
            
            openModal('viewAnswerModal');
        } catch (error) {
            console.error('Error:', error);
            showToast('Failed to load answer', 'error');
        }
    }
    </script>

    <script src="assets/js/main.js"></script>
</body>
</html>