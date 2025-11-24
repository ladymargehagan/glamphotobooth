// Authentication Pages JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const forms = document.querySelectorAll('.auth-form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Clear previous messages
            const messageDiv = document.getElementById('authMessage');
            if (messageDiv) {
                messageDiv.style.display = 'none';
            }

            // Validate passwords match (for registration and provider signup)
            const password = form.querySelector('#password');
            const confirmPassword = form.querySelector('#confirmPassword');

            if (password && confirmPassword) {
                if (password.value !== confirmPassword.value) {
                    showMessage('Passwords do not match!', 'error');
                    return;
                }

                // Validate password strength
                if (!validatePassword(password.value)) {
                    showMessage('Password must be at least 8 characters with uppercase, lowercase, and numbers', 'error');
                    return;
                }
            }

            // In production, this would submit to the server
            // For now, simulate successful submission
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Processing...';

            setTimeout(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;

                const action = form.querySelector('input[name="action"]')?.value;

                if (action === 'login') {
                    showMessage('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        // In production, redirect based on user role
                        window.location.href = 'views/customer/dashboard.php';
                    }, 1500);
                } else if (action === 'register') {
                    showMessage('Account created successfully! Please check your email to verify.', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 2000);
                } else if (action === 'provider_signup') {
                    showMessage('Application submitted! We\'ll review it within 48 hours.', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 3000);
                }
            }, 1500);
        });
    });

    // Portfolio file upload
    const portfolioUpload = document.getElementById('portfolioUpload');
    const portfolioFiles = document.getElementById('portfolioFiles');
    const selectedFiles = document.getElementById('selectedFiles');

    if (portfolioUpload && portfolioFiles) {
        portfolioUpload.addEventListener('click', function() {
            portfolioFiles.click();
        });

        portfolioUpload.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('active');
        });

        portfolioUpload.addEventListener('dragleave', function() {
            this.classList.remove('active');
        });

        portfolioUpload.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('active');

            const files = e.dataTransfer.files;
            handleFiles(files);
        });

        portfolioFiles.addEventListener('change', function() {
            handleFiles(this.files);
        });
    }

    function handleFiles(files) {
        if (!selectedFiles) return;

        if (files.length < 5) {
            showMessage('Please upload at least 5 portfolio images', 'error');
            return;
        }

        if (files.length > 10) {
            showMessage('Maximum 10 images allowed', 'error');
            return;
        }

        let fileList = '<strong>Selected files:</strong><br>';
        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            // Validate file size (5MB max)
            if (file.size > 5 * 1024 * 1024) {
                showMessage(`${file.name} is too large. Maximum 5MB per file.`, 'error');
                return;
            }

            // Validate file type
            if (!file.type.startsWith('image/')) {
                showMessage(`${file.name} is not an image file.`, 'error');
                return;
            }

            fileList += `${i + 1}. ${file.name} (${formatFileSize(file.size)})<br>`;
        }

        selectedFiles.innerHTML = fileList;
    }

    // Social auth buttons
    const socialBtns = document.querySelectorAll('.social-btn');
    socialBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const provider = this.classList.contains('google-btn') ? 'Google' : 'Facebook';
            if (window.showNotification) {
                showNotification(`${provider} authentication coming soon!`, 'info');
            }
        });
    });

    // Real-time password validation
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strength = getPasswordStrength(this.value);
            // Could add visual password strength indicator here
        });
    }
});

// Helper Functions
function showMessage(message, type) {
    const messageDiv = document.getElementById('authMessage');
    if (messageDiv) {
        messageDiv.textContent = message;
        messageDiv.className = `auth-message ${type}`;
        messageDiv.style.display = 'block';

        // Scroll to message
        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // Auto-hide error messages after 5 seconds
        if (type === 'error') {
            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    }
}

function validatePassword(password) {
    // At least 8 characters, one uppercase, one lowercase, one number
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
    return regex.test(password);
}

function getPasswordStrength(password) {
    let strength = 0;

    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    return strength;
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';

    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));

    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Handle URL parameters (e.g., for email verification, password reset)
const urlParams = new URLSearchParams(window.location.search);
const message = urlParams.get('message');
const type = urlParams.get('type');

if (message && type) {
    setTimeout(() => {
        showMessage(decodeURIComponent(message), type);
    }, 100);
}
