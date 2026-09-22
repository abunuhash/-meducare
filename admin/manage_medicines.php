<?php
require_once '../includes/config.php';
require_once '../includes/db_connect.php';
require_once '../includes/auth_check.php';

requireAdmin();

$message = '';
$error = '';

// Handle medicine deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $delete_query = "DELETE FROM medicines WHERE medicine_id = $id";
    if ($conn->query($delete_query)) {
        $message = 'Medicine deleted successfully';
    } else {
        $error = 'Failed to delete medicine';
    }
}

// Handle medicine addition/update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $generic_name = $conn->real_escape_string($_POST['generic_name']);
    $category = $conn->real_escape_string($_POST['category']);
    $manufacturer = $conn->real_escape_string($_POST['manufacturer']);
    $dosage = $conn->real_escape_string($_POST['dosage']);
    $form = $conn->real_escape_string($_POST['form']);
    $strength = $conn->real_escape_string($_POST['strength']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $requires_prescription = isset($_POST['requires_prescription']) ? 1 : 0;
    $description = $conn->real_escape_string($_POST['description']);
    $usage_instructions = $conn->real_escape_string($_POST['usage_instructions']);
    $side_effects = $conn->real_escape_string($_POST['side_effects']);
    $warnings = $conn->real_escape_string($_POST['warnings']);

    if (isset($_POST['medicine_id']) && !empty($_POST['medicine_id'])) {
        // Update
        $id = intval($_POST['medicine_id']);
        $query = "UPDATE medicines SET 
                  name = '$name',
                  generic_name = '$generic_name',
                  category = '$category',
                  manufacturer = '$manufacturer',
                  dosage = '$dosage',
                  form = '$form',
                  strength = '$strength',
                  price = $price,
                  stock = $stock,
                  requires_prescription = $requires_prescription,
                  description = '$description',
                  usage_instructions = '$usage_instructions',
                  side_effects = '$side_effects',
                  warnings = '$warnings'
                  WHERE medicine_id = $id";
        
        if ($conn->query($query)) {
            $message = 'Medicine updated successfully';
        } else {
            $error = 'Failed to update medicine';
        }
    } else {
        // Insert
        $query = "INSERT INTO medicines (name, generic_name, category, manufacturer, dosage, form, strength, price, stock, requires_prescription, description, usage_instructions, side_effects, warnings, created_by) 
                  VALUES ('$name', '$generic_name', '$category', '$manufacturer', '$dosage', '$form', '$strength', $price, $stock, $requires_prescription, '$description', '$usage_instructions', '$side_effects', '$warnings', {$_SESSION['user_id']})";
        
        if ($conn->query($query)) {
            $message = 'Medicine added successfully';
        } else {
            $error = 'Failed to add medicine';
        }
    }
}

// Get medicine for editing
$edit_medicine = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM medicines WHERE medicine_id = $id");
    $edit_medicine = $result->fetch_assoc();
}

// Get all medicines
$medicines = $conn->query("SELECT * FROM medicines ORDER BY name ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicines - Meducare Admin</title>
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
                <a href="manage_medicines.php" class="active"><i class="fas fa-pills"></i> Medicines</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Users</a>
                <a href="manage_qa.php"><i class="fas fa-question-circle"></i> Q&A Moderation</a>
                <a href="manage_firstaid.php"><i class="fas fa-heartbeat"></i> First Aid</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="dashboard-main">
            <div class="dashboard-header">
                <div class="header-title">
                    <h1>Manage Medicines</h1>
                    <p>Add, edit, or remove medicines from the database</p>
                </div>
                <button class="btn-solid" onclick="showAddForm()">
                    <i class="fas fa-plus"></i> Add New Medicine
                </button>
            </div>

            <?php if($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>

            <!-- Medicine Form -->
            <div class="medicine-form" id="medicineForm" style="<?php echo $edit_medicine ? 'display: block;' : 'display: none;'; ?>">
                <h3><?php echo $edit_medicine ? 'Edit Medicine' : 'Add New Medicine'; ?></h3>
                <form method="POST" action="">
                    <?php if($edit_medicine): ?>
                        <input type="hidden" name="medicine_id" value="<?php echo $edit_medicine['medicine_id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Medicine Name *</label>
                            <input type="text" name="name" required value="<?php echo $edit_medicine ? htmlspecialchars($edit_medicine['name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Generic Name</label>
                            <input type="text" name="generic_name" value="<?php echo $edit_medicine ? htmlspecialchars($edit_medicine['generic_name']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" required>
                                <option value="">Select Category</option>
                                <option value="Pain Relief" <?php echo ($edit_medicine && $edit_medicine['category'] == 'Pain Relief') ? 'selected' : ''; ?>>Pain Relief</option>
                                <option value="Antibiotics" <?php echo ($edit_medicine && $edit_medicine['category'] == 'Antibiotics') ? 'selected' : ''; ?>>Antibiotics</option>
                                <option value="Antihistamines" <?php echo ($edit_medicine && $edit_medicine['category'] == 'Antihistamines') ? 'selected' : ''; ?>>Antihistamines</option>
                                <option value="Gastrointestinal" <?php echo ($edit_medicine && $edit_medicine['category'] == 'Gastrointestinal') ? 'selected' : ''; ?>>Gastrointestinal</option>
                                <option value="Cardiovascular" <?php echo ($edit_medicine && $edit_medicine['category'] == 'Cardiovascular') ? 'selected' : ''; ?>>Cardiovascular</option>
                                <option value="Diabetes" <?php echo ($edit_medicine && $edit_medicine['category'] == 'Diabetes') ? 'selected' : ''; ?>>Diabetes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Manufacturer</label>
                            <input type="text" name="manufacturer" value="<?php echo $edit_medicine ? htmlspecialchars($edit_medicine['manufacturer']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Dosage</label>
                            <input type="text" name="dosage" value="<?php echo $edit_medicine ? htmlspecialchars($edit_medicine['dosage']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Form</label>
                            <select name="form">
                                <option value="Tablet" <?php echo ($edit_medicine && $edit_medicine['form'] == 'Tablet') ? 'selected' : ''; ?>>Tablet</option>
                                <option value="Capsule" <?php echo ($edit_medicine && $edit_medicine['form'] == 'Capsule') ? 'selected' : ''; ?>>Capsule</option>
                                <option value="Syrup" <?php echo ($edit_medicine && $edit_medicine['form'] == 'Syrup') ? 'selected' : ''; ?>>Syrup</option>
                                <option value="Cream" <?php echo ($edit_medicine && $edit_medicine['form'] == 'Cream') ? 'selected' : ''; ?>>Cream</option>
                                <option value="Injection" <?php echo ($edit_medicine && $edit_medicine['form'] == 'Injection') ? 'selected' : ''; ?>>Injection</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Strength</label>
                            <input type="text" name="strength" value="<?php echo $edit_medicine ? htmlspecialchars($edit_medicine['strength']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Price (BDT) *</label>
                            <input type="number" step="0.01" name="price" required value="<?php echo $edit_medicine ? $edit_medicine['price'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Stock *</label>
                            <input type="number" name="stock" required value="<?php echo $edit_medicine ? $edit_medicine['stock'] : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="requires_prescription" <?php echo ($edit_medicine && $edit_medicine['requires_prescription']) ? 'checked' : ''; ?>>
                                Requires Prescription
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"><?php echo $edit_medicine ? htmlspecialchars($edit_medicine['description']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Usage Instructions</label>
                        <textarea name="usage_instructions" rows="3"><?php echo $edit_medicine ? htmlspecialchars($edit_medicine['usage_instructions']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Side Effects</label>
                        <textarea name="side_effects" rows="3"><?php echo $edit_medicine ? htmlspecialchars($edit_medicine['side_effects']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Warnings</label>
                        <textarea name="warnings" rows="3"><?php echo $edit_medicine ? htmlspecialchars($edit_medicine['warnings']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-save"></i> <?php echo $edit_medicine ? 'Update Medicine' : 'Save Medicine'; ?>
                        </button>
                        <?php if($edit_medicine): ?>
                            <button type="button" class="cancel-btn" onclick="cancelEdit()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Medicines List -->
            <h3 style="margin: 2rem 0 1rem;">Medicine List</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Manufacturer</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Prescription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($med = $medicines->fetch_assoc()): ?>
                    <tr>
                        <td>#<?php echo $med['medicine_id']; ?></td>
                        <td><?php echo htmlspecialchars($med['name']); ?></td>
                        <td><?php echo htmlspecialchars($med['category']); ?></td>
                        <td><?php echo htmlspecialchars($med['manufacturer']); ?></td>
                        <td>৳<?php echo number_format($med['price'], 2); ?></td>
                        <td><?php echo $med['stock']; ?></td>
                        <td><?php echo $med['requires_prescription'] ? 'Yes' : 'No'; ?></td>
                        <td class="table-actions">
                            <a href="?edit=<?php echo $med['medicine_id']; ?>" class="edit-btn">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="?delete=<?php echo $med['medicine_id']; ?>" class="delete-btn" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </a>
                            <button class="view-btn" onclick="viewMedicine(<?php echo $med['medicine_id']; ?>)">
                                <i class="fas fa-eye"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- View Medicine Modal -->
    <div class="modal" id="viewMedicineModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Medicine Details</h2>
                <button class="close-modal" onclick="closeModal('viewMedicineModal')">&times;</button>
            </div>
            <div id="medicineDetails"></div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
    function showAddForm() {
        document.getElementById('medicineForm').style.display = 'block';
        window.scrollTo(0, document.getElementById('medicineForm').offsetTop);
    }

    function cancelEdit() {
        window.location.href = 'manage_medicines.php';
    }

    async function viewMedicine(id) {
        try {
            const response = await fetch(`../api/get_medicine.php?id=${id}`);
            const medicine = await response.json();
            
            document.getElementById('medicineDetails').innerHTML = `
                <div class="info-section">
                    <h3>${medicine.name}</h3>
                    <p><strong>Generic:</strong> ${medicine.generic_name || 'N/A'}</p>
                    <p><strong>Category:</strong> ${medicine.category}</p>
                    <p><strong>Manufacturer:</strong> ${medicine.manufacturer}</p>
                    <p><strong>Dosage:</strong> ${medicine.dosage}</p>
                    <p><strong>Form:</strong> ${medicine.form}</p>
                    <p><strong>Strength:</strong> ${medicine.strength}</p>
                    <p><strong>Price:</strong> ৳${parseFloat(medicine.price).toFixed(2)}</p>
                    <p><strong>Stock:</strong> ${medicine.stock}</p>
                    <p><strong>Prescription Required:</strong> ${medicine.requires_prescription ? 'Yes' : 'No'}</p>
                    <p><strong>Description:</strong> ${medicine.description}</p>
                    <p><strong>Usage:</strong> ${medicine.usage_instructions}</p>
                    <p><strong>Side Effects:</strong> ${medicine.side_effects}</p>
                    <p><strong>Warnings:</strong> ${medicine.warnings}</p>
                </div>
            `;
            
            openModal('viewMedicineModal');
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to load medicine details');
        }
    }
    </script>
    
    <script src="../assets/js/main.js"></script>
</body>
</html>