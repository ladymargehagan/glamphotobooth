/**
 * Login Form Handler
 * js/login.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const submitBtn = document.getElementById('submitBtn');
    const successMsg = document.getElementById('successMessage');
    const errorMsg = document.getElementById('errorMessage');
    const errorText = document.getElementById('errorText');

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Clear previous errors
        clearErrors();

        // Get form data
        const formData = new FormData(form);

        // Validate on client side
        const validation = validateForm();
        if (!validation.valid) {
            displayFieldError(validation.field, validation.message);
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.textContent = 'Logging in...';

        try {
            const response = await fetch('/~lady.hagan/glamphotobooth/actions/login_customer_action.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                successMsg.classList.remove('hidden');
                successMsg.style.display = 'flex';
                form.style.display = 'none';

                // Redirect based on user role
                setTimeout(() => {
                    const userRole = data.user_role;
                    let redirectUrl = '/~lady.hagan/glamphotobooth/index.php';

                    // Route based on role
                    // 1 = admin, 2 = photographer, 3 = vendor, 4 = customer
                    if (userRole === 1) {
                        redirectUrl = '/~lady.hagan/glamphotobooth/admin/dashboard.php';
                    } else if (userRole === 2) {
                        redirectUrl = '/~lady.hagan/glamphotobooth/photographer/dashboard.php';
                    } else if (userRole === 3) {
                        redirectUrl = '/~lady.hagan/glamphotobooth/vendor/dashboard.php';
                    } else if (userRole === 4) {
                        redirectUrl = '/~lady.hagan/glamphotobooth/shop.php';
                    }

                    window.location.href = redirectUrl;
                }, 2000);
            } else {
                // Show error
                errorText.textContent = data.message || 'Login failed. Please try again.';
                errorMsg.classList.remove('hidden');
                errorMsg.style.display = 'flex';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Login';
            }
        } catch (error) {
            console.error('Error:', error);
            errorText.textContent = 'Network error. Please check your connection and try again.';
            errorMsg.classList.remove('hidden');
            errorMsg.style.display = 'flex';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Login';
        }
    });

    /**
     * Validate form
     */
    function validateForm() {
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;

        if (!email) {
            return { valid: false, field: 'email', message: 'Email is required' };
        }

        if (!isValidEmail(email)) {
            return { valid: false, field: 'email', message: 'Invalid email format' };
        }

        if (!password) {
            return { valid: false, field: 'password', message: 'Password is required' };
        }

        return { valid: true };
    }

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Display field error
     */
    function displayFieldError(field, message) {
        const errorElement = document.getElementById(field + 'Error');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    /**
     * Clear all errors
     */
    function clearErrors() {
        const errorElements = document.querySelectorAll('.form-error');
        errorElements.forEach(el => el.textContent = '');
        errorMsg.classList.add('hidden');
    }

    // Real-time field validation
    document.getElementById('email').addEventListener('blur', function() {
        if (this.value.trim() && !isValidEmail(this.value.trim())) {
            displayFieldError('email', 'Invalid email format');
        } else {
            displayFieldError('email', '');
        }
    });
});
