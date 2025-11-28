/**
 * Shop Page Script
 * js/shop.js
 */

// HTML escape helper function
function escapeHtml(str) {
    if (!str) return "";
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function escapeHtmlForAttribute(str) {
    if (!str) return "";
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.querySelector('input[name="category"]');
    const typeFilter = document.querySelector('input[name="product_type"]');
    const allCategoryInputs = document.querySelectorAll('input[name="category"]');
    const allTypeInputs = document.querySelectorAll('input[name="product_type"]');
    const productsGrid = document.getElementById('productsGrid');

    // Load initial products
    loadProducts();

    // Add event listeners to all filters
    allCategoryInputs.forEach(input => {
        input.addEventListener('change', loadProducts);
    });

    allTypeInputs.forEach(input => {
        input.addEventListener('change', loadProducts);
    });

    /**
     * Load products based on current filters
     */
    function loadProducts() {
        // Safely get category - default to "0" (all) if not found
        const categoryElement = document.querySelector('input[name="category"]:checked');
        const selectedCategory = categoryElement ? categoryElement.value : '0';

        // Safely get product_type - handle both radio buttons and hidden inputs
        let selectedType = 'all';
        const checkedTypeElement = document.querySelector('input[name="product_type"]:checked');
        if (checkedTypeElement) {
            selectedType = checkedTypeElement.value;
        } else {
            // Fallback: look for hidden input or any input with name="product_type"
            const hiddenTypeElement = document.querySelector('input[name="product_type"]');
            if (hiddenTypeElement) {
                selectedType = hiddenTypeElement.value;
            }
        }

        // Determine provider class based on page type (services vs shop)
        // Look for data-provider-class attribute on productsGrid
        const providerClass = productsGrid.dataset.providerClass || '0';

        const formData = new FormData();
        formData.append('cat_id', selectedCategory);
        formData.append('product_type', selectedType);
        formData.append('provider_class', providerClass); // Filter by provider type
        formData.append('page', 1);

        productsGrid.innerHTML = '<div style="grid-column: 1/-1; text-align: center; padding: 2rem;"><p>Loading products...</p></div>';

        // Use relative path - shop.php is in root, so actions/ is correct
        fetch('actions/fetch_products_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    renderProducts(data.data);
                } else {
                    showEmpty(data.message || 'No products found');
                }
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response text:', text);
                showEmpty('Error loading products');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showEmpty('Error loading products. Please try again.');
        });
    }

    /**
     * Render products in grid
     */
    function renderProducts(products) {
        productsGrid.innerHTML = '';

        if (!products || products.length === 0) {
            showEmpty('No products match your filters');
            return;
        }

        products.forEach(product => {
            const productCard = document.createElement('div');
            productCard.className = 'product-card';

            const imageHtml = product.image
                ? `<img src="${escapeHtml(product.image)}" alt="${escapeHtml(product.title)}">`
                : '📸';

            const description = product.description ?
                (product.description.length > 60 ? product.description.substring(0, 60) + '...' : product.description) :
                'No description available';

            const productTitle = escapeHtml(product.title || 'Untitled Product');
            const productPrice = parseFloat(product.price || 0);

            productCard.innerHTML = `
                <div class="product-card-link" onclick="openProviderModal(${product.product_id}, ${product.provider_id})" style="cursor: pointer;">
                    <div class="product-image">
                        ${imageHtml}
                    </div>
                    <div class="product-info">
                        <span class="product-category">Category</span>
                        <div class="product-title">${productTitle}</div>
                        <div class="product-type">${product.product_type || 'N/A'}</div>
                        <p class="product-description">${escapeHtml(description)}</p>
                        <div class="product-price">₵${productPrice.toFixed(2)}</div>
                    </div>
                </div>
                <button class="product-card-btn-add-to-cart" onclick="bookOrAddToCart(event, ${product.product_id}, ${product.provider_id}, ${productPrice}, '${productTitle}');">${product.product_type === 'service' ? 'Book Now' : 'Add to Cart'}</button>
            `;

            productsGrid.appendChild(productCard);
        });
    }

    /**
     * Show empty state
     */
    function showEmpty(message) {
        productsGrid.innerHTML = `
            <div class="empty-state">
                <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
                <h3 class="empty-state-title">${message}</h3>
                <p class="empty-state-text">Try adjusting your filters or browse all products</p>
            </div>
        `;
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

/**
 * Handle booking or add to cart
 */
function bookOrAddToCart(evt, productId, providerId, price, title) {
    evt.stopPropagation();

    if (!window.isLoggedIn) {
        window.location.href = window.loginUrl;
        return;
    }

    // Check button text to determine action
    const buttonText = evt.target.textContent.trim();
    if (buttonText === 'Book Now') {
        window.location.href = `${window.siteUrl}/customer/booking.php?provider_id=${providerId}&product_id=${productId}`;
    } else {
        // Add to cart for non-services
        addToCart(productId, title, price);
    }
}

/**
 * Open provider profile modal
 */
function openProviderModal(productId, providerId) {
    const modal = document.getElementById('providerModal');
    const modalBody = document.getElementById('modalBody');

    // Show loading state
    modalBody.innerHTML = '<div class="provider-modal-loading"><p>Loading provider information...</p></div>';
    modal.classList.add('active');

    // Fetch provider details and reviews
    fetchProviderProfile(productId, providerId);
}

/**
 * Close provider profile modal
 */
function closeProviderModal() {
    const modal = document.getElementById('providerModal');
    modal.classList.remove('active');
}

/**
 * Close modal when clicking outside content
 */
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('providerModal');

    modal.addEventListener('click', function(evt) {
        if (evt.target === modal) {
            closeProviderModal();
        }
    });
});

/**
 * Fetch provider profile and reviews
 */
function fetchProviderProfile(productId, providerId) {
    const formData = new FormData();
    formData.append('provider_id', providerId);
    formData.append('product_id', productId);

    fetch(`${window.siteUrl}/actions/fetch_provider_profile_action.php`, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            renderProviderProfile(data.provider, data.reviews);
        } else {
            showProviderError(data.message || 'Failed to load provider information');
        }
    })
    .catch(error => {
        console.error('Error fetching provider profile:', error);
        showProviderError('Error loading provider information. Please try again.');
    });
}

/**
 * Render provider profile in modal
 */
function renderProviderProfile(provider, reviews) {
    const modalBody = document.getElementById('modalBody');

    const ratingStars = generateStars(parseFloat(provider.rating || 0));
    const profileImage = provider.image ? `<img src="${escapeHtmlForAttribute(provider.image)}" alt="${escapeHtmlForAttribute(provider.business_name)}">` : '👤';

    let reviewsHtml = '';
    if (reviews && reviews.length > 0) {
        reviewsHtml = reviews.map(review => {
            const stars = generateStars(parseInt(review.rating || 0));
            return `
                <div class="review-item">
                    <div class="review-header">
                        <span class="review-author">${escapeHtml(review.customer_name || 'Anonymous')}</span>
                        <div>
                            <span class="review-rating">${stars}</span>
                        </div>
                    </div>
                    <div class="review-date">${formatReviewDate(review.review_date)}</div>
                    ${review.comment ? `<div class="review-comment">${escapeHtml(review.comment)}</div>` : ''}
                </div>
            `;
        }).join('');
    } else {
        reviewsHtml = '<div class="no-reviews">No reviews yet</div>';
    }

    const html = `
        <div class="provider-profile-header">
            <div class="provider-profile-cover">${profileImage}</div>
            <div class="provider-profile-name">${escapeHtml(provider.business_name || 'Provider')}</div>
            <div class="provider-rating">
                <span class="provider-rating-stars">${ratingStars}</span>
                <span>${parseFloat(provider.rating || 0).toFixed(1)}/5 (${parseInt(provider.total_reviews || 0)} reviews)</span>
            </div>
            ${provider.description ? `<div class="provider-description">${escapeHtml(provider.description)}</div>` : ''}
            <div class="provider-info-grid">
                ${provider.city ? `
                    <div class="provider-info-item">
                        <div class="provider-info-label">Location</div>
                        <div class="provider-info-value">${escapeHtml(provider.city || 'N/A')}</div>
                    </div>
                ` : ''}
                ${provider.country ? `
                    <div class="provider-info-item">
                        <div class="provider-info-label">Country</div>
                        <div class="provider-info-value">${escapeHtml(provider.country || 'N/A')}</div>
                    </div>
                ` : ''}
            </div>
        </div>
        <div class="provider-reviews-section">
            <div class="provider-reviews-title">Reviews</div>
            ${reviewsHtml}
        </div>
    `;

    modalBody.innerHTML = html;
}

/**
 * Show provider error message
 */
function showProviderError(message) {
    const modalBody = document.getElementById('modalBody');
    modalBody.innerHTML = `<div class="provider-modal-error">${escapeHtml(message)}</div>`;
}

/**
 * Generate star rating display
 */
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let stars = '★'.repeat(fullStars);
    if (hasHalfStar) stars += '☆';
    stars += '☆'.repeat(5 - Math.ceil(rating));
    return stars;
}

/**
 * Format review date
 */
function formatReviewDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (date.toDateString() === today.toDateString()) {
        return 'Today';
    } else if (date.toDateString() === yesterday.toDateString()) {
        return 'Yesterday';
    } else {
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
    }
}

/**
 * Escape HTML for attribute values
 */
function escapeHtmlForAttribute(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML.replace(/"/g, '&quot;');
}
