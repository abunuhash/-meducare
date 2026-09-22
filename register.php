<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/auth_check.php';

redirectIfLoggedIn();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $conn->real_escape_string($_POST['role']);
    
    // Validation
    if ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        // Check if email exists
        $check_query = "SELECT user_id FROM users WHERE email = '$email'";
        $check_result = $conn->query($check_query);
        
        if ($check_result->num_rows > 0) {
            $error = 'Email already registered';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_query = "INSERT INTO users (first_name, last_name, email, phone, password, role) 
                            VALUES ('$first_name', '$last_name', '$email', '$phone', '$hashed_password', '$role')";
            
            if ($conn->query($insert_query)) {
                $success = 'Registration successful! You can now log in.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/register.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="auth-container">
        <div class="auth-box register-box">
            <div class="auth-header">
                <h2>Create Account</h2>
                <p>Join Meducare for personalized health assistance</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="POST" action="" id="registrationForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" placeholder="John" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Doe" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="john.doe@example.com" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" placeholder="01712345678" required>
                    <div class="password-hint">Bangladesh number (11 digits)</div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Create a strong password" required>
                    <div class="password-strength">
                        <div class="strength-bar" id="strengthBar"></div>
                    </div>
                    <div class="password-hint" id="strengthHint"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                </div>

                <div class="form-group">
                    <label for="role">Register as</label>
                    <select id="role" name="role" required>
                        <option value="patient">Patient</option>
                        <option value="admin">Healthcare Admin</option>
                    </select>
                </div>

                <label class="terms">
                    <input type="checkbox" name="terms" required>
                    I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                </label>

                <button type="submit" class="auth-btn">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <div class="auth-footer">
                Already have an account? <a href="login.php">Log in here</a>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/validation.js"></script>
</body>
</html>