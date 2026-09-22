<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireAdmin();

$message = '';
$error = '';

// Handle first aid deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM first_aid WHERE aid_id = $id";
    if ($conn->query($delete_query)) {
        $message = 'First aid entry deleted successfully';
    } else {
        $error = 'Failed to delete entry';
    }
}

// Handle first aid addition/update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $condition_name = $conn->real_escape_string($_POST['condition_name']);
    $symptoms = $conn->real_escape_string($_POST['symptoms']);
    $causes = $conn->real_escape_string($_POST['causes']);
    $first_aid_steps = $conn->real_escape_string($_POST['first_aid_steps']);
    $do_not = $conn->real_escape_string($_POST['do_not']);
    $warning_signs = $conn->real_escape_string($_POST['warning_signs']);
    $when_to_see_doctor = $conn->real_escape_string($_POST['when_to_see_doctor']);
    $prevention_tips = $conn->real_escape_string($_POST['prevention_tips']);
    $category = $conn->real_escape_string($_POST['category']);
    $icon_class = $conn->real_escape_string($_POST['icon_class']);
    $severity = $conn->real_escape_string($_POST['severity']);

    if (isset($_POST['aid_id']) && !empty($_POST['aid_id'])) {
        // Update
        $id = intval($_POST['aid_id']);
        $query = "UPDATE first_aid SET 
                  condition_name = '$condition_name',
                  symptoms = '$symptoms',
                  causes = '$causes',
                  first_aid_steps = '$first_aid_steps',
                  do_not = '$do_not',
                  warning_signs = '$warning_signs',
                  when_to_see_doctor = '$when_to_see_doctor',
                  prevention_tips = '$prevention_tips',
                  category = '$category',
                  icon_class = '$icon_class',
                  severity = '$severity'
                  WHERE aid_id = $id";
        
        if ($conn->query($query)) {
            $message = 'First aid entry updated successfully';
        } else {
            $error = 'Failed to update entry';
        }
    } else {
        // Insert
        $query = "INSERT INTO first_aid (condition_name, symptoms, causes, first_aid_steps, do_not, warning_signs, when_to_see_doctor, prevention_tips, category, icon_class, severity) 
                  VALUES ('$condition_name', '$symptoms', '$causes', '$first_aid_steps', '$do_not', '$warning_signs', '$when_to_see_doctor', '$prevention_tips', '$category', '$icon_class', '$severity')";
        
        if ($conn->query($query)) {
            $message = 'First aid entry added successfully';
        } else {
            $error = 'Failed to add entry';
        }
    }
}

// Get entry for editing
$edit_entry = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM first_aid WHERE aid_id = $id");
    $edit_entry = $result->fetch_assoc();
}

// Get all first aid entries
$entries = $conn->query("SELECT * FROM first_aid ORDER BY condition_name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage First Aid - Meducare Admin</title>
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
                <a href="manage_users.php"><i class="fas fa-users"></i> Users</a>
                <a href="manage_qa.php"><i class="fas fa-question-circle"></i> Q&A Moderation</a>
                <a href="manage_firstaid.php" class="active"><i class="fas fa-heartbeat"></i> First Aid</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Manage First Aid</h1>
                    <p>Add and update first aid information</p>
                </div>
                <button class="btn-solid" onclick="showAddForm()">
                    <i class="fas fa-plus"></i> Add New Entry
                </button>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- First Aid Form -->
            <div class="medicine-form" id="firstAidForm" style="<?php echo $edit_entry ? 'display: block;' : 'display: none;'; ?>">
                <h3><?php echo $edit_entry ? 'Edit First Aid Entry' : 'Add New First Aid Entry'; ?></h3>
                <form method="POST" action="">
                    <?php if($edit_entry): ?>
                        <input type="hidden" name="aid_id" value="<?php echo $edit_entry['aid_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Condition Name *</label>
                            <input type="text" name="condition_name" required value="<?php echo $edit_entry ? htmlspecialchars($edit_entry['condition_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <input type="text" name="category" value="<?php echo $edit_entry ? htmlspecialchars($edit_entry['category']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Icon Class</label>
                            <input type="text" name="icon_class" value="<?php echo $edit_entry ? htmlspecialchars($edit_entry['icon_class']) : ''; ?>" placeholder="e.g., temperature-high">
                        </div>
                        <div class="form-group">
                            <label>Severity</label>
                            <select name="severity">
                                <option value="Mild" <?php echo ($edit_entry && $edit_entry['severity'] == 'Mild') ? 'selected' : ''; ?>>Mild</option>
                                <option value="Moderate" <?php echo ($edit_entry && $edit_entry['severity'] == 'Moderate') ? 'selected' : ''; ?>>Moderate</option>
                                <option value="Severe" <?php echo ($edit_entry && $edit_entry['severity'] == 'Severe') ? 'selected' : ''; ?>>Severe</option>
                                <option value="Emergency" <?php echo ($edit_entry && $edit_entry['severity'] == 'Emergency') ? 'selected' : ''; ?>>Emergency</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Symptoms (comma separated)</label>
                        <textarea name="symptoms" rows="3"><?php echo $edit_entry ? htmlspecialchars($edit_entry['symptoms']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Causes</label>
                        <textarea name="causes" rows="3"><?php echo $edit_entry ? htmlspecialchars($edit_entry['causes']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>First Aid Steps (one per line)</label>
                        <textarea name="first_aid_steps" rows="5"><?php echo $edit_entry ? htmlspecialchars($edit_entry['first_aid_steps']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>What NOT to Do (one per line)</label>
                        <textarea name="do_not" rows="3"><?php echo $edit_entry ? htmlspecialchars($edit_entry['do_not']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Warning Signs</label>
                        <textarea name="warning_signs" rows="3"><?php echo $edit_entry ? htmlspecialchars($edit_entry['warning_signs']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>When to See Doctor</label>
                        <textarea name="when_to_see_doctor" rows="3"><?php echo $edit_entry ? htmlspecialchars($edit_entry['when_to_see_doctor']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Prevention Tips</label>
                        <textarea name="prevention_tips" rows="3"><?php echo $edit_entry ? htmlspecialchars($edit_entry['prevention_tips']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i> <?php echo $edit_entry ? 'Update Entry' : 'Save Entry'; ?>
                        </button>
                        <?php if($edit_entry): ?>
                            <button type="button" class="cancel-btn" onclick="cancelEdit()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- First Aid List -->
            <h3 style="margin: 2rem 0 1rem;">First Aid Entries</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Condition</th>
                        <th>Category</th>
                        <th>Severity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($entry = $entries->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $entry['aid_id']; ?></td>
                        <td><?php echo htmlspecialchars($entry['condition_name']); ?></td>
                        <td><?php echo htmlspecialchars($entry['category']); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($entry['severity']); ?>">
                                <?php echo $entry['severity']; ?>
                            </span>
                        </td>
                        <td class="table-actions">
                            <a href="?edit=<?php echo $entry['aid_id']; ?>" class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $entry['aid_id']; ?>" class="delete-btn" onclick="return confirm('Delete this entry?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <button class="view-btn" onclick="viewFirstAid(<?php echo $entry['aid_id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View First Aid Modal -->
    <div class="modal" id="viewFirstAidModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>First Aid Details</h2>
                <button class="close-modal" onclick="closeModal('viewFirstAidModal')">&times;</button>
            </div>
            <div id="firstAidDetails"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function showAddForm() {
        document.getElementById('firstAidForm').style.display = 'block';
        window.scrollTo(0, document.getElementById('firstAidForm').offsetTop);
    }

    function cancelEdit() {
        window.location.href = 'manage_firstaid.php';
    }

    function viewFirstAid(id) {
        // You can implement AJAX fetch here to get full details
        // For now, redirect to first-aid page with anchor
        window.open(`../first-aid.php#${id}`, '_blank');
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>