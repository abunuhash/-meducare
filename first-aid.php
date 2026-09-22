<?php
require_once 'includes/config.php';
require_once 'includes/db_connect.php';

$query = "SELECT * FROM first_aid ORDER BY condition_name ASC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>First Aid Guide - Meducare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/first-aid.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <main class="container">
        <div class="first-aid-header">
            <h1>First Aid Guide</h1>
            <p>Quick and reliable first aid information for common conditions</p>
        </div>

        <div class="emergency-banner">
            <div>
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Emergency? Call for help immediately</strong>
            </div>
            <div class="emergency-number">
                <i class="fas fa-phone-alt"></i> 999
            </div>
        </div>

        <div class="symptom-checker">
            <h2><i class="fas fa-search"></i> Symptom Checker</h2>
            <p>Enter your symptoms to get first aid advice</p>
            <div class="symptom-input">
                <input type="text" id="symptomInput" placeholder="e.g., fever, headache, cough...">
                <button onclick="checkSymptoms()"><i class="fas fa-arrow-right"></i> Check</button>
            </div>
            <div class="common-symptoms">
                <span class="symptom-tag">Fever</span>
                <span class="symptom-tag">Headache</span>
                <span class="symptom-tag">Cough</span>
                <span class="symptom-tag">Stomach ache</span>
                <span class="symptom-tag">Diarrhea</span>
                <span class="symptom-tag">Cold</span>
            </div>
            <div id="firstAidAdvice" class="advice-container"></div>
        </div>

        <h2 style="color: #0a3142; margin-bottom: 1.5rem;">Common Conditions & First Aid</h2>
        <div class="disease-grid">
            <?php while($aid = $result->fetch_assoc()): ?>
            <div class="disease-card">
                <div class="disease-header">
                    <div class="disease-icon">
                        <i class="fas fa-<?php echo $aid['icon_class'] ?: 'heartbeat'; ?>"></i>
                    </div>
                    <h3 class="disease-name"><?php echo htmlspecialchars($aid['condition_name']); ?></h3>
                </div>
                <div class="disease-info">
                    <div class="info-label">Symptoms:</div>
                    <div class="symptoms-list">
                        <?php 
                        $symptoms = explode(',', $aid['symptoms']);
                        foreach($symptoms as $symptom): 
                        ?>
                        <span class="symptom-badge"><?php echo trim($symptom); ?></span>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="info-label">First Aid:</div>
                    <div class="first-aid-steps">
                        <?php 
                        $steps = explode("\n", $aid['first_aid_steps']);
                        foreach($steps as $index => $step): 
                        ?>
                        <div class="step-item">
                            <span class="step-number"><?php echo $index + 1; ?></span>
                            <span><?php echo htmlspecialchars($step); ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if(!empty($aid['warning_signs'])): ?>
                    <div class="warning-box">
                        ⚠️ <?php echo htmlspecialchars($aid['warning_signs']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

    <script>
    async function checkSymptoms() {
        const symptom = document.getElementById('symptomInput').value.trim();
        if (!symptom) {
            alert('Please enter a symptom');
            return;
        }
        
        try {
            const response = await fetch(`api/first_aid_advice.php?symptom=${encodeURIComponent(symptom)}`);
            const advice = await response.json();
            displayFirstAidAdvice(advice);
        } catch (error) {
            console.error('Error:', error);
        }
    }
    
    function displayFirstAidAdvice(advice) {
        const container = document.getElementById('firstAidAdvice');
        
        if (!advice || advice.length === 0) {
            container.innerHTML = '<p class="no-results">No matching advice found. Try different symptoms.</p>';
            return;
        }
        
        let html = '<h3>Recommended Advice:</h3>';
        advice.forEach(item => {
            html += `
                <div class="advice-card">
                    <h4>${item.condition_name}</h4>
                    <p><strong>First Aid:</strong> ${item.first_aid_steps.replace(/\n/g, '<br>')}</p>
                    ${item.warning_signs ? `<p class="warning"><strong>⚠️ Warning:</strong> ${item.warning_signs}</p>` : ''}
                </div>
            `;
        });
        
        container.innerHTML = html;
    }
    
    // Symptom tags click handler
    document.querySelectorAll('.symptom-tag').forEach(tag => {
        tag.addEventListener('click', function() {
            document.getElementById('symptomInput').value = this.textContent;
            checkSymptoms();
        });
    });
    </script>
    
    <script src="assets/js/main.js"></script>
</body>
</html>