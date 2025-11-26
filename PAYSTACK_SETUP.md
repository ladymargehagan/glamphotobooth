# Paystack Integration Setup Guide

## Current Status
✓ Public Key configured
⏳ Secret Key pending

## Configuration File Location
`settings/paystack_config.php`

## What You Need to Do

### Step 1: Get Your Paystack API Keys
1. Log in to your Paystack Dashboard: https://dashboard.paystack.com
2. Navigate to Settings → API Keys & Webhooks
3. Copy your **Secret Key** (keep it secure!)
4. Your Public Key is already in the file

### Step 2: Update the Configuration File

In `settings/paystack_config.php`, find this line:
```php
define('PAYSTACK_SECRET_KEY', ''); // You'll provide this
```

Replace it with:
```php
define('PAYSTACK_SECRET_KEY', 'sk_test_your_secret_key_here'); // Your actual secret key
```

### Step 3: Set Up Webhook

In Paystack Dashboard:
1. Go to Settings → API Keys & Webhooks
2. Add a Webhook URL:
   ```
   https://yoursite.com/customer/payment_callback.php
   ```
3. Subscribe to these events:
   - charge.success
   - charge.failed

### Step 4: Test the Integration

1. Add items to cart
2. Go to checkout
3. Fill in test information
4. Click "Proceed to Payment"
5. Use Paystack test card: 4084 0343 6360 6891
6. Expiry: Any future date
7. CVV: Any 3 digits

### Step 5: Go Live (When Ready)

To use real payments:
1. Replace test keys with live keys
2. Update URLs if hosting on live server
3. Ensure SSL/HTTPS is enabled
4. Verify webhook configuration

## API Keys in Configuration

```php
// Public Key - Safe to use on frontend
define('PAYSTACK_PUBLIC_KEY', 'pk_test_cd389d93978a547260b8e8362def282f0b015eb6');

// Secret Key - Keep this private! Only use on backend
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxx'); // ADD THIS
```

## Endpoints Used

- **Initialize**: https://api.paystack.co/transaction/initialize
- **Verify**: https://api.paystack.co/transaction/verify/{reference}
- **Callback**: https://yoursite.com/customer/payment_callback.php

## Amount Conversion

All amounts are automatically converted from Cedis (₵) to Kobo:
```
Amount in Kobo = Amount in Cedis × 100

Example:
₵50.00 = 5000 kobo
```

## Testing Scenarios

### Successful Payment
- Card: 4084 0343 6360 6891
- Expiry: Any future date
- CVV: Any 3 digits
- OTP: 123456

### Failed Payment
- Card: 5555 5555 5555 5555
- Any future expiry
- Any CVV
- Will decline

## Troubleshooting

**Issue**: "Invalid security token"
- Ensure CSRF token is being sent correctly
- Check session is active

**Issue**: "Payment initialization failed"
- Verify Secret Key is correct
- Check internet connectivity
- Review API response in error logs

**Issue**: "Order not updated after payment"
- Check webhook is configured in Paystack
- Ensure payment_callback.php is accessible
- Verify SECRET_KEY matches Paystack account

## Security Notes

✓ Secret Key should never be exposed in frontend code
✓ Always use HTTPS in production
✓ Validate all payment requests
✓ Check payment status before fulfilling orders
✓ Keep Secret Key out of version control

## Support

For issues with Paystack:
- Visit: https://paystack.com/support
- Check logs in Paystack Dashboard for transaction details

For implementation issues:
- Check browser console for JavaScript errors
- Review server error logs
- Verify all files are in correct directories
