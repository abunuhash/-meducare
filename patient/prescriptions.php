<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireLogin();

$user_id = $_SESSION['user_id'];

// Get prescriptions from medical records
$query = "SELECT * FROM user_medical_records 
          WHERE user_id = $user_id 
          AND (record_type = 'Prescription' OR prescription IS NOT NULL)
          ORDER BY record_date DESC";
$prescriptions_result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions - Meducare Patient</title>
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
                <a href="medical_history.php"><i class="fas fa-notes-medical"></i> Medical History</a>
                <a href="prescriptions.php" class="active"><i class="fas fa-prescription"></i> Prescriptions</a>
                <a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a>
                <a href="my_questions.php"><i class="fas fa-question-circle"></i> My Questions</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>My Prescriptions</h1>
                    <p>View your prescription history</p>
                </div>
            </div>

            <?php if($prescriptions_result->num_rows > 0): ?>
                <div class="prescriptions-list">
                    <?php while($prescription = $prescriptions_result->fetch_assoc()): ?>
                    <div class="prescription-card">
                        <div class="prescription-header">
                            <div>
                                <span class="prescription-date"><?php echo date('M d, Y', strtotime($prescription['record_date'])); ?></span>
                                <span class="doctor-name">Dr. <?php echo htmlspecialchars($prescription['doctor_name']); ?></span>
                            </div>
                            <span class="hospital-name"><?php echo htmlspecialchars($prescription['hospital_name']); ?></span>
                        </div>
                        <div class="prescription-body">
                            <div class="diagnosis">
                                <strong>Diagnosis:</strong> <?php echo htmlspecialchars($prescription['diagnosis']); ?>
                            </div>
                            <div class="prescription-details">
                                <strong>Prescription:</strong>
                                <p><?php echo nl2br(htmlspecialchars($prescription['prescription'])); ?></p>
                            </div>
                            <?php if($prescription['notes']): ?>
                            <div class="notes">
                                <strong>Notes:</strong>
                                <p><?php echo nl2br(htmlspecialchars($prescription['notes'])); ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="prescription-footer">
                            <button class="btn-outline" onclick="printPrescription(<?php echo $prescription['record_id']; ?>)">
                                <i class="fas fa-print"></i> Print
                            </button>
                            <button class="btn-outline" onclick="downloadPrescription(<?php echo $prescription['record_id']; ?>)">
                                <i class="fas fa-download"></i> Download
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: 3rem;">
                    <i class="fas fa-prescription" style="font-size: 4rem; color: #cde1ed;"></i>
                    <h3 style="margin: 1rem 0; color: #4b6a7c;">No prescriptions found</h3>
                    <p>Your prescription history will appear here</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function printPrescription(id) {
        // In a real app, this would open a printable version
        alert('Print prescription ' + id);
    }

    function downloadPrescription(id) {
        // In a real app, this would download as PDF
        alert('Download prescription ' + id);
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>