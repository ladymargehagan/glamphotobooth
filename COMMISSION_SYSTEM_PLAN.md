# Commission & Payment Request System - Implementation Plan

## Overview
Platform takes 5% commission on all transactions. Vendors and photographers can request payouts for their earnings (95% of their sales).

---

## Database Tables

### 1. `pb_commissions` - Track Commission Per Transaction
Tracks commission for each order/booking transaction.

### 2. `pb_payment_requests` - Payment Request System
Tracks payout requests from vendors/photographers.

---

## Implementation Flow

### Phase 1: Commission Tracking
1. **When Order is Paid:**
   - Calculate 5% commission from `total_amount`
   - Calculate vendor earnings (95% of their product sales)
   - Store commission record in `pb_commissions`
   - Link commission to order and provider

2. **When Booking is Completed:**
   - Calculate 5% commission from `total_price`
   - Calculate photographer earnings (95% of booking price)
   - Store commission record in `pb_commissions`
   - Link commission to booking and provider

### Phase 2: Earnings Calculation
- **Vendor Earnings** = Sum of (95% of their product sales from paid orders)
- **Photographer Earnings** = Sum of (95% of completed booking prices)
- **Platform Commission** = Sum of (5% of all transactions)

### Phase 3: Payment Request System
1. **Vendor/Photographer Side:**
   - View available earnings (not yet requested)
   - Create payment request with:
     - Payment method (bank_transfer, mobile_money, etc.)
     - Payment details (account number, phone number, etc.)
     - Requested amount (up to available earnings)
   - View request history

2. **Admin Side:**
   - View all payment requests
   - Approve/reject requests
   - Mark requests as paid
   - View platform commission totals

### Phase 4: Admin Dashboard Updates
- Show total platform commission (5% of all transactions)
- Show pending payment requests count
- Show recent payment requests
- Commission breakdown by month

---

## Key Features

### For Vendors/Photographers:
- View total earnings
- View available balance (earnings not yet requested)
- Request payout with payment details
- View request status and history

### For Admin:
- View platform commission (5% earnings)
- Manage payment requests (approve/reject/pay)
- View commission analytics
- Export commission reports

---

## Commission Calculation Logic

```
Order Total: ₵1000
Platform Commission (5%): ₵50
Vendor Earnings (95%): ₵950

Booking Total: ₵500
Platform Commission (5%): ₵25
Photographer Earnings (95%): ₵475
```

---

## Payment Request Workflow

1. Vendor/Photographer creates request
2. Request status: `pending`
3. Admin reviews and approves
4. Request status: `approved`
5. Admin processes payment
6. Request status: `paid`
7. Earnings deducted from available balance

---

## Files to Create/Modify

### New Files:
- `classes/commission_class.php` - Commission management
- `classes/payment_request_class.php` - Payment request management
- `actions/create_payment_request_action.php` - Create payment request
- `actions/fetch_earnings_action.php` - Get vendor/photographer earnings
- `actions/update_payment_request_action.php` - Admin approve/reject/pay
- `actions/fetch_commission_stats_action.php` - Get commission stats for admin
- `vendor/payment_requests.php` - Vendor payment request page
- `photographer/payment_requests.php` - Photographer payment request page
- `admin/payment_requests.php` - Admin payment request management

### Modified Files:
- `actions/verify_payment_action.php` - Add commission calculation on payment
- `actions/update_booking_status_action.php` - Add commission when booking completed
- `admin/dashboard.php` - Add commission stats
- `vendor/earnings.php` - Add payment request functionality
- `photographer/earnings.php` - Add payment request functionality

---

## Security Considerations
- Validate payment request amounts (can't exceed available earnings)
- Prevent duplicate payment requests
- Admin-only access to approve/pay requests
- Secure payment details storage
- Audit trail for all payment requests

