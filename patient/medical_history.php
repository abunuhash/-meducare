<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Get all medical records
$records_query = "SELECT * FROM user_medical_records WHERE user_id = $user_id ORDER BY record_date DESC";
$records_result = $conn->query($records_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medical History - Meducare Patient</title>
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
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php"><i class="fas fa-home"></i> Overview</a>
                <a href="medical_history.php" class="active"><i class="fas fa-notes-medical"></i> Medical History</a>
                <a href="prescriptions.php"><i class="fas fa-prescription"></i> Prescriptions</a>
                <a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
                <a href="my_questions.php"><i class="fas fa-question-circle"></i> My Questions</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Medical History</h1>
                    <p>Your complete medical records</p>
                </div>
            </div>

            <?php if($records_result->num_rows > 0): ?>
                <table class="history-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Doctor</th>
                            <th>Hospital</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($record = $records_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($record['record_date'])); ?></td>
                            <td><span class="status-badge"><?php echo $record['record_type']; ?></span></td>
                            <td><?php echo htmlspecialchars($record['description']); ?></td>
                            <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['hospital_name']); ?></td>
                            <td>
                                <button class="view-btn" onclick="viewRecord(<?php echo $record['record_id']; ?>)">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-notes-medical" style="font-size: 4rem; color: #cde1ed;"></i>
                    <h3 style="margin: 1rem 0; color: #4b6a7c;">No medical records found</h3>
                    <p>Your medical history will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- View Record Modal -->
    <div class="modal" id="viewRecordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Medical Record Details</h2>
                <button class="close-modal" onclick="closeModal('viewRecordModal')">&times;</button>
            </div>
            <div id="recordDetails"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function viewRecord(id) {
        // In a real app, fetch record details via AJAX
        alert('View record ' + id + ' - Details would be shown here');
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>