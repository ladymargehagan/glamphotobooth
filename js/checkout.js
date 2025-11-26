/**
 * Checkout Script
 * js/checkout.js
 */

window.proceedToPayment = function() {
    const form = document.getElementById('checkoutForm');
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

    const button = event.target;
    button.disabled = true;
    button.textContent = 'Creating order...';

    // Create order
    const formData = new FormData();
    formData.append('csrf_token', csrfToken);

    fetch(window.siteUrl + '/actions/create_order_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
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
        showError('Network error. Please try again.');
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
