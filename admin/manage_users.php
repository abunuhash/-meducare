<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireAdmin();

$message = '';
$error = '';

// Handle user deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    if ($id != $_SESSION['user_id']) { // Prevent self-deletion
        $delete_query = "DELETE FROM users WHERE user_id = $id";
        if ($conn->query($delete_query)) {
            $message = 'User deleted successfully';
        } else {
            $error = 'Failed to delete user';
        }
    } else {
        $error = 'Cannot delete your own account';
    }
}

// Handle user role update
if (isset($_POST['update_role'])) {
    $id = intval($_POST['user_id']);
    $role = $conn->real_escape_string($_POST['role']);
    
    if ($id != $_SESSION['user_id']) { // Prevent self-role change
        $query = "UPDATE users SET role = '$role' WHERE user_id = $id";
        if ($conn->query($query)) {
            $message = 'User role updated successfully';
        } else {
            $error = 'Failed to update user role';
        }
    } else {
        $error = 'Cannot change your own role';
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Meducare Admin</title>
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
                <a href="manage_users.php" class="active"><i class="fas fa-users"></i> Users</a>
                <a href="manage_qa.php"><i class="fas fa-question-circle"></i> Q&A Moderation</a>
                <a href="manage_firstaid.php"><i class="fas fa-heartbeat"></i> First Aid</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Manage Users</h1>
                    <p>View and manage user accounts</p>
                </div>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Users List -->
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Update user role?')">
                                <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                <select name="role" onchange="this.form.submit()" <?php echo ($user['user_id'] == $_SESSION['user_id']) ? 'disabled' : ''; ?>>
                                    <option value="patient" <?php echo $user['role'] == 'patient' ? 'selected' : ''; ?>>Patient</option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <input type="hidden" name="update_role" value="1">
                            </form>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td class="table-actions">
                            <button class="view-btn" onclick="viewUser(<?php echo $user['user_id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                            <a href="?delete=<?php echo $user['user_id']; ?>" class="delete-btn" onclick="return confirm('Delete this user?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View User Modal -->
    <div class="modal" id="viewUserModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>User Details</h2>
                <button class="close-modal" onclick="closeModal('viewUserModal')">&times;</button>
            </div>
            <div id="userDetails"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    async function viewUser(id) {
        try {
            const response = await fetch(`../api/get_user.php?id=${id}`);
            const user = await response.json();
            
            document.getElementById('userDetails').innerHTML = `
                <div class="info-section">
                    <p><strong>Name:</strong> ${user.first_name} ${user.last_name}</p>
                    <p><strong>Email:</strong> ${user.email}</p>
                    <p><strong>Phone:</strong> ${user.phone || 'N/A'}</p>
                    <p><strong>Role:</strong> ${user.role}</p>
                    <p><strong>Blood Group:</strong> ${user.blood_group || 'N/A'}</p>
                    <p><strong>Date of Birth:</strong> ${user.date_of_birth || 'N/A'}</p>
                    <p><strong>Address:</strong> ${user.address || 'N/A'}</p>
                    <p><strong>Emergency Contact:</strong> ${user.emergency_contact || 'N/A'}</p>
                    <p><strong>Joined:</strong> ${new Date(user.created_at).toLocaleDateString()}</p>
                </div>
            `;
            
            openModal('viewUserModal');
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load user details');
        }
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>