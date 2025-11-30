<?php
/**
 * Add Review Modal
 * customer/add_review.php
 * Modal form for adding reviews
 */

require_once __DIR__ . '/../settings/core.php';
?>

<style>
    /* Review Modal Styles */
    #reviewModal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    #reviewModal.show {
        display: flex;
    }

    .review-modal-content {
        background: white;
        border-radius: 8px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        animation: slideUp 0.3s ease;
    }

    @keyframes slideUp {
        from {
            transform: translateY(50px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .review-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .review-modal-header h2 {
        margin: 0;
        font-size: 22px;
        color: #333;
    }

    .review-modal-close {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #999;
        padding: 0;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .review-modal-close:hover {
        color: #333;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #333;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .star-rating {
        display: flex;
        gap: 10px;
        margin: 10px 0;
    }

    .star {
        font-size: 36px;
        cursor: pointer;
        color: #ddd;
        transition: all 0.2s ease;
    }

    .star:hover,
    .star.hover {
        color: #ffc107;
        transform: scale(1.1);
    }

    .star.active {
        color: #ffc107;
    }

    .star-rating-label {
        display: inline-block;
        margin-left: 10px;
        color: #666;
        font-size: 14px;
        vertical-align: middle;
    }

    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-family: inherit;
        font-size: 14px;
        resize: vertical;
        min-height: 100px;
    }

    textarea:focus {
        outline: none;
        border-color: #5c9ead;
        box-shadow: 0 0 0 3px rgba(92, 154, 173, 0.1);
    }

    .char-count {
        font-size: 12px;
        color: #999;
        margin-top: 5px;
        text-align: right;
    }

    .review-messages {
        margin-bottom: 15px;
    }

    .review-error,
    .review-success {
        padding: 12px 15px;
        border-radius: 4px;
        margin-bottom: 10px;
        display: none;
        font-size: 14px;
    }

    .review-error.show {
        display: block;
        background-color: #fee;
        color: #c33;
        border: 1px solid #fcc;
    }

    .review-success.show {
        display: block;
        background-color: #efe;
        color: #3c3;
        border: 1px solid #cfc;
    }

    .form-buttons {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }

    .btn-submit,
    .btn-cancel {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-submit {
        background-color: #5c9ead;
        color: white;
    }

    .btn-submit:hover:not(:disabled) {
        background-color: #4a7a8e;
    }

    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }

    .btn-cancel {
        background-color: #ddd;
        color: #333;
    }

    .btn-cancel:hover {
        background-color: #ccc;
    }

    .hidden-inputs {
        display: none;
    }

    /* Responsive */
    @media (max-width: 600px) {
        .review-modal-content {
            padding: 20px;
        }

        .review-modal-header h2 {
            font-size: 18px;
        }

        .star {
            font-size: 28px;
        }
    }
</style>

<!-- Review Modal -->
<div id="reviewModal">
    <div class="review-modal-content">
        <div class="review-modal-header">
            <h2>Leave a Review</h2>
            <button class="review-modal-close" onclick="closeReviewModal()">×</button>
        </div>

        <div class="review-messages">
            <div id="reviewError" class="review-error"></div>
            <div id="reviewSuccess" class="review-success"></div>
        </div>

        <form id="reviewForm">
            <div class="hidden-inputs">
                <input type="hidden" name="booking_id" id="booking_id" value="">
                <input type="hidden" name="order_id" id="order_id" value="">
                <input type="hidden" name="provider_id" id="provider_id" value="">
                <input type="hidden" name="rating" id="rating" value="">
            </div>

            <div class="form-group">
                <label>Your Rating</label>
                <div class="star-rating">
                    <span class="star" data-rating="1">★</span>
                    <span class="star" data-rating="2">★</span>
                    <span class="star" data-rating="3">★</span>
                    <span class="star" data-rating="4">★</span>
                    <span class="star" data-rating="5">★</span>
                </div>
                <span class="star-rating-label" id="ratingLabel">Click to rate</span>
            </div>

            <div class="form-group">
                <label for="comment">Your Comment (Optional)</label>
                <textarea
                    id="comment"
                    name="comment"
                    placeholder="Share your experience with this photographer..."
                    maxlength="500"
                ></textarea>
                <div class="char-count">
                    <span id="charCount">0</span>/500 characters
                </div>
            </div>

            <div class="form-buttons">
                <button type="button" class="btn-cancel" onclick="closeReviewModal()">Cancel</button>
                <button type="submit" class="btn-submit">Submit Review</button>
            </div>
        </form>
    </div>
</div>

<script>
// Update rating label when stars are selected
document.addEventListener('DOMContentLoaded', function() {
    const ratingInput = document.getElementById('rating');
    const ratingLabel = document.getElementById('ratingLabel');
    const commentInput = document.getElementById('comment');
    const charCount = document.getElementById('charCount');

    if (ratingInput) {
        ratingInput.addEventListener('change', function() {
            const rating = parseInt(this.value);
            if (rating >= 1 && rating <= 5) {
                const labels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
                ratingLabel.textContent = labels[rating - 1];
            }
        });
    }

    if (commentInput && charCount) {
        commentInput.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    }
});
</script>
