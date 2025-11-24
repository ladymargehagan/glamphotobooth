/**
 * Customer Registration Form Handler
 * js/register.js
 */

document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerForm');

    if (registerForm) {
        registerForm.addEventListener('submit', function (e) {
            e.preventDefault();
            handleRegistration();
        });
    }
});

/**
 * Handle registration form submission
 */
function handleRegistration() {
    // Get form elements
    const fullName = document.getElementById('fullName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const messageEl = document.getElementById('authMessage');

    // Clear previous errors
    clearErrorMessages();

    // Validate inputs
    const errors = validateRegistrationForm(fullName, email, phone, password, confirmPassword);

    if (Object.keys(errors).length > 0) {
        displayErrors(errors);
        return;
    }

    // Disable submit button
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Creating account...';

    // Send AJAX request
    const formData = new FormData();
    formData.append('full_name', fullName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('password', password);
    formData.append('confirm_password', confirmPassword);

    fetch('../actions/register_customer_action.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;

            if (data.success) {
                // Show success message
                messageEl.classList.add('success');
                messageEl.classList.remove('error');
                messageEl.textContent = data.message;
                messageEl.style.display = 'block';

                // Redirect to customer dashboard after 1.5 seconds
                setTimeout(() => {
                    window.location.href = 'customer/dashboard.php';
                }, 1500);
            } else {
                // Display errors
                if (data.errors && Object.keys(data.errors).length > 0) {
                    displayErrors(data.errors);
                } else {
                    messageEl.classList.add('error');
                    messageEl.classList.remove('success');
                    messageEl.textContent = data.message || 'An error occurred. Please try again.';
                    messageEl.style.display = 'block';
                }
            }
        })
        .catch(error => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalBtnText;

            messageEl.classList.add('error');
            messageEl.classList.remove('success');
            messageEl.textContent = 'Network error. Please try again.';
            messageEl.style.display = 'block';
        });
}

/**
 * Validate registration form
 */
function validateRegistrationForm(fullName, email, phone, password, confirmPassword) {
    const errors = {};

    // Full name validation
    if (!fullName) {
        errors['full_name'] = 'Full name is required';
    } else if (fullName.length < 2) {
        errors['full_name'] = 'Full name must be at least 2 characters';
    }

    // Email validation
    if (!email) {
        errors['email'] = 'Email is required';
    } else if (!isValidEmail(email)) {
        errors['email'] = 'Invalid email format';
    }

    // Phone validation
    if (!phone) {
        errors['phone'] = 'Phone number is required';
    } else if (!/^[\d\s\-\+\(\)]+$/.test(phone)) {
        errors['phone'] = 'Invalid phone number format';
    } else if (phone.replace(/\D/g, '').length < 7) {
        errors['phone'] = 'Phone number must have at least 7 digits';
    }

    // Password validation
    if (!password) {
        errors['password'] = 'Password is required';
    } else if (password.length < 8) {
        errors['password'] = 'Password must be at least 8 characters';
    } else if (!/[A-Z]/.test(password)) {
        errors['password'] = 'Password must contain at least one uppercase letter';
    } else if (!/[a-z]/.test(password)) {
        errors['password'] = 'Password must contain at least one lowercase letter';
    } else if (!/[0-9]/.test(password)) {
        errors['password'] = 'Password must contain at least one number';
    }

    // Confirm password validation
    if (!confirmPassword) {
        errors['confirm_password'] = 'Confirm password is required';
    } else if (password !== confirmPassword) {
        errors['confirm_password'] = 'Passwords do not match';
    }

    return errors;
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Display validation errors
 */
function displayErrors(errors) {
    for (const field in errors) {
        const inputEl = document.getElementById(field.replace('_', ''));
        const errorMessage = errors[field];

        if (inputEl) {
            // Add error styling
            inputEl.classList.add('error');

            // Show error message
            const errorEl = document.createElement('small');
            errorEl.classList.add('error-text');
            errorEl.textContent = errorMessage;
            errorEl.style.color = 'var(--error)';
            errorEl.style.display = 'block';
            errorEl.style.marginTop = '0.25rem';
            errorEl.style.fontSize = '0.875rem';

            // Insert error after input
            if (inputEl.nextElementSibling && inputEl.nextElementSibling.classList.contains('error-text')) {
                inputEl.nextElementSibling.remove();
            }
            inputEl.parentElement.appendChild(errorEl);
        }
    }
}

/**
 * Clear error messages
 */
function clearErrorMessages() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.classList.remove('error');
        const errorEl = input.parentElement.querySelector('.error-text');
        if (errorEl) {
            errorEl.remove();
        }
    });
}
