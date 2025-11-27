/**
 * Booking Script
 * js/booking.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Wait a bit to ensure DOM is fully ready
    setTimeout(function() {
        const bookingForm = document.getElementById('bookingForm');
        const bookingDateInput = document.getElementById('booking_date');
        const timeSlotsContainer = document.getElementById('timeSlots');
        const bookingTimeInput = document.getElementById('booking_time');
        const serviceDescriptionInput = document.getElementById('service_description');
        const submitBtn = document.getElementById('submitBtn');

        // Check if required elements exist
        if (!bookingForm || !bookingDateInput || !timeSlotsContainer || !bookingTimeInput || !serviceDescriptionInput || !submitBtn) {
            console.error('Booking form elements not found:', {
                bookingForm: !!bookingForm,
                bookingDateInput: !!bookingDateInput,
                timeSlotsContainer: !!timeSlotsContainer,
                bookingTimeInput: !!bookingTimeInput,
                serviceDescriptionInput: !!serviceDescriptionInput,
                submitBtn: !!submitBtn
            });
            return;
        }

        // Set minimum date to today
        const today = new Date();
        const minDate = today.toISOString().split('T')[0];
        bookingDateInput.setAttribute('min', minDate);

        // Fetch available time slots
        function fetchAvailableSlots(bookingDate) {
            const providerId = document.getElementById('providerId');
            const csrfToken = document.querySelector('input[name="csrf_token"]');
            
            if (!providerId || !csrfToken) {
                console.error('Missing required form fields');
                return;
            }

            const formData = new FormData();
            formData.append('provider_id', providerId.value);
            formData.append('booking_date', bookingDate);
            formData.append('csrf_token', csrfToken.value);

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

        // Fetch available slots when date changes
        bookingDateInput.addEventListener('change', function() {
            if (this.value) {
                // Clear previous time selection
                bookingTimeInput.value = '';
                document.querySelectorAll('.time-slot.selected').forEach(el => {
                    el.classList.remove('selected');
                });
                fetchAvailableSlots(this.value);
            }
        });

        // If date is already selected on page load, fetch slots
        if (bookingDateInput.value) {
            fetchAvailableSlots(bookingDateInput.value);
        }

        // Handle time slot selection (using event delegation)
        timeSlotsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('time-slot') && !event.target.classList.contains('disabled')) {
                // Remove previous selection
                document.querySelectorAll('.time-slot.selected').forEach(el => {
                    el.classList.remove('selected');
                });

                // Add selection to clicked slot
                event.target.classList.add('selected');
                const selectedTime = event.target.dataset.time || event.target.textContent.trim();
                bookingTimeInput.value = selectedTime;
                
                // Clear any error messages when time is selected
                const errorMsg = document.getElementById('errorMessage');
                if (errorMsg) {
                    errorMsg.classList.remove('show');
                }
            }
        });

        // Handle form submission
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Validate
            if (!bookingDateInput.value) {
                showError('Please select a booking date');
                bookingDateInput.focus();
                return;
            }

            if (!bookingTimeInput.value) {
                showError('Please select a time slot');
                return;
            }

            if (!serviceDescriptionInput.value || serviceDescriptionInput.value.trim().length < 10) {
                showError('Service description must be at least 10 characters');
                serviceDescriptionInput.focus();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Requesting...';

            const formData = new FormData(this);
            
            // Debug: Log form data to ensure values are being sent
            console.log('Submitting booking:', {
                booking_date: bookingDateInput.value,
                booking_time: bookingTimeInput.value,
                provider_id: document.getElementById('providerId').value,
                service_description: serviceDescriptionInput.value
            });

            fetch(window.siteUrl + '/actions/create_booking_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Booking submitted! Proceeding to checkout...');
                    setTimeout(() => {
                        window.location.href = window.siteUrl + '/customer/cart.php';
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
            if (errorMsg) {
                errorMsg.textContent = message;
                errorMsg.classList.add('show');
                setTimeout(() => {
                    errorMsg.classList.remove('show');
                }, 5000);
            }
        }

        function showSuccess(message) {
            const successMsg = document.getElementById('successMessage');
            if (successMsg) {
                successMsg.textContent = message;
                successMsg.classList.add('show');
            }
        }
    }, 100); // Small delay to ensure DOM is ready
});
