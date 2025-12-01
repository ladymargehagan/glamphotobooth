/**
 * Review JavaScript
 * js/review.js
 * Handles star rating selection and review submission
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeStarRating();
    initializeReviewForm();
});

/**
 * Initialize star rating selection
 */
function initializeStarRating() {
    const stars = document.querySelectorAll('.star-rating .star');
    const ratingInput = document.getElementById('rating');

    if (!stars.length || !ratingInput) return;

    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            updateStarDisplay(rating);
        });

        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            updateStarDisplay(rating, true);
        });
    });

    document.querySelector('.star-rating').addEventListener('mouseleave', function() {
        const currentRating = ratingInput.value || 0;
        updateStarDisplay(currentRating);
    });
}

/**
 * Update star display
 */
function updateStarDisplay(rating, isHover = false) {
    const stars = document.querySelectorAll('.star-rating .star');
    stars.forEach(star => {
        const starRating = parseInt(star.getAttribute('data-rating'));
        if (starRating <= rating) {
            star.classList.add('active');
            if (isHover) {
                star.classList.add('hover');
            } else {
                star.classList.remove('hover');
            }
        } else {
            star.classList.remove('active');
            star.classList.remove('hover');
        }
    });
}

/**
 * Initialize review form submission
 */
function initializeReviewForm() {
    const reviewForm = document.getElementById('reviewForm');
    if (!reviewForm) return;

    reviewForm.addEventListener('submit', function(e) {
        e.preventDefault();
        submitReview();
    });
}

/**
 * Submit review
 */
function submitReview() {
    const form = document.getElementById('reviewForm');
    const ratingInput = document.getElementById('rating');
    const commentInput = document.getElementById('comment');
    const csrfToken = document.querySelector('input[name="csrf_token"]')?.value || window.csrfToken;
    const submitBtn = form.querySelector('button[type="submit"]');

    // Validate rating
    const rating = parseInt(ratingInput.value);
    if (!rating || rating < 1 || rating > 5) {
        showReviewError('Please select a rating');
        return;
    }

    // Validate comment
    const comment = commentInput.value.trim();
    if (comment.length > 500) {
        showReviewError('Comment cannot exceed 500 characters');
        return;
    }

    const formData = new FormData(form);
    formData.append('csrf_token', csrfToken);

    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    hideReviewMessages();

    fetch(window.siteUrl + '/actions/add_review_action.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal immediately
            closeReviewModal();

            // Show SweetAlert success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Success!',
                    text: data.message || 'Review submitted successfully!',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5c9ead'
                }).then(() => {
                    location.reload();
                });
            } else {
                // Fallback if SweetAlert not loaded
                showReviewSuccess(data.message || 'Review submitted successfully!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } else {
            showReviewError(data.message || 'Failed to submit review');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Review';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showReviewError('Error submitting review');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit Review';
    });
}

/**
 * Reset review form
 */
function resetReviewForm() {
    const form = document.getElementById('reviewForm');
    if (form) {
        form.reset();
        document.getElementById('rating').value = '';
        updateStarDisplay(0);
    }
}

/**
 * Show review error message
 */
function showReviewError(message) {
    const errorDiv = document.getElementById('reviewError');
    if (errorDiv) {
        errorDiv.textContent = message;
        errorDiv.classList.add('show');
    }
}

/**
 * Show review success message
 */
function showReviewSuccess(message) {
    const successDiv = document.getElementById('reviewSuccess');
    if (successDiv) {
        successDiv.textContent = message;
        successDiv.classList.add('show');
    }
}

/**
 * Hide review messages
 */
function hideReviewMessages() {
    const errorDiv = document.getElementById('reviewError');
    const successDiv = document.getElementById('reviewSuccess');
    if (errorDiv) errorDiv.classList.remove('show');
    if (successDiv) successDiv.classList.remove('show');
}

/**
 * Open review modal
 */
function openReviewModal(bookingId, providerId) {
    const modal = document.getElementById('reviewModal');
    if (!modal) return;

    document.getElementById('booking_id').value = bookingId;
    document.getElementById('provider_id').value = providerId;

    // Set CSRF token
    const csrfTokenInput = document.getElementById('csrf_token');
    if (csrfTokenInput) {
        csrfTokenInput.value = window.csrfToken || '';
    }

    resetReviewForm();
    hideReviewMessages();

    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

/**
 * Close review modal
 */
function closeReviewModal() {
    const modal = document.getElementById('reviewModal');
    if (!modal) return;

    modal.classList.remove('show');
    document.body.style.overflow = 'auto';
}

/**
 * Close modal when clicking outside
 */
document.addEventListener('click', function(e) {
    const modal = document.getElementById('reviewModal');
    if (modal && e.target === modal) {
        closeReviewModal();
    }
});

/**
 * Close modal with ESC key
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeReviewModal();
    }
});

/**
 * Load and display reviews for a product
 */
function loadProductReviews(providerId, container = null) {
    if (!providerId) return;

    const reviewContainer = container || document.getElementById('productReviews');
    if (!reviewContainer) return;

    fetch(window.siteUrl + '/actions/fetch_reviews_action.php?provider_id=' + providerId + '&limit=5')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayProductReviews(data.reviews, reviewContainer);
        } else {
            reviewContainer.innerHTML = '<p>Unable to load reviews</p>';
        }
    })
    .catch(error => {
        console.error('Error loading reviews:', error);
        reviewContainer.innerHTML = '<p>Error loading reviews</p>';
    });
}

/**
 * Display product reviews
 */
function displayProductReviews(reviews, container) {
    if (!reviews || reviews.length === 0) {
        container.innerHTML = '<p class="no-reviews">No reviews yet</p>';
        return;
    }

    let html = '<div class="reviews-list">';
    reviews.forEach(review => {
        html += `
            <div class="review-item">
                <div class="review-header">
                    <div class="reviewer-info">
                        <span class="reviewer-name">${review.customer_name}</span>
                        <span class="review-date">${review.review_date}</span>
                    </div>
                    <div class="review-rating">${review.rating_stars}</div>
                </div>
                <p class="review-comment">${review.comment}</p>
            </div>
        `;
    });
    html += '</div>';

    container.innerHTML = html;
}
