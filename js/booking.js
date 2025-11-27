/**
 * Booking Script
 * js/booking.js
 */

document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('booking_date');
    const timeSelect = document.getElementById('booking_time');
    const slotsContainer = document.getElementById('time_slots');
    const bookingForm = document.getElementById('booking_form');
    const submitBtn = document.getElementById('submit_btn');

    // Set min date to today
    const today = new Date();
    dateInput.min = today.toISOString().split('T')[0];

    // When user selects a date, fetch available slots
    dateInput.addEventListener('change', function() {
        if (!this.value) {
            timeSelect.innerHTML = '<option value="">Select a time</option>';
            return;
        }

        const provider_id = document.getElementById('provider_id').value;
        const csrf_token = document.querySelector('input[name="csrf_token"]').value;

        const formData = new FormData();
        formData.append('provider_id', provider_id);
        formData.append('booking_date', this.value);

        // Show loading
        timeSelect.innerHTML = '<option value="">Loading times...</option>';
        timeSelect.disabled = true;

        fetch(window.siteUrl + '/actions/fetch_available_slots_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            timeSelect.disabled = false;
            timeSelect.innerHTML = '<option value="">Select a time</option>';

            if (data.success && data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
            } else {
                timeSelect.innerHTML = '<option value="">No available times</option>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            timeSelect.innerHTML = '<option value="">Error loading times</option>';
            timeSelect.disabled = false;
        });
    });

    // Form submission
    bookingForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const booking_date = dateInput.value;
        const booking_time = timeSelect.value;
        const service_description = document.getElementById('service_description').value;
        const notes = document.getElementById('notes').value;
        const provider_id = document.getElementById('provider_id').value;
        const product_id = document.getElementById('product_id').value;
        const csrf_token = document.querySelector('input[name="csrf_token"]').value;

        // Validate
        if (!booking_date) {
            alert('Please select a date');
            return;
        }
        if (!booking_time) {
            alert('Please select a time');
            return;
        }
        if (!service_description || service_description.length < 10) {
            alert('Please enter a description (at least 10 characters)');
            return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Booking...';

        const formData = new FormData();
        formData.append('provider_id', provider_id);
        formData.append('product_id', product_id);
        formData.append('booking_date', booking_date);
        formData.append('booking_time', booking_time);
        formData.append('service_description', service_description);
        formData.append('notes', notes);
        formData.append('csrf_token', csrf_token);

        fetch(window.siteUrl + '/actions/create_booking_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = window.siteUrl + '/customer/cart.php';
            } else {
                alert('Error: ' + data.message);
                submitBtn.disabled = false;
                submitBtn.textContent = 'Request Booking';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error. Try again.');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Request Booking';
        });
    });
});
