// Main JavaScript file for Meducare

// Global functions
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

// Loading spinner
function showLoading(show = true) {
    let spinner = document.getElementById('loadingSpinner');
    if (!spinner && show) {
        spinner = document.createElement('div');
        spinner.id = 'loadingSpinner';
        spinner.className = 'loading-spinner';
        spinner.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(spinner);
    } else if (spinner && !show) {
        spinner.remove();
    }
}

// API request helper
async function apiRequest(url, method = 'GET', data = null) {
    const options = {
        method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (data) {
        options.body = JSON.stringify(data);
    }
    
    try {
        showLoading(true);
        const response = await fetch(url, options);
        const result = await response.json();
        
        if (!response.ok) {
            throw new Error(result.error || 'API request failed');
        }
        
        return result;
    } catch (error) {
        showToast(error.message, 'error');
        throw error;
    } finally {
        showLoading(false);
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search form
    const searchForm = document.querySelector('.search-box');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchInput = this.querySelector('input[type="text"]');
            const searchTerm = searchInput.value.trim();
            
            if (searchTerm) {
                window.location.href = `medicines.php?search=${encodeURIComponent(searchTerm)}`;
            }
        });
    }
    
    // Filter tabs
    const filterTabs = document.querySelectorAll('.filter-tab');
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filterTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.textContent.trim();
            if (category === 'All Medicines') {
                window.location.href = 'medicines.php';
            } else {
                window.location.href = `medicines.php?category=${encodeURIComponent(category)}`;
            }
        });
    });
    
    // Symptom checker
    const symptomInput = document.querySelector('.symptom-input input');
    const symptomButton = document.querySelector('.symptom-input button');
    if (symptomButton && symptomInput) {
        symptomButton.addEventListener('click', async function() {
            const symptom = symptomInput.value.trim();
            if (!symptom) return;
            
            try {
                const advice = await apiRequest(`api/first_aid_advice.php?symptom=${encodeURIComponent(symptom)}`);
                displayFirstAidAdvice(advice);
            } catch (error) {
                console.error('Error fetching advice:', error);
            }
        });
        
        symptomInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                symptomButton.click();
            }
        });
    }
    
    // Symptom tags
    const symptomTags = document.querySelectorAll('.symptom-tag');
    symptomTags.forEach(tag => {
        tag.addEventListener('click', function() {
            symptomInput.value = this.textContent;
            symptomButton.click();
        });
    });
});

// Display first aid advice
function displayFirstAidAdvice(advice) {
    const adviceContainer = document.getElementById('firstAidAdvice');
    if (!adviceContainer) return;
    
    if (!advice || advice.length === 0) {
        adviceContainer.innerHTML = '<p class="no-results">No matching advice found. Try different symptoms.</p>';
        return;
    }
    
    let html = '';
    advice.forEach(item => {
        html += `
            <div class="advice-card">
                <h3>${item.condition_name}</h3>
                <div class="symptoms-list">
                    <strong>Symptoms:</strong> ${item.symptoms}
                </div>
                <div class="steps-list">
                    <strong>First Aid Steps:</strong>
                    <ol>
                        ${item.first_aid_steps.split('\n').map(step => `<li>${step}</li>`).join('')}
                    </ol>
                </div>
                ${item.warning_signs ? `
                    <div class="warning-box">
                        <strong>⚠️ Warning Signs:</strong> ${item.warning_signs}
                    </div>
                ` : ''}
            </div>
        `;
    });
    
    adviceContainer.innerHTML = html;
}

// Modal functions
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal when clicking outside
window.addEventListener('click', function(e) {
    document.querySelectorAll('.modal').forEach(modal => {
        if (e.target === modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    });
});

// Form validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[0-9+\-\s]+$/;
    return re.test(phone);
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    return strength;
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(element => {
        element.addEventListener('mouseenter', function(e) {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.dataset.tooltip;
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width - tooltip.offsetWidth) / 2 + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            
            this.addEventListener('mouseleave', function() {
                tooltip.remove();
            }, { once: true });
        });
    });
});

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});