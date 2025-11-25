/**
 * Profile Setup/Edit Script
 * js/profile.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const businessNameInput = document.getElementById('businessName');
    const descriptionInput = document.getElementById('description');
    const hourlyRateInput = document.getElementById('hourlyRate');
    const submitBtn = document.getElementById('submitBtn');

    // Determine if this is an add or update form
    const formType = window.profileFormType || 'add';

    // Event listeners
    form.addEventListener('submit', handleSubmit);
    businessNameInput.addEventListener('blur', validateBusinessName);
    descriptionInput.addEventListener('blur', validateDescription);
    hourlyRateInput.addEventListener('blur', validateHourlyRate);

    /**
     * Validate business name
     */
    function validateBusinessName() {
        const value = businessNameInput.value.trim();
        const errorEl = document.getElementById('businessNameError');

        if (!value) {
            errorEl.textContent = 'Business name is required';
            return false;
        }

        if (value.length < 3 || value.length > 255) {
            errorEl.textContent = 'Business name must be between 3 and 255 characters';
            return false;
        }

        errorEl.textContent = '';
        return true;
    }

    /**
     * Validate description
     */
    function validateDescription() {
        const value = descriptionInput.value.trim();
        const errorEl = document.getElementById('descriptionError');

        if (!value) {
            errorEl.textContent = 'Description is required';
            return false;
        }

        if (value.length < 10 || value.length > 5000) {
            errorEl.textContent = 'Description must be between 10 and 5000 characters';
            return false;
        }

        errorEl.textContent = '';
        return true;
    }

    /**
     * Validate hourly rate
     */
    function validateHourlyRate() {
        const value = parseFloat(hourlyRateInput.value);
        const errorEl = document.getElementById('hourlyRateError');

        if (!value || isNaN(value)) {
            errorEl.textContent = 'Hourly rate is required';
            return false;
        }

        if (value <= 0 || value > 99999.99) {
            errorEl.textContent = 'Hourly rate must be a valid positive number';
            return false;
        }

        errorEl.textContent = '';
        return true;
    }

    /**
     * Handle form submission
     */
    function handleSubmit(e) {
        e.preventDefault();

        // Clear previous messages
        document.getElementById('successMessage').classList.remove('show');
        document.getElementById('errorMessage').classList.remove('show');

        // Validate all fields
        const businessNameValid = validateBusinessName();
        const descriptionValid = validateDescription();
        const hourlyRateValid = validateHourlyRate();

        if (!businessNameValid || !descriptionValid || !hourlyRateValid) {
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = formType === 'add' ? 'Creating...' : 'Saving...';

        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('business_name', businessNameInput.value.trim());
        formData.append('description', descriptionInput.value.trim());
        formData.append('hourly_rate', hourlyRateInput.value);

        // Add provider_id for update form
        if (formType === 'update') {
            formData.append('provider_id', document.querySelector('input[name="provider_id"]').value);
        }

        let url = '../actions/add_provider_action.php';
        if (formType === 'update') {
            url = '../actions/update_provider_action.php';
        }

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Profile saved successfully');
                setTimeout(() => {
                    // Redirect based on role
                    const userRole = getUserRole();
                    if (userRole === 2) {
                        window.location.href = '../photographer/dashboard.php';
                    } else if (userRole === 3) {
                        window.location.href = '../vendor/dashboard.php';
                    } else {
                        window.location.href = '../customer/dashboard.php';
                    }
                }, 1500);
            } else {
                showError(data.message || 'Failed to save profile');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network error. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    }

    /**
     * Show success message
     */
    function showSuccess(message) {
        const msg = document.getElementById('successMessage');
        document.getElementById('successText').textContent = message;
        msg.classList.add('show');
    }

    /**
     * Show error message
     */
    function showError(message) {
        const msg = document.getElementById('errorMessage');
        document.getElementById('errorText').textContent = message;
        msg.classList.add('show');
    }

    /**
     * Get user role from session data
     * Note: This is a simplified approach; in production you might fetch this from the server
     */
    function getUserRole() {
        // This would ideally come from session data
        // For now, we'll redirect to the appropriate dashboard based on the current URL
        const url = window.location.pathname;
        if (url.includes('photographer')) return 2;
        if (url.includes('vendor')) return 3;
        return 4;
    }
});
