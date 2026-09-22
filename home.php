<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

// Fetch medical history
$history_query = "SELECT * FROM medical_history ORDER BY year_discovered DESC LIMIT 6";
$history_result = $conn->query($history_query);

// Fetch Q&A
$qa_query = "SELECT q.*, u.first_name, u.last_name 
             FROM qa q 
             JOIN users u ON q.user_id = u.user_id 
             WHERE q.status = 'answered' 
             ORDER BY q.created_at DESC LIMIT 6";
$qa_result = $conn->query($qa_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meducare - Smart Health Assistance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/home.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container">
        <!-- Hero Section -->
        <div class="hero">
            <div class="hero-content">
                <h1>Your Smart Health Companion</h1>
                <p>Medicine information, first aid guidance, and interactive education — all in one place.</p>
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">1,200+</span>
                        <span class="stat-label">Medicines</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">50K+</span>
                        <span class="stat-label">Happy Users</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Support</span>
                    </div>
                </div>
                <div style="margin-top: 2rem;">
                    <a href="medicines.php" class="btn-solid" style="display: inline-block; margin-right: 1rem;">
                        <i class="fas fa-search"></i> Browse Medicines
                    </a>
                    <a href="first-aid.php" class="btn-outline" style="display: inline-block;">
                        <i class="fas fa-heartbeat"></i> First Aid Guide
                    </a>
                </div>
            </div>
            <div class="hero-image">
                <i class="fas fa-heartbeat" style="font-size: 8rem; color: #116466; opacity: 0.2;"></i>
            </div>
        </div>

        <!-- Medical History Section -->
        <div class="medical-history">
            <h2 class="section-title">📚 Medical History: Great Discoveries</h2>
            <p class="section-sub">Fascinating stories behind life-changing medicine inventions</p>
            
            <div class="timeline">
                <?php while($history = $history_result->fetch_assoc()): ?>
                <div class="timeline-card">
                    <div class="timeline-date"><?php echo htmlspecialchars($history['year_discovered']); ?></div>
                    <div class="timeline-title"><?php echo htmlspecialchars($history['title']); ?></div>
                    <div class="timeline-desc"><?php echo htmlspecialchars($history['description']); ?></div>
                    <span class="timeline-tag"><?php echo htmlspecialchars($history['category']); ?></span>
                    <?php if(!empty($history['fun_fact'])): ?>
                    <div style="margin-top: 1rem; color: #116466; font-size: 0.9rem;">
                        <i class="fas fa-flask"></i> <?php echo htmlspecialchars($history['fun_fact']); ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Quick Q&A Section -->
        <div class="quick-qa">
            <h2 class="section-title">Quick Questions & Answers</h2>
            <p class="section-sub">Common queries from our community</p>
            
            <div class="qa-grid">
                <?php while($qa = $qa_result->fetch_assoc()): ?>
                <div class="qa-item">
                    <div class="qa-question">
                        <i class="fas fa-question-circle"></i>
                        <?php echo htmlspecialchars($qa['question']); ?>
                    </div>
                    <div class="qa-answer">
                        <?php echo htmlspecialchars(substr($qa['answer'], 0, 100)) . '...'; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div style="text-align: center; margin-top: 2rem;">
                <a href="qa.php" class="btn-solid" style="display: inline-block;">
                    <i class="fas fa-question"></i> Ask Your Question
                </a>
            </div>
        </div>

        <!-- Success Stories -->
        <div class="success-stories">
            <h2 class="section-title">Success Stories</h2>
            <p class="section-sub">Real stories from real users</p>
            
            <div class="stories-grid">
                <div class="story-card">
                    <div class="story-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="story-name">Mahmud Nashid</div>
                    <div class="story-role">Patient</div>
                    <div class="story-text">
                        "Meducare helped me understand my medication schedule better. The first aid tips saved me during an emergency!"
                    </div>
                    <div class="story-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="story-card">
                    <div class="story-avatar">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <div class="story-name">Dr. Mehrab Hossain Mridul</div>
                    <div class="story-role">Healthcare Provider</div>
                    <div class="story-text">
                        "As a doctor, I recommend Meducare to my patients. It bridges the gap between prescriptions and understanding."
                    </div>
                    <div class="story-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
                
                <div class="story-card">
                    <div class="story-avatar">
                        <i class="fas fa-user-nurse"></i>
                    </div>
                    <div class="story-name">Emily Manon</div>
                    <div class="story-role">Nurse</div>
                    <div class="story-text">
                        "The interactive body and skeleton views are excellent teaching tools. My students love them!"
                    </div>
                    <div class="story-rating">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <div class="reviews">
            <h2 class="section-title">User Reviews</h2>
            <p class="section-sub">What our community says about us</p>
            
            <div class="review-card">
                <div class="review-header">
                    <i class="fas fa-user-circle" style="font-size: 2.5rem; color: #116466;"></i>
                    <div class="reviewer-info">
                        <h4>Mahmud Un Nabi</h4>
                        <p>Verified User</p>
                    </div>
                </div>
                <div class="review-text">
                    "The medicine database is comprehensive and accurate. I love how I can check side effects and interactions before taking anything new."
                </div>
                <div class="review-date">Posted on <?php echo date('F j, Y'); ?></div>
            </div>
        </div>

        <!-- Quick Links -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin: 3rem 0; text-align: center;">
            <a href="medicines.php" style="background: white; padding: 1.5rem; border-radius: 15px; text-decoration: none; color: #116466; border: 1px solid #e2eef5;">
                <i class="fas fa-pills" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <span>Medicines</span>
            </a>
            <a href="qa.php" style="background: white; padding: 1.5rem; border-radius: 15px; text-decoration: none; color: #116466; border: 1px solid #e2eef5;">
                <i class="fas fa-question-circle" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <span>Q&A Forum</span>
            </a>
            <a href="first-aid.php" style="background: white; padding: 1.5rem; border-radius: 15px; text-decoration: none; color: #116466; border: 1px solid #e2eef5;">
                <i class="fas fa-heartbeat" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <span>First Aid</span>
            </a>
            <a href="human-body.php" style="background: white; padding: 1.5rem; border-radius: 15px; text-decoration: none; color: #116466; border: 1px solid #e2eef5;">
                <i class="fas fa-user-md" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                <span>Human Body</span>
            </a>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <script src="assets/js/main.js"></script>
</body>
</html>