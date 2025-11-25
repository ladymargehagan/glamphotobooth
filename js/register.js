/**
 * Register Form Handler
 * js/register.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('registerForm');
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
        submitBtn.textContent = 'Creating Account...';

        try {
            const response = await fetch('/~lady.hagan/glamphotobooth/actions/register_customer_action.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Show success message
                successMsg.classList.remove('hidden');
                successMsg.style.display = 'flex';
                form.style.display = 'none';

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '/~lady.hagan/glamphotobooth/index.php';
                }, 2000);
            } else {
                // Show error
                errorText.textContent = data.message || 'Registration failed. Please try again.';
                errorMsg.classList.remove('hidden');
                errorMsg.style.display = 'flex';
                submitBtn.disabled = false;
                submitBtn.textContent = 'Create Account';
            }
        } catch (error) {
            console.error('Error:', error);
            errorText.textContent = 'Network error. Please check your connection and try again.';
            errorMsg.classList.remove('hidden');
            errorMsg.style.display = 'flex';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Create Account';
        }
    });

    /**
     * Validate form
     */
    function validateForm() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (!name) {
            return { valid: false, field: 'name', message: 'Full name is required' };
        }

        if (name.length < 2) {
            return { valid: false, field: 'name', message: 'Name must be at least 2 characters' };
        }

        if (!email) {
            return { valid: false, field: 'email', message: 'Email is required' };
        }

        if (!isValidEmail(email)) {
            return { valid: false, field: 'email', message: 'Invalid email format' };
        }

        if (!password) {
            return { valid: false, field: 'password', message: 'Password is required' };
        }

        if (password.length < 6) {
            return { valid: false, field: 'password', message: 'Password must be at least 6 characters' };
        }

        if (!confirmPassword) {
            return { valid: false, field: 'confirmPassword', message: 'Please confirm your password' };
        }

        if (password !== confirmPassword) {
            return { valid: false, field: 'confirmPassword', message: 'Passwords do not match' };
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

    document.getElementById('password').addEventListener('change', function() {
        if (this.value.length > 0 && this.value.length < 6) {
            displayFieldError('password', 'Password must be at least 6 characters');
        } else {
            displayFieldError('password', '');
        }
    });

    document.getElementById('confirmPassword').addEventListener('change', function() {
        const password = document.getElementById('password').value;
        if (this.value && this.value !== password) {
            displayFieldError('confirmPassword', 'Passwords do not match');
        } else {
            displayFieldError('confirmPassword', '');
        }
    });
});
