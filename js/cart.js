/**
 * Shopping Cart Script
 * js/cart.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Update cart badge count when page loads
    if (window.isLoggedIn) {
        updateCartBadge();
    }
});

/**
 * Add item to cart with animation
 */
window.addToCart = function(productId, productTitle, productPrice) {
    if (!window.isLoggedIn) {
        window.location.href = window.loginUrl;
        return;
    }

    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value;
    if (!csrfToken) {
        showCartError('Security token not found');
        return;
    }

    const formData = new FormData();
    formData.append('csrf_token', csrfToken);
    formData.append('product_id', productId);
    formData.append('quantity', 1);

    // Get the button element for animation
    const button = event ? event.target : null;

    fetch(getActionUrl('add_to_cart_action.php'), {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge
            if (data.cart_count) {
                updateCartBadgeCount(data.cart_count);
            }

            // Show success message
            showCartSuccess(`${productTitle} added to cart!`);

            // Animate button
            if (button) {
                button.textContent = '✓ Added to Cart';
                button.disabled = true;
                setTimeout(() => {
                    button.textContent = 'Add to Cart';
                    button.disabled = false;
                }, 2000);
            }
        } else {
            showCartError(data.message || 'Failed to add to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCartError('Network error. Please try again.');
    });
};

/**
 * Update cart badge
 */
function updateCartBadge() {
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || generateCSRFToken();

    fetch(getActionUrl('fetch_cart_action.php'), {
        method: 'POST',
        body: new URLSearchParams({
            csrf_token: csrfToken
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.count > 0) {
            updateCartBadgeCount(data.count);
        }
    })
    .catch(error => console.error('Error updating cart badge:', error));
}

/**
 * Update cart badge count
 */
function updateCartBadgeCount(count) {
    let badge = document.getElementById('cartBadge');
    if (!badge) {
        // Create badge if it doesn't exist
        const cartIcon = document.querySelector('.cart-icon');
        if (cartIcon) {
            badge = document.createElement('span');
            badge.id = 'cartBadge';
            badge.className = 'cart-badge';
            cartIcon.appendChild(badge);
        } else {
            return;
        }
    }

    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'block';
    } else {
        badge.style.display = 'none';
    }
}

/**
 * Show cart success message
 */
function showCartSuccess(message) {
    const msgEl = document.getElementById('cartSuccessMessage');
    if (!msgEl) {
        // Create message element if it doesn't exist
        const container = document.body;
        const msg = document.createElement('div');
        msg.id = 'cartSuccessMessage';
        msg.className = 'cart-notification success';
        msg.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(msg);

        setTimeout(() => {
            msg.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => msg.remove(), 300);
        }, 3000);
    } else {
        msgEl.querySelector('span').textContent = message;
        msgEl.style.display = 'flex';
        setTimeout(() => {
            msgEl.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => msgEl.style.display = 'none', 300);
        }, 3000);
    }
}

/**
 * Show cart error message
 */
function showCartError(message) {
    const msgEl = document.getElementById('cartErrorMessage');
    if (!msgEl) {
        // Create message element if it doesn't exist
        const container = document.body;
        const msg = document.createElement('div');
        msg.id = 'cartErrorMessage';
        msg.className = 'cart-notification error';
        msg.innerHTML = `
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span>${message}</span>
        `;
        container.appendChild(msg);

        setTimeout(() => {
            msg.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => msg.remove(), 300);
        }, 3000);
    } else {
        msgEl.querySelector('span').textContent = message;
        msgEl.style.display = 'flex';
        setTimeout(() => {
            msgEl.style.animation = 'slideOut 0.3s ease forwards';
            setTimeout(() => msgEl.style.display = 'none', 300);
        }, 3000);
    }
}

/**
 * Get action URL based on page context
 */
function getActionUrl(action) {
    const baseUrl = window.location.pathname.includes('/customer/') ? '../' : '';
    return baseUrl + 'actions/' + action;
}

/**
 * Generate CSRF token (fallback)
 */
function generateCSRFToken() {
    // In a real app, this would be passed from the server
    return Math.random().toString(36).substring(7);
}

// Add styles for notifications
const style = document.createElement('style');
style.textContent = `
    .cart-notification {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        font-size: 0.95rem;
    }

    .cart-notification svg {
        width: 20px;
        height: 20px;
        flex-shrink: 0;
        stroke-width: 2;
    }

    .cart-notification.success {
        background: rgba(76, 175, 80, 0.95);
        color: white;
        border: 1px solid rgba(76, 175, 80, 0.3);
    }

    .cart-notification.error {
        background: rgba(211, 47, 47, 0.95);
        color: white;
        border: 1px solid rgba(211, 47, 47, 0.3);
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(20px);
        }
    }

    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--primary);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: none;
        flex-align: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        line-height: 24px;
        text-align: center;
    }
`;
document.head.appendChild(style);
