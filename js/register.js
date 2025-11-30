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
    const roleRadios = document.querySelectorAll('input[name="role"]');

    // Handle role selection to show/hide provider-specific fields
    roleRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const isProvider = this.value === '2' || this.value === '3';

            // Show/hide provider fields
            document.getElementById('businessNameGroup').style.display = isProvider ? 'block' : 'none';
            document.getElementById('descriptionGroup').style.display = isProvider ? 'block' : 'none';
            document.getElementById('rateGroup').style.display = isProvider ? 'block' : 'none';
            document.getElementById('serviceTypeGroup').style.display = isProvider ? 'block' : 'none';

            // Clear provider fields when not selected
            if (!isProvider) {
                document.getElementById('businessName').value = '';
                document.getElementById('description').value = '';
                document.getElementById('hourlyRate').value = '';
                document.getElementById('serviceType').value = '';
            }
        });
    });

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
            // Use relative path to action file
            const actionUrl = '../actions/register_customer_action.php';
            
            const response = await fetch(actionUrl, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const text = await response.text();
            if (!text) {
                throw new Error('Empty response from server');
            }

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text);
                throw new Error('Invalid response from server');
            }

            if (data.success) {
                // Show success message
                successMsg.classList.remove('hidden');
                successMsg.style.display = 'flex';
                form.style.display = 'none';

                // Redirect after 2 seconds
                setTimeout(() => {
                    window.location.href = '../index.php';
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
        const phone = document.getElementById('phone').value.trim();
        const city = document.getElementById('city').value.trim();
        const role = document.querySelector('input[name="role"]:checked').value;

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

        if (!phone) {
            return { valid: false, field: 'phone', message: 'Phone number is required' };
        }

        if (!city) {
            return { valid: false, field: 'city', message: 'City/Location is required' };
        }

        // Validate provider-specific fields
        if (role === '2' || role === '3') {
            const businessName = document.getElementById('businessName').value.trim();
            const hourlyRate = document.getElementById('hourlyRate').value;

            if (!businessName) {
                return { valid: false, field: 'businessName', message: 'Business name is required' };
            }

            if (!hourlyRate || parseFloat(hourlyRate) <= 0) {
                return { valid: false, field: 'hourlyRate', message: 'Hourly rate must be greater than 0' };
            }
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
