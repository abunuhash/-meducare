<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';
require_once 'includes/auth_check.php';

redirectIfLoggedIn();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $role = $conn->real_escape_string($_POST['role']);
    
    $query = "SELECT * FROM users WHERE email = '$email' AND role = '$role'";
    $result = $conn->query($query);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            if ($user['role'] == 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: patient/dashboard.php');
            }
            exit();
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'User not found';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="auth-container">
        <div class="auth-box">
            <div class="auth-header">
                <h2>Welcome Back</h2>
                <p>Log in to access your health dashboard</p>
            </div>

            <?php if($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="role-selector">
                <div class="role-option active" onclick="selectRole('patient')">
                    <i class="fas fa-user"></i>
                    <span>Patient</span>
                </div>
                <div class="role-option" onclick="selectRole('admin')">
                    <i class="fas fa-user-md"></i>
                    <span>Admin</span>
                </div>
            </div>

            <form class="auth-form" method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>

                <input type="hidden" name="role" id="roleInput" value="patient">

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox"> Remember me
                    </label>
                    <a href="#" class="forgot-password">Forgot Password?</a>
                </div>

                <button type="submit" class="auth-btn">
                    <i class="fas fa-sign-in-alt"></i> Log In
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="register.php">Sign up here</a>
            </div>
        </div>
    </div>

    <script>
    function selectRole(role) {
        document.querySelectorAll('.role-option').forEach(opt => {
            opt.classList.remove('active');
        });
        event.target.closest('.role-option').classList.add('active');
        document.getElementById('roleInput').value = role;
    }
    </script>
    
    <script src="assets/js/main.js"></script>
    <script src="assets/js/validation.js"></script>
</body>
</html>