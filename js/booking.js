/**
 * Booking Script
 * js/booking.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const bookingForm = document.getElementById('bookingForm');
    const bookingDateInput = document.getElementById('booking_date');
    const timeSlotsContainer = document.getElementById('timeSlots');
    const bookingTimeInput = document.getElementById('booking_time');
    const serviceDescriptionInput = document.getElementById('service_description');
    const submitBtn = document.getElementById('submitBtn');

    // Set minimum date to today
    const today = new Date();
    const minDate = today.toISOString().split('T')[0];
    bookingDateInput.setAttribute('min', minDate);

    // Fetch available slots when date changes
    bookingDateInput.addEventListener('change', function() {
        if (this.value) {
            fetchAvailableSlots(this.value);
        }
    });

    // Handle time slot selection
    function handleTimeSlotClick(event) {
        if (event.target.classList.contains('time-slot') && !event.target.classList.contains('disabled')) {
            // Remove previous selection
            document.querySelectorAll('.time-slot.selected').forEach(el => {
                el.classList.remove('selected');
            });

            // Add selection to clicked slot
            event.target.classList.add('selected');
            bookingTimeInput.value = event.target.dataset.time;
        }
    }

    // Fetch available time slots
    function fetchAvailableSlots(bookingDate) {
        const providerId = document.getElementById('providerId').value;
        const csrfToken = document.querySelector('input[name="csrf_token"]').value;

        const formData = new FormData();
        formData.append('provider_id', providerId);
        formData.append('booking_date', bookingDate);
        formData.append('csrf_token', csrfToken);

        timeSlotsContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">Loading time slots...</p>';

        fetch(window.siteUrl + '/actions/fetch_available_slots_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.slots && data.slots.length > 0) {
                timeSlotsContainer.innerHTML = '';
                data.slots.forEach(time => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'time-slot';
                    button.textContent = time;
                    button.dataset.time = time;
                    button.addEventListener('click', handleTimeSlotClick);
                    timeSlotsContainer.appendChild(button);
                });
            } else {
                timeSlotsContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: var(--text-secondary);">No available slots for this date. Please choose another date.</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            timeSlotsContainer.innerHTML = '<p style="grid-column: 1/-1; text-align: center; color: #c62828;">Error loading time slots</p>';
        });
    }

    // Handle form submission
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate
        if (!bookingDateInput.value) {
            showError('Please select a booking date');
            return;
        }

        if (!bookingTimeInput.value) {
            showError('Please select a time slot');
            return;
        }

        if (!serviceDescriptionInput.value || serviceDescriptionInput.value.length < 10) {
            showError('Service description must be at least 10 characters');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Requesting...';

        const formData = new FormData(this);

        fetch(window.siteUrl + '/actions/create_booking_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('Booking request submitted successfully! The provider will review and respond to your request.');
                setTimeout(() => {
                    window.location.href = window.siteUrl + '/customer/my_bookings.php';
                }, 2000);
            } else {
                showError(data.message || 'Failed to submit booking request');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Request Booking';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network error. Please try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Request Booking';
        });
    });

    function showError(message) {
        const errorMsg = document.getElementById('errorMessage');
        errorMsg.textContent = message;
        errorMsg.classList.add('show');
        setTimeout(() => {
            errorMsg.classList.remove('show');
        }, 5000);
    }

    function showSuccess(message) {
        const successMsg = document.getElementById('successMessage');
        successMsg.textContent = message;
        successMsg.classList.add('show');
    }
});
