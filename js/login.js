/**
 * Customer Login Form Handler
 * js/login.js
 */

document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.getElementById('loginForm');

    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            e.preventDefault();
            handleLogin();
        });
    }
});

/**
 * Handle login form submission
 */
function handleLogin() {
    // Get form elements
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const rememberMe = document.querySelector('input[name="remember"]').checked;
    const messageEl = document.getElementById('authMessage');

    // Clear previous errors
    clearErrorMessages();

    // Validate inputs
    const errors = validateLoginForm(email, password);

    if (Object.keys(errors).length > 0) {
        displayErrors(errors);
        return;
    }

    // Disable submit button
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = 'Signing in...';

    // Send AJAX request
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    if (rememberMe) {
        formData.append('remember', 'on');
    }

    fetch('../actions/login_customer_action.php', {
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

                // Redirect to customer dashboard after 1 second
                setTimeout(() => {
                    window.location.href = 'customer/dashboard.php';
                }, 1000);
            } else {
                // Display errors
                if (data.errors && Object.keys(data.errors).length > 0) {
                    displayErrors(data.errors);
                } else {
                    messageEl.classList.add('error');
                    messageEl.classList.remove('success');
                    messageEl.textContent = data.message || 'Login failed. Please try again.';
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
 * Validate login form
 */
function validateLoginForm(email, password) {
    const errors = {};

    // Email validation
    if (!email) {
        errors['email'] = 'Email is required';
    } else if (!isValidEmail(email)) {
        errors['email'] = 'Invalid email format';
    }

    // Password validation
    if (!password) {
        errors['password'] = 'Password is required';
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
