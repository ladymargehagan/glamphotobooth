/**
 * Shop Page Script
 * js/shop.js
 */

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
        const selectedCategory = document.querySelector('input[name="category"]:checked').value;
        const selectedType = document.querySelector('input[name="product_type"]:checked').value;

        const formData = new FormData();
        formData.append('cat_id', selectedCategory);
        formData.append('product_type', selectedType);
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
                <a href="product_details.php?id=${product.product_id}" class="product-card-link">
                    <div class="product-image">
                        ${imageHtml}
                    </div>
                    <div class="product-info">
                        <span class="product-category">Category</span>
                        <div class="product-title">${productTitle}</div>
                        <div class="product-type">${product.product_type || 'N/A'}</div>
                        <p class="product-description">${escapeHtml(description)}</p>
                        <div class="product-price">₵${productPrice.toFixed(2)}</div>
                        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #eee; font-size: 0.85rem;">
                            <a href="provider_profile.php?id=${product.provider_id}" style="color: var(--primary); text-decoration: none; font-weight: 500;">View Provider Profile →</a>
                        </div>
                    </div>
                </a>
                <button class="product-card-btn-add-to-cart" onclick="addToCart(${product.product_id}, '${productTitle}', ${productPrice}); event.stopPropagation();">Add to Cart</button>
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
