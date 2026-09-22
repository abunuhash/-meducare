<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM medicines WHERE 1=1";

if ($category && $category != 'All') {
    $query .= " AND category = '" . $conn->real_escape_string($category) . "'";
}

if ($search) {
    $search_term = $conn->real_escape_string($search);
    $query .= " AND (name LIKE '%$search_term%' OR generic_name LIKE '%$search_term%' OR manufacturer LIKE '%$search_term%')";
}

$query .= " ORDER BY name ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicines - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/medicines.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container">
        <div class="page-header">
            <h1>Medicine Database</h1>
            <p>Comprehensive information about medications, dosages, and side effects</p>
        </div>

        <div class="search-section">
            <form class="search-box" method="GET" action="medicines.php">
                <input type="text" name="search" placeholder="Search for medicines..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Search</button>
            </form>
            <div class="filter-tabs">
                <a href="medicines.php" class="filter-tab <?php echo !$category ? 'active' : ''; ?>">All Medicines</a>
                <a href="medicines.php?category=Pain Relief" class="filter-tab <?php echo $category == 'Pain Relief' ? 'active' : ''; ?>">Pain Relief</a>
                <a href="medicines.php?category=Antibiotics" class="filter-tab <?php echo $category == 'Antibiotics' ? 'active' : ''; ?>">Antibiotics</a>
                <a href="medicines.php?category=Antihistamines" class="filter-tab <?php echo $category == 'Antihistamines' ? 'active' : ''; ?>">Antihistamines</a>
                <a href="medicines.php?category=Gastrointestinal" class="filter-tab <?php echo $category == 'Gastrointestinal' ? 'active' : ''; ?>">Gastrointestinal</a>
            </div>
        </div>

        <div class="medicine-grid" id="medicineGrid">
            <?php if($result->num_rows > 0): ?>
                <?php while($medicine = $result->fetch_assoc()): ?>
                <div class="medicine-card">
                    <span class="medicine-category"><?php echo htmlspecialchars($medicine['category']); ?></span>
                    <h3 class="medicine-name"><?php echo htmlspecialchars($medicine['name']); ?></h3>
                    <div class="medicine-manufacturer"><?php echo htmlspecialchars($medicine['manufacturer']); ?></div>
                    <div class="medicine-details">
                        <div class="detail-item">
                            <div class="detail-label">Dosage</div>
                            <div class="detail-value"><?php echo htmlspecialchars($medicine['dosage']); ?></div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Form</div>
                            <div class="detail-value"><?php echo htmlspecialchars($medicine['form']); ?></div>
                        </div>
                    </div>
                    <div class="medicine-price">৳<?php echo number_format($medicine['price'], 2); ?></div>
                    <button class="view-details" onclick="showMedicineDetails(<?php echo $medicine['medicine_id']; ?>)">
                        <i class="fas fa-info-circle"></i> View Details
                    </button>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="grid-column: 1/-1; text-align: center; color: #4b6a7c;">No medicines found.</p>
            <?php endif; ?>
        </div>
    </main>

    <!-- Medicine Details Modal -->
    <div class="modal" id="medicineModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Medicine Details</h2>
                <button class="close-modal" onclick="closeModal('medicineModal')">&times;</button>
            </div>
            <div id="modalContent"></div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script>
    async function showMedicineDetails(id) {
        try {
            const response = await fetch(`api/get_medicine.php?id=${id}`);
            const medicine = await response.json();
            
            document.getElementById('modalTitle').textContent = medicine.name;
            document.getElementById('modalContent').innerHTML = `
                <div class="info-section">
                    <h3>Generic Name</h3>
                    <p>${medicine.generic_name || 'N/A'}</p>
                </div>
                <div class="info-section">
                    <h3>Usage</h3>
                    <p>${medicine.description || 'N/A'}</p>
                </div>
                <div class="info-section">
                    <h3>Dosage</h3>
                    <p>${medicine.dosage || 'N/A'}</p>
                </div>
                <div class="info-section">
                    <h3>Usage Instructions</h3>
                    <p>${medicine.usage_instructions || 'N/A'}</p>
                </div>
                <div class="info-section">
                    <h3>Side Effects</h3>
                    <p class="side-effects">${medicine.side_effects || 'N/A'}</p>
                </div>
                <div class="info-section">
                    <h3>Warnings</h3>
                    <p style="color: #dc3545;">⚠️ ${medicine.warnings || 'N/A'}</p>
                </div>
                <div class="info-section">
                    <h3>Manufacturer</h3>
                    <p>${medicine.manufacturer || 'N/A'} (${medicine.country_of_origin || 'Bangladesh'})</p>
                </div>
                <div class="info-section">
                    <h3>Price</h3>
                    <p style="font-size: 1.5rem; color: #116466; font-weight: 700;">৳${parseFloat(medicine.price).toFixed(2)}</p>
                </div>
                <div class="info-section">
                    <h3>Requires Prescription</h3>
                    <p>${medicine.requires_prescription ? 'Yes' : 'No'}</p>
                </div>
            `;
            
            openModal('medicineModal');
        } catch (error) {
            console.error('Error fetching medicine details:', error);
            alert('Failed to load medicine details');
        }
    }
    </script>
    
    <script src="assets/js/main.js"></script>
</body>
</html>