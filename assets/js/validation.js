// Form validation utilities

// Registration form validation
function validateRegistrationForm(formData) {
    const errors = [];
    
    // First name validation
    if (!formData.first_name || formData.first_name.length < 2) {
        errors.push('First name must be at least 2 characters long');
    }
    
    // Last name validation
    if (!formData.last_name || formData.last_name.length < 2) {
        errors.push('Last name must be at least 2 characters long');
    }
    
    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!formData.email || !emailRegex.test(formData.email)) {
        errors.push('Please enter a valid email address');
    }
    
    // Phone validation (Bangladesh format)
    const phoneRegex = /^(?:\+88|01)?\d{11}$/;
    if (!formData.phone || !phoneRegex.test(formData.phone.replace(/\s/g, ''))) {
        errors.push('Please enter a valid Bangladesh phone number (11 digits)');
    }
    
    // Password validation
    if (!formData.password || formData.password.length < 6) {
        errors.push('Password must be at least 6 characters long');
    }
    
    // Confirm password
    if (formData.password !== formData.confirm_password) {
        errors.push('Passwords do not match');
    }
    
    // Password strength
    const strength = checkPasswordStrength(formData.password);
    if (strength < 3) {
        errors.push('Password is too weak. Include uppercase, lowercase, numbers, and special characters');
    }
    
    // Terms acceptance
    if (!formData.terms) {
        errors.push('You must agree to the Terms of Service');
    }
    
    return errors;
}

// Login form validation
function validateLoginForm(formData) {
    const errors = [];
    
    if (!formData.email || !validateEmail(formData.email)) {
        errors.push('Please enter a valid email address');
    }
    
    if (!formData.password) {
        errors.push('Please enter your password');
    }
    
    return errors;
}

// Medicine search validation
function validateMedicineSearch(searchTerm) {
    if (!searchTerm || searchTerm.trim().length < 2) {
        return 'Please enter at least 2 characters to search';
    }
    return null;
}

// Question form validation
function validateQuestionForm(formData) {
    const errors = [];
    
    if (!formData.question || formData.question.trim().length < 10) {
        errors.push('Question must be at least 10 characters long');
    }
    
    if (!formData.category) {
        errors.push('Please select a category');
    }
    
    return errors;
}

// Appointment form validation
function validateAppointmentForm(formData) {
    const errors = [];
    
    if (!formData.doctor_name) {
        errors.push('Please enter doctor name');
    }
    
    if (!formData.appointment_date) {
        errors.push('Please select appointment date');
    } else {
        const selectedDate = new Date(formData.appointment_date);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            errors.push('Appointment date cannot be in the past');
        }
    }
    
    if (!formData.appointment_time) {
        errors.push('Please select appointment time');
    }
    
    if (!formData.reason || formData.reason.trim().length < 10) {
        errors.push('Please provide a reason for appointment (minimum 10 characters)');
    }
    
    return errors;
}

// Profile update validation
function validateProfileForm(formData) {
    const errors = [];
    
    if (!formData.first_name || formData.first_name.length < 2) {
        errors.push('First name must be at least 2 characters');
    }
    
    if (!formData.last_name || formData.last_name.length < 2) {
        errors.push('Last name must be at least 2 characters');
    }
    
    if (!validateEmail(formData.email)) {
        errors.push('Please enter a valid email');
    }
    
    if (formData.phone && !validateBangladeshPhone(formData.phone)) {
        errors.push('Please enter a valid Bangladesh phone number');
    }
    
    return errors;
}

// Helper: Validate email
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Helper: Validate Bangladesh phone
function validateBangladeshPhone(phone) {
    const cleaned = phone.replace(/\s+/g, '');
    const re = /^(?:\+88|01)?\d{11}$/;
    return re.test(cleaned);
}

// Helper: Check password strength
function checkPasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 8) strength++;
    if (password.match(/[a-z]+/)) strength++;
    if (password.match(/[A-Z]+/)) strength++;
    if (password.match(/[0-9]+/)) strength++;
    if (password.match(/[$@#&!]+/)) strength++;
    
    return strength;
}

// Display validation errors
function displayErrors(errors, containerId) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    if (errors.length === 0) {
        container.style.display = 'none';
        container.innerHTML = '';
        return;
    }
    
    let html = '<ul class="error-list">';
    errors.forEach(error => {
        html += `<li><i class="fas fa-exclamation-circle"></i> ${error}</li>`;
    });
    html += '</ul>';
    
    container.innerHTML = html;
    container.style.display = 'block';
}

// Clear validation errors
function clearErrors(containerId) {
    const container = document.getElementById(containerId);
    if (container) {
        container.style.display = 'none';
        container.innerHTML = '';
    }
}

// Real-time validation for forms
document.addEventListener('DOMContentLoaded', function() {
    // Registration form real-time validation
    const regForm = document.getElementById('registrationForm');
    if (regForm) {
        const inputs = regForm.querySelectorAll('input[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            input.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateField(this);
                }
            });
        });
        
        // Password strength meter
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const strengthHint = document.getElementById('strengthHint');
        
        if (passwordInput && strengthBar) {
            passwordInput.addEventListener('input', function() {
                const strength = checkPasswordStrength(this.value);
                updatePasswordStrength(strength, strengthBar, strengthHint);
            });
        }
    }
});

// Validate single field
function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    switch (field.id) {
        case 'first_name':
        case 'last_name':
            if (value.length < 2) {
                isValid = false;
                errorMessage = 'Minimum 2 characters required';
            }
            break;
            
        case 'email':
            if (!validateEmail(value)) {
                isValid = false;
                errorMessage = 'Invalid email address';
            }
            break;
            
        case 'phone':
            if (!validateBangladeshPhone(value)) {
                isValid = false;
                errorMessage = 'Invalid Bangladesh phone number';
            }
            break;
            
        case 'password':
            if (value.length < 6) {
                isValid = false;
                errorMessage = 'Minimum 6 characters required';
            }
            break;
            
        case 'confirm_password':
            const password = document.getElementById('password')?.value;
            if (value !== password) {
                isValid = false;
                errorMessage = 'Passwords do not match';
            }
            break;
    }
    
    const errorSpan = field.nextElementSibling?.classList.contains('field-error') 
        ? field.nextElementSibling 
        : null;
    
    if (!isValid) {
        field.classList.add('error');
        if (errorSpan) {
            errorSpan.textContent = errorMessage;
        } else {
            const span = document.createElement('span');
            span.className = 'field-error';
            span.textContent = errorMessage;
            field.parentNode.insertBefore(span, field.nextSibling);
        }
    } else {
        field.classList.remove('error');
        if (errorSpan) {
            errorSpan.remove();
        }
    }
    
    return isValid;
}

// Update password strength meter
function updatePasswordStrength(strength, strengthBar, strengthHint) {
    strengthBar.className = 'strength-bar';
    
    if (strength <= 2) {
        strengthBar.classList.add('weak');
        if (strengthHint) strengthHint.textContent = 'Weak password';
    } else if (strength <= 4) {
        strengthBar.classList.add('medium');
        if (strengthHint) strengthHint.textContent = 'Medium password';
    } else {
        strengthBar.classList.add('strong');
        if (strengthHint) strengthHint.textContent = 'Strong password';
    }
}