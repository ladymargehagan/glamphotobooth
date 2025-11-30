/**
 * Booking Script
 * js/booking.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('booking_form');
    const bookingDateInput = document.getElementById('booking_date');
    const bookingTimeSelect = document.getElementById('booking_time');
    const serviceDescriptionInput = document.getElementById('service_description');
    const notesInput = document.getElementById('notes');
    const submitBtn = document.getElementById('submit_btn');

    if (!bookingForm || !bookingDateInput || !bookingTimeSelect || !serviceDescriptionInput || !submitBtn) {
        console.error('Form elements not found');
        return;
    }

    // Set minimum date to today
    const today = new Date();
    bookingDateInput.min = today.toISOString().split('T')[0];

    // Fetch available slots when date changes
    bookingDateInput.addEventListener('change', function() {
        if (!this.value) {
            bookingTimeSelect.innerHTML = '<option value="">Select a date first</option>';
            return;
        }

        const provider_id = document.getElementById('provider_id').value;

        const formData = new FormData();
        formData.append('provider_id', provider_id);
        formData.append('booking_date', this.value);

        bookingTimeSelect.innerHTML = '<option value="">Loading times...</option>';
        bookingTimeSelect.disabled = true;

        fetch(window.siteUrl + '/actions/fetch_available_slots_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            bookingTimeSelect.disabled = false;
            bookingTimeSelect.innerHTML = '<option value="">Select a time</option>';

            if (data.success && data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    bookingTimeSelect.appendChild(option);
                });
            } else {
                bookingTimeSelect.innerHTML = '<option value="">No available times</option>';
            }
        })
        .catch(error => {
            console.error('Error fetching slots:', error);
            bookingTimeSelect.innerHTML = '<option value="">Error loading times</option>';
            bookingTimeSelect.disabled = false;
        });
    });

    // Handle form submission
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate
        if (!bookingDateInput.value) {
            showErrorAlert('Validation Error', 'Please select a booking date');
            return;
        }

        if (!bookingTimeSelect.value) {
            showErrorAlert('Validation Error', 'Please select a time');
            return;
        }

        if (!serviceDescriptionInput.value || serviceDescriptionInput.value.trim().length < 10) {
            showErrorAlert('Validation Error', 'Service description must be at least 10 characters');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Booking...';

        const formData = new FormData();
        formData.append('provider_id', document.getElementById('provider_id').value);
        formData.append('product_id', document.getElementById('product_id').value);
        formData.append('booking_date', bookingDateInput.value);
        formData.append('booking_time', bookingTimeSelect.value);
        formData.append('service_description', serviceDescriptionInput.value);
        formData.append('notes', notesInput.value || '');
        formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);

        fetch(window.siteUrl + '/actions/create_booking_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlertAndRedirect('Success', 'Booking created! Proceeding to cart...', window.siteUrl + '/customer/cart.php');
            } else {
                showErrorAlert('Booking Error', data.message || 'Failed to create booking');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Request Booking';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorAlert('Network Error', 'Network error. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Request Booking';
        });
    });
});
