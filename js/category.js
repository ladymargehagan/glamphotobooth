/**
 * Category Management Script
 * js/category.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const addBtn = document.getElementById('addCategoryBtn');
    const closeBtn = document.getElementById('closeModalBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const table = document.getElementById('categoriesTable');
    const modalTitle = document.getElementById('modalTitle');
    const submitBtn = document.getElementById('submitBtn');
    const catIdInput = document.getElementById('catId');
    const catNameInput = document.getElementById('catName');

    // Load categories on page load
    loadCategories();

    // Event listeners
    addBtn.addEventListener('click', openAddModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    form.addEventListener('submit', handleSubmit);

    // Close modal when clicking overlay
    document.getElementById('categoryModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    /**
     * Load all categories
     */
    function loadCategories() {
        const formData = new FormData();
        formData.append('cat_id', 0);

        fetch('../actions/fetch_category_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                renderCategories(data.data);
            } else {
                table.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">No categories found</td></tr>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            table.innerHTML = '<tr><td colspan="5" style="text-align: center; padding: var(--spacing-xl); color: #d32f2f;">Error loading categories</td></tr>';
        });
    }

    /**
     * Render categories in table
     */
    function renderCategories(categories) {
        table.innerHTML = '';

        categories.forEach((category, index) => {
            const row = document.createElement('tr');
            const createdDate = new Date(category.created_at).toLocaleDateString('en-US');

            row.innerHTML = `
                <td>${index + 1}</td>
                <td><strong>${escapeHtml(category.cat_name)}</strong></td>
                <td><span class="status-badge status-active">0</span></td>
                <td>${createdDate}</td>
                <td style="text-align: right;">
                    <button type="button" class="action-btn edit-btn" onclick="editCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">Edit</button>
                    <button type="button" class="action-btn delete-btn" onclick="deleteCategory(${category.cat_id}, '${escapeHtml(category.cat_name)}')">Delete</button>
                </td>
            `;

            table.appendChild(row);
        });
    }

    /**
     * Open add modal
     */
    function openAddModal() {
        resetForm();
        modalTitle.textContent = 'Add Category';
        submitBtn.textContent = 'Add Category';
        catIdInput.value = '';
        modal.classList.remove('hidden');
        modal.classList.add('active');
        catNameInput.focus();
    }

    /**
     * Close modal
     */
    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('active');
        resetForm();
    }

    /**
     * Reset form
     */
    function resetForm() {
        form.reset();
        catIdInput.value = '';
        document.getElementById('catNameError').textContent = '';
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');
    }

    /**
     * Handle form submission
     */
    function handleSubmit(e) {
        e.preventDefault();

        // Clear errors
        document.getElementById('catNameError').textContent = '';
        document.getElementById('successMessage').classList.add('hidden');
        document.getElementById('errorMessage').classList.add('hidden');

        const catName = catNameInput.value.trim();
        const catId = catIdInput.value;

        if (!catName) {
            document.getElementById('catNameError').textContent = 'Category name is required';
            return;
        }

        // Disable submit button
        submitBtn.disabled = true;
        const originalText = submitBtn.textContent;
        submitBtn.textContent = catId ? 'Updating...' : 'Adding...';

        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('cat_name', catName);

        let url = '../actions/add_category_action.php';
        if (catId) {
            formData.append('cat_id', catId);
            url = '../actions/update_category_action.php';
        }

        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess(data.message || 'Operation completed successfully');
                setTimeout(() => {
                    closeModal();
                    loadCategories();
                }, 1500);
            } else {
                showError(data.message || 'Operation failed');
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
        msg.classList.remove('hidden');
    }

    /**
     * Show error message
     */
    function showError(message) {
        const msg = document.getElementById('errorMessage');
        document.getElementById('errorText').textContent = message;
        msg.classList.remove('hidden');
    }

    /**
     * Escape HTML special characters
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Global functions for inline onclick handlers
    window.editCategory = function(catId, catName) {
        modalTitle.textContent = 'Edit Category';
        submitBtn.textContent = 'Update Category';
        catIdInput.value = catId;
        catNameInput.value = catName;
        modal.classList.remove('hidden');
        modal.classList.add('active');
        catNameInput.focus();
    };

    window.deleteCategory = function(catId, catName) {
        if (!confirm(`Are you sure you want to delete "${catName}"? This action cannot be undone.`)) {
            return;
        }

        const formData = new FormData();
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
        formData.append('cat_id', catId);

        fetch('../actions/delete_category_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadCategories();
                // Could show a toast here instead
                alert(data.message || 'Category deleted successfully');
            } else {
                alert(data.message || 'Failed to delete category');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Please try again.');
        });
    };
});
