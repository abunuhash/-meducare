<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireLogin();

// Don't allow admin to access patient dashboard
if ($_SESSION['role'] == 'admin') {
    header('Location: ../admin/dashboard.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's medical records
$records_query = "SELECT * FROM user_medical_records WHERE user_id = $user_id ORDER BY record_date DESC LIMIT 5";
$records_result = $conn->query($records_query);

// Get user's appointments
$appointments_query = "SELECT * FROM appointments WHERE user_id = $user_id AND appointment_date >= CURDATE() ORDER BY appointment_date ASC LIMIT 5";
$appointments_result = $conn->query($appointments_query);

// Get user's questions
$questions_query = "SELECT * FROM user_questions WHERE user_id = $user_id ORDER BY created_at DESC LIMIT 5";
$questions_result = $conn->query($questions_query);

// Get answered questions from qa
$qa_query = "SELECT q.*, u.first_name, u.last_name 
             FROM qa q 
             JOIN users u ON q.answered_by = u.user_id 
             WHERE q.user_id = $user_id AND q.status = 'answered'
             ORDER BY q.answered_at DESC LIMIT 5";
$qa_result = $conn->query($qa_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/patient-dashboard.css">
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
                <a href="dashboard.php" class="active"><i class="fas fa-home"></i> Overview</a>
                <a href="medical_history.php"><i class="fas fa-notes-medical"></i> Medical History</a>
                <a href="prescriptions.php"><i class="fas fa-prescription"></i> Prescriptions</a>
                <a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
                <a href="my_questions.php"><i class="fas fa-question-circle"></i> My Questions</a>
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>
                    <p>Here's your health overview for today</p>
                </div>
                <div class="date-display">
                    <i class="fas fa-calendar"></i> <?php echo date('F j, Y'); ?>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-notes-medical"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Medical Records</h3>
                        <div class="stat-value"><?php echo $records_result->num_rows; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Upcoming Appointments</h3>
                        <div class="stat-value"><?php echo $appointments_result->num_rows; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-question"></i>
                    </div>
                    <div class="stat-info">
                        <h3>My Questions</h3>
                        <div class="stat-value"><?php echo $questions_result->num_rows; ?></div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Answered</h3>
                        <div class="stat-value"><?php echo $qa_result->num_rows; ?></div>
                    </div>
                </div>
            </div>

            <!-- Recent Medical History -->
            <h2 style="margin: 2rem 0 1rem;">Recent Medical History</h2>
            <?php if($records_result->num_rows > 0): ?>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Doctor</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($record = $records_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($record['record_date'])); ?></td>
                        <td><?php echo htmlspecialchars($record['description']); ?></td>
                        <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                        <td><span class="status-badge"><?php echo $record['record_type']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p style="color: #4b6a7c; text-align: center; padding: 2rem;">No medical records found.</p>
            <?php endif; ?>

            <!-- Upcoming Appointments -->
            <h2 style="margin: 2rem 0 1rem;">Upcoming Appointments</h2>
            <div class="appointments-list">
                <?php if($appointments_result->num_rows > 0): ?>
                    <?php while($appointment = $appointments_result->fetch_assoc()): ?>
                    <div class="appointment-card">
                        <div class="appointment-info">
                            <h4><?php echo htmlspecialchars($appointment['doctor_name']); ?></h4>
                            <p><?php echo htmlspecialchars($appointment['reason']); ?></p>
                            <small><?php echo htmlspecialchars($appointment['hospital_name']); ?></small>
                        </div>
                        <div class="appointment-date">
                            <div class="date"><?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></div>
                            <div class="time"><?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></div>
                            <span class="status-badge <?php echo $appointment['status']; ?>">
                                <?php echo ucfirst($appointment['status']); ?>
                            </span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                <p style="color: #4b6a7c; text-align: center; padding: 2rem;">No upcoming appointments.</p>
                <?php endif; ?>
            </div>

            <!-- My Recent Questions -->
            <h2 style="margin: 2rem 0 1rem;">My Recent Questions</h2>
            <div class="qa-list">
                <?php if($qa_result->num_rows > 0): ?>
                    <?php while($qa = $qa_result->fetch_assoc()): ?>
                    <div class="qa-item">
                        <div class="qa-question">
                            <i class="fas fa-question-circle"></i>
                            <?php echo htmlspecialchars($qa['question']); ?>
                        </div>
                        <div class="qa-answer">
                            <i class="fas fa-check-circle" style="color: #116466;"></i>
                            <?php echo htmlspecialchars(substr($qa['answer'], 0, 150)) . '...'; ?>
                        </div>
                        <div class="qa-meta">
                            <span>Answered by: Dr. <?php echo htmlspecialchars($qa['first_name'] . ' ' . $qa['last_name']); ?></span>
                            <span><?php echo date('M d, Y', strtotime($qa['answered_at'])); ?></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                <p style="color: #4b6a7c; text-align: center; padding: 2rem;">No answered questions yet.</p>
                <?php endif; ?>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions" style="margin-top: 2rem;">
                <a href="appointments.php?action=new" class="action-btn">
                    <i class="fas fa-calendar-plus"></i>
                    Book Appointment
                </a>
                <a href="my_questions.php?action=new" class="action-btn">
                    <i class="fas fa-question"></i>
                    Ask Question
                </a>
                <a href="medicines.php" class="action-btn">
                    <i class="fas fa-search"></i>
                    Search Medicines
                </a>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>