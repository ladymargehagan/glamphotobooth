# Booking System Documentation

## Overview

The PhotoMarket booking system allows customers to request photography services from service providers (photographers) with specific dates and times. Providers can then accept, reject, or mark bookings as complete.

## Components

### Backend Files

#### Classes
- **`classes/booking_class.php`** - Core booking management with CRUD operations
  - `create_booking()` - Create new booking request
  - `get_booking_by_id()` - Retrieve single booking with related data
  - `get_customer_bookings()` - Get all customer bookings
  - `get_provider_bookings()` - Get all provider bookings
  - `update_booking_status()` - Update booking status and provider response
  - `get_available_slots()` - Get available time slots for a date
  - `get_provider_stats()` - Get booking statistics (pending, confirmed, completed, total)

#### Controllers
- **`controllers/booking_controller.php`** - Validation and business logic
  - `create_booking_ctr()` - Validates booking data before creation
  - `update_booking_status_ctr()` - Validates status updates
  - `get_available_slots_ctr()` - Validates and returns available slots

#### Actions (API Endpoints)
- **`actions/create_booking_action.php`** - POST handler for creating bookings
- **`actions/update_booking_status_action.php`** - POST handler for status updates
- **`actions/fetch_bookings_action.php`** - GET/POST handler for retrieving bookings
- **`actions/fetch_available_slots_action.php`** - GET/POST handler for available time slots
- **`actions/accept_booking_action.php`** - POST handler for accepting bookings
- **`actions/reject_booking_action.php`** - POST handler for rejecting bookings

### Frontend Files

#### Customer Pages
- **`customer/booking.php`** - Booking interface with date/time picker
  - Calendar date input with min date = today
  - Dynamic time slots (09:00-18:00) loaded via AJAX
  - Service description textarea (required, min 10 chars)
  - Optional notes field
  - Provider sidebar showing rating, reviews, description

- **`customer/my_bookings.php`** - Customer booking history
  - List of all customer bookings
  - Status badges (pending/confirmed/completed/cancelled)
  - Booking details: date, time, service description, notes
  - Provider response message display
  - Cancel button for pending bookings

- **`customer/dashboard.php`** - Updated with booking statistics
  - Stats cards: total bookings, pending, confirmed
  - Recent bookings (up to 3) with status and details
  - Link to view all bookings

#### Provider Pages
- **`customer/manage_bookings.php`** - Provider booking management
  - Requires user_role == 2 (photographer)
  - Statistics cards: pending, confirmed, completed bookings
  - List of all provider bookings
  - Accept button with optional message textarea (for pending)
  - Reject button with optional reason textarea (for pending)
  - Mark Complete button (for confirmed)
  - Modal dialogs for accept/reject actions

- **`photographer/dashboard.php`** - Updated with booking statistics
  - Stats cards: pending, confirmed, completed, total bookings
  - Recent booking requests (up to 3)
  - Customer info, date/time, service description
  - Link to view all booking requests

#### JavaScript
- **`js/booking.js`** - Client-side booking interactions
  - Date input min date validation
  - Fetch available time slots on date change
  - Time slot selection with visual feedback
  - Form validation (date, time, service description)
  - Submit booking via fetch API
  - Error and success message display

## Database Schema

The system uses the `pb_bookings` table with the following structure:

```sql
ALTER TABLE pb_bookings
ADD COLUMN IF NOT EXISTS provider_id INT,
ADD COLUMN IF NOT EXISTS service_description TEXT,
ADD COLUMN IF NOT EXISTS notes TEXT,
ADD COLUMN IF NOT EXISTS response_note TEXT,
ADD COLUMN IF NOT EXISTS contact VARCHAR(20);

-- Status values: pending, confirmed, rejected, completed, cancelled
```

**Note:** Run the migration script to add these columns if they don't exist.

## Booking Workflow

### Customer Side
1. Customer clicks "Book Services" or "Browse Photographers"
2. Selects a photographer and clicks "Book"
3. Fills out booking form:
   - Selects booking date (min = today)
   - Selects available time slot
   - Enters service description
   - Optionally adds notes
4. Submits booking request
5. Booking status becomes "pending"
6. Provider can accept, reject, or complete the booking
7. Customer can view booking status in "My Bookings"
8. Customer can cancel pending bookings

### Provider Side
1. Provider receives booking request notification
2. Views booking in "Manage Bookings" page
3. Can:
   - **Accept**: Sets status to "confirmed", optionally adds message
   - **Reject**: Sets status to "rejected", optionally adds reason
   - **Mark Complete**: Sets status to "completed" (only if confirmed)
4. Provider dashboard shows booking statistics and recent requests

## Status Workflow

```
pending → confirmed → completed
   ↓
rejected

cancelled (customer cancellation of pending booking)
```

## Available Time Slots

The system generates hourly time slots from 09:00 to 18:00 (24-hour format).

Slots are marked as unavailable if they have existing:
- Confirmed bookings
- Pending bookings (conservative approach)

Query filters:
```sql
WHERE provider_id = ?
  AND booking_date = ?
  AND status IN ('pending', 'confirmed')
```

## API Endpoints

### Create Booking
**POST** `/actions/create_booking_action.php`

Parameters:
- `provider_id` (required)
- `booking_date` (required, YYYY-MM-DD)
- `booking_time` (required, HH:MM)
- `service_description` (required, min 10 chars)
- `notes` (optional)
- `csrf_token` (required)

Response:
```json
{
  "success": true,
  "booking_id": 123
}
```

### Update Booking Status
**POST** `/actions/update_booking_status_action.php`

Parameters:
- `booking_id` (required)
- `status` (required: pending, confirmed, rejected, completed, cancelled)
- `response_note` (optional)
- `csrf_token` (required)

Response:
```json
{
  "success": true,
  "message": "Booking status updated"
}
```

### Fetch Available Slots
**POST** `/actions/fetch_available_slots_action.php`

Parameters:
- `provider_id` (required)
- `booking_date` (required, YYYY-MM-DD)
- `csrf_token` (required)

Response:
```json
{
  "success": true,
  "slots": ["09:00", "10:00", "11:00", ...]
}
```

### Fetch Bookings
**GET/POST** `/actions/fetch_bookings_action.php`

Parameters:
- `type` (required: "customer" or "provider")
- `csrf_token` (required)

Response:
```json
{
  "success": true,
  "bookings": [...]
}
```

## Security Features

1. **CSRF Protection**: All POST requests require valid csrf_token
2. **Authentication**: All endpoints require user to be logged in
3. **Authorization**:
   - Providers can only manage their own bookings
   - Customers can only view their own bookings
4. **Data Validation**:
   - Service description minimum 10 characters
   - Booking dates must be in future
   - Provider ID validation
5. **Input Sanitization**:
   - All user inputs are escaped using prepared statements
   - htmlspecialchars() used for output

## Installation

### 1. Run Database Migration

Execute the migration script to add required columns to pb_bookings:

```sql
-- MySQL
mysql -u [user] -p [database] < db/booking_migration.sql

-- Or via PHP (if supported by hosting)
// Copy and paste db/booking_migration.sql into your database admin tool
```

### 2. Verify Files

Check that all files are created:
- ✓ `classes/booking_class.php`
- ✓ `controllers/booking_controller.php`
- ✓ `actions/create_booking_action.php`
- ✓ `actions/update_booking_status_action.php`
- ✓ `actions/fetch_bookings_action.php`
- ✓ `actions/fetch_available_slots_action.php`
- ✓ `actions/accept_booking_action.php`
- ✓ `actions/reject_booking_action.php`
- ✓ `customer/booking.php`
- ✓ `customer/my_bookings.php`
- ✓ `customer/manage_bookings.php`
- ✓ `photographer/dashboard.php`
- ✓ `js/booking.js`

### 3. Update Dashboards

Customer and photographer dashboards are updated to show:
- Booking statistics
- Recent bookings with quick view
- Links to manage bookings

## Testing

### Test Scenario 1: Create Booking
1. Login as customer (role 4)
2. Navigate to shop.php
3. Select photographer and click "Book"
4. Select date, time, enter service description
5. Submit booking
6. Verify status is "pending" in my_bookings.php

### Test Scenario 2: Accept Booking
1. Login as photographer (role 2)
2. Navigate to manage_bookings.php
3. Click "Accept" on pending booking
4. Optionally add message
5. Verify status changes to "confirmed"

### Test Scenario 3: Complete Booking
1. Login as photographer (role 2)
2. In manage_bookings.php, find confirmed booking
3. Click "Mark Complete"
4. Verify status changes to "completed"

### Test Scenario 4: Reject Booking
1. Login as photographer (role 2)
2. Click "Reject" on pending booking
3. Optionally add reason
4. Verify status changes to "rejected"

### Test Scenario 5: Customer Cancellation
1. Login as customer (role 4)
2. In my_bookings.php, find pending booking
3. Click "Cancel Request"
4. Confirm cancellation
5. Verify status changes to "cancelled"

## Future Enhancements

1. **Email Notifications**
   - Booking request received
   - Booking accepted/rejected/completed
   - Reminder before scheduled booking

2. **Review System**
   - Rate completed bookings
   - Leave comments
   - Update provider rating

3. **Calendar Integration**
   - iCalendar export for customers
   - Google Calendar sync for providers

4. **Availability Management**
   - Providers set working hours
   - Bulk date blocking for vacations
   - Custom time slot durations

5. **Payment Integration**
   - Deposit/full payment at booking
   - Refund on cancellation
   - Payout to provider on completion

## Troubleshooting

### "Class not found" Error
**Cause**: Database table missing columns
**Solution**: Run the migration script from db/booking_migration.sql

### "Invalid provider" Error
**Cause**: Provider profile incomplete or doesn't exist
**Solution**: Complete provider profile in photographer dashboard first

### No Time Slots Available
**Cause**: All slots are booked or provider has no availability
**Solution**: Select different date or check provider's working hours

### Status Update Fails
**Cause**: User is not the provider for that booking
**Solution**: Verify you're logged in as the correct provider

## Support

For issues or questions about the booking system, check:
1. Database migration status
2. User authentication and roles
3. CSRF token validation
4. Browser console for JavaScript errors
5. Server error logs for PHP exceptions
