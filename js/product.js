/**
 * Product Management Script
 * js/product.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('productForm');

    if (form) {
        // Add product form
        setupAddProductForm();
    } else {
        // Manage products page
        setupManageProducts();
    }
});

/**
 * Setup add product form
 */
function setupAddProductForm() {
    const form = document.getElementById('productForm');
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const categoryInput = document.getElementById('category');
    const priceInput = document.getElementById('price');
    const productTypeInput = document.getElementById('productType');
    const submitBtn = document.getElementById('submitBtn');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imageInput = document.getElementById('imageInput');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImageBtn');

    // Image upload handlers
    imageUploadArea.addEventListener('click', () => imageInput.click());
    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('drag-over');
    });
    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('drag-over');
    });
    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('drag-over');
        if (e.dataTransfer.files.length > 0) {
            imageInput.files = e.dataTransfer.files;
            handleImageSelect();
        }
    });

    imageInput.addEventListener('change', handleImageSelect);
    removeImageBtn.addEventListener('click', (e) => {
        e.preventDefault();
        imageInput.value = '';
        imagePreview.style.display = 'none';
    });

    // Form validation
    form.addEventListener('submit', handleProductSubmit);
    titleInput.addEventListener('blur', validateTitle);
    descriptionInput.addEventListener('blur', validateDescription);
    categoryInput.addEventListener('change', validateCategory);
    priceInput.addEventListener('blur', validatePrice);
    productTypeInput.addEventListener('change', validateProductType);

    function handleImageSelect() {
        const file = imageInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function validateTitle() {
        const value = titleInput.value.trim();
        const errorEl = document.getElementById('titleError');
        if (!value) {
            errorEl.textContent = 'Title is required';
            return false;
        }
        if (value.length < 3 || value.length > 255) {
            errorEl.textContent = 'Title must be between 3 and 255 characters';
            return false;
        }
        errorEl.textContent = '';
        return true;
    }

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

    function validateCategory() {
        const value = categoryInput.value;
        const errorEl = document.getElementById('categoryError');
        if (!value) {
            errorEl.textContent = 'Please select a category';
            return false;
        }
        errorEl.textContent = '';
        return true;
    }

    function validatePrice() {
        const value = parseFloat(priceInput.value);
        const errorEl = document.getElementById('priceError');
        if (!value || isNaN(value)) {
            errorEl.textContent = 'Price is required';
            return false;
        }
        if (value <= 0) {
            errorEl.textContent = 'Price must be greater than 0';
            return false;
        }
        errorEl.textContent = '';
        return true;
    }

    function validateProductType() {
        const value = productTypeInput.value;
        const errorEl = document.getElementById('productTypeError');
        if (!value) {
            errorEl.textContent = 'Please select a product type';
            return false;
        }
        errorEl.textContent = '';
        return true;
    }

    function handleProductSubmit(e) {
        e.preventDefault();

        document.getElementById('successMessage').classList.remove('show');
        document.getElementById('errorMessage').classList.remove('show');

        // Validate all fields
        const titleValid = validateTitle();
        const descriptionValid = validateDescription();
        const categoryValid = validateCategory();
        const priceValid = validatePrice();
        const typeValid = validateProductType();

        if (!titleValid || !descriptionValid || !categoryValid || !priceValid || !typeValid) {
            return;
        }

        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Creating...';

        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('provider_id', document.querySelector('input[name="provider_id"]').value);
        formData.append('cat_id', categoryInput.value);
        formData.append('title', titleInput.value.trim());
        formData.append('description', descriptionInput.value.trim());
        formData.append('price', priceInput.value);
        formData.append('product_type', productTypeInput.value);
        formData.append('keywords', document.getElementById('keywords').value.trim());

        fetch('../actions/add_product_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Product created successfully');
                // Upload image if selected
                if (imageInput.files.length > 0) {
                    uploadProductImage(data.product_id);
                } else {
                    setTimeout(() => {
                        window.location.href = '../vendor/manage_products.php';
                    }, 1500);
                }
            } else {
                showError(data.message || 'Failed to create product');
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

    function uploadProductImage(productId) {
        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('product_id', productId);
        formData.append('image', imageInput.files[0]);

        fetch('../actions/upload_product_image_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Image uploaded successfully');
                setTimeout(() => {
                    window.location.href = '../vendor/manage_products.php';
                }, 1500);
            } else {
                // Product created but image upload failed
                showError('Warning: Image upload failed - ' + (data.message || 'Unknown error'));
                console.error('Image upload error:', data);
                setTimeout(() => {
                    window.location.href = '../vendor/manage_products.php';
                }, 2000);
            }
        })
        .catch((error) => {
            // Silently fail and redirect anyway
            console.error('Image upload network error:', error);
            setTimeout(() => {
                window.location.href = '../vendor/manage_products.php';
            }, 1500);
        });
    }

    function showSuccess(message) {
        const msg = document.getElementById('successMessage');
        document.getElementById('successText').textContent = message;
        msg.classList.add('show');
    }

    function showError(message) {
        const msg = document.getElementById('errorMessage');
        document.getElementById('errorText').textContent = message;
        msg.classList.add('show');
    }
}

/**
 * Setup manage products page
 */
function setupManageProducts() {
    // Delete product handler
    window.deleteProduct = function(productId) {
        if (!confirm('Are you sure you want to delete this product?')) {
            return;
        }

        const csrfToken = document.querySelector('input[name="csrf_token"]');
        if (!csrfToken) {
            // Fetch CSRF token from a hidden input or create form
            showError('Security token not found');
            return;
        }

        const formData = new FormData();
        formData.append('csrf_token', csrfToken.value);
        formData.append('product_id', productId);

        fetch('../actions/delete_product_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess('Product deleted successfully');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                console.error('Delete failed:', data);
                showError(data.message || 'Failed to delete product');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            showError('Error: ' + error.message);
        });
    };

    function showSuccess(message) {
        const msg = document.getElementById('successMessage');
        document.getElementById('successText').textContent = message;
        msg.classList.add('show');
    }

    function showError(message) {
        const msg = document.getElementById('errorMessage');
        document.getElementById('errorText').textContent = message;
        msg.classList.add('show');
    }
}
