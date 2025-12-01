/**
 * Checkout Script
 * js/checkout.js
 */

window.proceedToPayment = function(clickEvent) {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const address = document.getElementById('address').value.trim();
    const csrfToken = document.querySelector('input[name="csrf_token"]').value;

    // Validate form
    if (!firstName || !lastName || !email || !phone || !address) {
        showError('Please fill in all delivery information');
        return;
    }

    // Validate email
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError('Please enter a valid email address');
        return;
    }

    const button = clickEvent ? clickEvent.target : document.querySelector('.btn-checkout');
    button.disabled = true;
    button.textContent = 'Creating order...';

    // Create order
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);

    fetch(window.siteUrl + '/actions/create_order_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        // Check if response is ok (status 200-299)
        if (!response.ok) {
            // Try to parse as JSON first
            return response.text().then(text => {
                try {
                    const json = JSON.parse(text);
                    throw new Error(json.message || 'Server error occurred');
                } catch (e) {
                    // If not JSON, throw with status text
                    throw new Error(`Server error (${response.status}): ${response.statusText}`);
                }
            });
        }
        // Check content type
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        } else {
            // If not JSON, get text and try to parse
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid response from server');
                }
            });
        }
    })
    .then(data => {
        if (data.success) {
            // Store order details in session storage
            sessionStorage.setItem('orderId', data.order_id);
            sessionStorage.setItem('customerName', firstName + ' ' + lastName);
            sessionStorage.setItem('customerEmail', email);
            sessionStorage.setItem('customerPhone', phone);

            // Redirect to payment initialization
            window.location.href = window.siteUrl + '/customer/payment.php?order_id=' + data.order_id;
        } else {
            showError(data.message || 'Failed to create order');
            button.disabled = false;
            button.textContent = 'Proceed to Payment';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showError(error.message || 'Network error. Please try again.');
        button.disabled = false;
        button.textContent = 'Proceed to Payment';
    });
};

function showError(message) {
    const msg = document.getElementById('errorMessage');
    document.getElementById('errorText').textContent = message;
    msg.classList.add('show');
    setTimeout(() => {
        msg.classList.remove('show');
    }, 5000);
}
