<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireLogin();

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Handle new appointment booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_appointment'])) {
    $doctor_name = trim($_POST['doctor_name']);
    $doctor_specialty = trim($_POST['doctor_specialty']);
    $hospital_name = trim($_POST['hospital_name']);
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = trim($_POST['reason']);
    
    // Validate
    $errors = [];
    
    if (empty($doctor_name)) {
        $errors[] = 'Doctor name is required';
    }
    
    if (empty($appointment_date)) {
        $errors[] = 'Appointment date is required';
    } else {
        $selected_date = new DateTime($appointment_date);
        $today = new DateTime('today');
        if ($selected_date < $today) {
            $errors[] = 'Appointment date cannot be in the past';
        }
    }
    
    if (empty($appointment_time)) {
        $errors[] = 'Appointment time is required';
    }
    
    if (empty($reason)) {
        $errors[] = 'Reason for visit is required';
    } elseif (strlen($reason) < 10) {
        $errors[] = 'Reason must be at least 10 characters';
    }
    
    if (empty($errors)) {
        $doctor_name = $conn->real_escape_string($doctor_name);
        $doctor_specialty = $conn->real_escape_string($doctor_specialty);
        $hospital_name = $conn->real_escape_string($hospital_name);
        $appointment_date = $conn->real_escape_string($appointment_date);
        $appointment_time = $conn->real_escape_string($appointment_time);
        $reason = $conn->real_escape_string($reason);
        
        $query = "INSERT INTO appointments (user_id, doctor_name, doctor_specialty, hospital_name, appointment_date, appointment_time, reason, status) 
                  VALUES ($user_id, '$doctor_name', '$doctor_specialty', '$hospital_name', '$appointment_date', '$appointment_time', '$reason', 'scheduled')";
        
        if ($conn->query($query)) {
            $message = 'Appointment booked successfully! You will receive a confirmation soon.';
        } else {
            $error = 'Failed to book appointment. Please try again.';
        }
    } else {
        $error = implode('<br>', $errors);
    }
}

// Handle appointment cancellation
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);
    
    // Check if appointment belongs to user and is not in the past
    $check_query = "SELECT * FROM appointments WHERE appointment_id = $id AND user_id = $user_id AND appointment_date >= CURDATE()";
    $check_result = $conn->query($check_query);
    
    if ($check_result->num_rows > 0) {
        $query = "UPDATE appointments SET status = 'cancelled' WHERE appointment_id = $id AND user_id = $user_id";
        if ($conn->query($query)) {
            $message = 'Appointment cancelled successfully';
        } else {
            $error = 'Failed to cancel appointment';
        }
    } else {
        $error = 'Cannot cancel this appointment';
    }
}

// Get all appointments
$appointments_query = "SELECT * FROM appointments WHERE user_id = $user_id ORDER BY 
                       CASE 
                           WHEN appointment_date >= CURDATE() AND status = 'scheduled' THEN 1
                           ELSE 2
                       END,
                       appointment_date ASC";
$appointments_result = $conn->query($appointments_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Meducare Patient</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/patient-dashboard.css">
    <style>
        .appointment-form {
            background: #f8fafc;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid #e2eef5;
            display: none;
        }
        
        .appointment-form.active {
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
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
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
        
        .form-group input,
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
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #116466;
            box-shadow: 0 0 0 3px rgba(17,100,102,0.1);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
        
        .cancel-form-btn {
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
        
        .cancel-form-btn:hover {
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
        
        .appointment-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem;
            border: 1px solid #e2eef5;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: 0.3s;
            background: white;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .appointment-card:hover {
            box-shadow: 0 10px 20px rgba(17,100,102,0.1);
            transform: translateY(-2px);
        }
        
        .appointment-card.past {
            opacity: 0.8;
            background: #f8fafc;
        }
        
        .appointment-info h4 {
            color: #0a3142;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .appointment-info p {
            color: #4b6a7c;
            margin-bottom: 0.3rem;
            font-size: 0.95rem;
        }
        
        .appointment-info small {
            color: #78b9c5;
            font-size: 0.85rem;
        }
        
        .appointment-date {
            text-align: right;
            min-width: 200px;
        }
        
        .appointment-date .date {
            font-weight: 600;
            color: #116466;
            font-size: 1.1rem;
        }
        
        .appointment-date .time {
            color: #4b6a7c;
            margin: 0.3rem 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }
        
        .status-badge.scheduled {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-badge.completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .cancel-link {
            color: #dc3545;
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 0.3rem 0.8rem;
            border-radius: 5px;
            transition: 0.2s;
        }
        
        .cancel-link:hover {
            background: #f8d7da;
            text-decoration: none;
        }
        
        .section-title {
            margin: 2rem 0 1rem;
            color: #0a3142;
            font-size: 1.3rem;
        }
        
        .hint {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.3rem;
            display: block;
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
                <a href="appointments.php" class="active"><i class="fas fa-calendar-check"></i> Appointments</a>
                <a href="my_questions.php"><i class="fas fa-question-circle"></i> My Questions</a>
                <a href="#"><i class="fas fa-cog"></i> Settings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>My Appointments</h1>
                    <p>Manage your medical appointments</p>
                </div>
                <button class="btn-solid" onclick="toggleAppointmentForm()">
                    <i class="fas fa-plus"></i> Book Appointment
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

            <!-- Book Appointment Form -->
            <div class="appointment-form" id="appointmentForm">
                <h3 style="margin-bottom: 1.5rem; color: #0a3142;">Book New Appointment</h3>
                <form method="POST" action="" onsubmit="return validateAppointmentForm()">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="doctor_name">Doctor Name *</label>
                            <input type="text" id="doctor_name" name="doctor_name" placeholder="Dr. John Doe" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="doctor_specialty">Specialty</label>
                            <input type="text" id="doctor_specialty" name="doctor_specialty" placeholder="e.g., Cardiologist">
                        </div>
                        
                        <div class="form-group">
                            <label for="hospital_name">Hospital/Clinic</label>
                            <input type="text" id="hospital_name" name="hospital_name" placeholder="e.g., Square Hospital">
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_date">Appointment Date *</label>
                            <input type="date" id="appointment_date" name="appointment_date" min="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_time">Appointment Time *</label>
                            <input type="time" id="appointment_time" name="appointment_time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason for Visit *</label>
                        <textarea id="reason" name="reason" rows="4" placeholder="Please describe your symptoms or reason for visit..." required></textarea>
                        <small class="hint">Minimum 10 characters. Be specific about your symptoms.</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" name="book_appointment" class="submit-btn">
                            <i class="fas fa-calendar-check"></i> Book Appointment
                        </button>
                        <button type="button" class="cancel-form-btn" onclick="toggleAppointmentForm()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Upcoming Appointments -->
            <h3 class="section-title">Upcoming Appointments</h3>
            <?php 
            $has_upcoming = false;
            if ($appointments_result && $appointments_result->num_rows > 0):
                $appointments_result->data_seek(0);
                while($appointment = $appointments_result->fetch_assoc()): 
                    if($appointment['appointment_date'] >= date('Y-m-d') && $appointment['status'] == 'scheduled'):
                        $has_upcoming = true;
            ?>
            <div class="appointment-card">
                <div class="appointment-info">
                    <h4>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h4>
                    <?php if(!empty($appointment['doctor_specialty'])): ?>
                    <p><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($appointment['doctor_specialty']); ?></p>
                    <?php endif; ?>
                    <p><i class="fas fa-notes-medical"></i> <?php echo htmlspecialchars($appointment['reason']); ?></p>
                    <?php if(!empty($appointment['hospital_name'])): ?>
                    <small><i class="fas fa-hospital"></i> <?php echo htmlspecialchars($appointment['hospital_name']); ?></small>
                    <?php endif; ?>
                </div>
                <div class="appointment-date">
                    <div class="date"><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></div>
                    <div class="time"><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></div>
                    <span class="status-badge <?php echo $appointment['status']; ?>">
                        <?php echo ucfirst($appointment['status']); ?>
                    </span>
                    <a href="?cancel=<?php echo $appointment['appointment_id']; ?>" class="cancel-link" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                        <i class="fas fa-times-circle"></i> Cancel
                    </a>
                </div>
            </div>
            <?php 
                    endif; 
                endwhile; 
            endif; 
            
            if(!$has_upcoming): 
            ?>
                <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 12px;">
                    <i class="fas fa-calendar-times" style="font-size: 3rem; color: #cde1ed;"></i>
                    <p style="margin-top: 1rem; color: #4b6a7c;">No upcoming appointments. Book your first appointment today!</p>
                </div>
            <?php endif; ?>

            <!-- Past Appointments -->
            <h3 class="section-title">Past Appointments</h3>
            <?php 
            $has_past = false;
            if ($appointments_result && $appointments_result->num_rows > 0):
                $appointments_result->data_seek(0);
                while($appointment = $appointments_result->fetch_assoc()): 
                    if($appointment['appointment_date'] < date('Y-m-d') || $appointment['status'] != 'scheduled'):
                        $has_past = true;
            ?>
            <div class="appointment-card past">
                <div class="appointment-info">
                    <h4>Dr. <?php echo htmlspecialchars($appointment['doctor_name']); ?></h4>
                    <?php if(!empty($appointment['doctor_specialty'])): ?>
                    <p><i class="fas fa-stethoscope"></i> <?php echo htmlspecialchars($appointment['doctor_specialty']); ?></p>
                    <?php endif; ?>
                    <p><i class="fas fa-notes-medical"></i> <?php echo htmlspecialchars($appointment['reason']); ?></p>
                </div>
                <div class="appointment-date">
                    <div class="date"><i class="far fa-calendar-alt"></i> <?php echo date('M d, Y', strtotime($appointment['appointment_date'])); ?></div>
                    <div class="time"><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?></div>
                    <span class="status-badge <?php echo $appointment['status']; ?>">
                        <?php echo ucfirst($appointment['status']); ?>
                    </span>
                </div>
            </div>
            <?php 
                    endif; 
                endwhile; 
            endif; 
            
            if(!$has_past): 
            ?>
                <div style="text-align: center; padding: 2rem; background: #f8fafc; border-radius: 12px;">
                    <i class="fas fa-history" style="font-size: 3rem; color: #cde1ed;"></i>
                    <p style="margin-top: 1rem; color: #4b6a7c;">No past appointments found.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function toggleAppointmentForm() {
        const form = document.getElementById('appointmentForm');
        form.classList.toggle('active');
        if (form.classList.contains('active')) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function validateAppointmentForm() {
        const doctor = document.getElementById('doctor_name').value.trim();
        const date = document.getElementById('appointment_date').value;
        const time = document.getElementById('appointment_time').value;
        const reason = document.getElementById('reason').value.trim();
        
        if (!doctor) {
            alert('Please enter doctor name');
            return false;
        }
        
        if (!date) {
            alert('Please select appointment date');
            return false;
        }
        
        // Check if date is not in past
        const selectedDate = new Date(date);
        const today = new Date();
        today.setHours(0,0,0,0);
        
        if (selectedDate < today) {
            alert('Appointment date cannot be in the past');
            return false;
        }
        
        if (!time) {
            alert('Please select appointment time');
            return false;
        }
        
        if (reason.length < 10) {
            alert('Please provide a detailed reason (minimum 10 characters)');
            return false;
        }
        
        return true;
    }

    // Set minimum date to today
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.getElementById('appointment_date');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
        }
    });

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