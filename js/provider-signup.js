document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('providerForm');
    const messageDiv = document.getElementById('authMessage');

    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            // Basic validation
            if (data.full_name.length < 2) {
                showMessage('Full name must be at least 2 characters', 'error');
                return;
            }

            if (!isValidEmail(data.email)) {
                showMessage('Please enter a valid email address', 'error');
                return;
            }

            if (data.password.length < 8) {
                showMessage('Password must be at least 8 characters', 'error');
                return;
            }

            if (data.business_name.length < 2) {
                showMessage('Business name must be at least 2 characters', 'error');
                return;
            }

            if (!data.service_type) {
                showMessage('Please select a service type', 'error');
                return;
            }

            if (data.experience_years < 0) {
                showMessage('Years of experience cannot be negative', 'error');
                return;
            }

            if (data.description.length < 10) {
                showMessage('Business description must be at least 10 characters', 'error');
                return;
            }

            // Submit form
            try {
                const response = await fetch('actions/provider_signup_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (result.success) {
                    showMessage('Application submitted successfully! You will receive an email confirmation within 48 hours.', 'success');
                    form.reset();
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else {
                    showMessage(result.message || 'An error occurred. Please try again.', 'error');
                }
            } catch (error) {
                showMessage('Network error. Please try again.', 'error');
            }
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function showMessage(message, type) {
        messageDiv.style.display = 'block';
        messageDiv.className = 'message message-' + type;
        messageDiv.innerHTML = message;
        messageDiv.scrollIntoView({ behavior: 'smooth' });
    }
});
