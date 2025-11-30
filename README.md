# GlamPhotobooth Accra - Marketplace Platform

A comprehensive marketplace platform connecting clients with photographers and vendors for event photography services and related products.

## Overview

GlamPhotobooth Accra is a web-based marketplace that facilitates seamless transactions between clients seeking photography services, professional photographers offering event coverage, and vendors selling photography-related products. The platform manages bookings, product sales, secure payments, gallery delivery, and provider payouts through an automated commission system.

## Core Features

### Customer Portal
- Account management and profile customization
- Browse and filter photographer portfolios by service type, pricing, and availability
- Product catalogue with search and category filtering
- Secure checkout supporting card payments via Paystack
- Post-event gallery access with download capabilities
- Automated notifications for bookings, payments, and gallery delivery

### Photographer Dashboard
- Portfolio management with service descriptions and pricing packages
- Availability calendar and booking management
- Client communication tools
- Event gallery upload and delivery system
- Earnings overview with commission breakdown
- Payment request submission for completed bookings

### Vendor Dashboard
- Product catalogue management with images, descriptions, and pricing
- Inventory tracking and stock management
- Order fulfillment dashboard
- Sales analytics and revenue tracking
- Commission-based earnings with payment request functionality

### Administrative Panel
- User and provider verification workflows
- Content moderation and category management
- Transaction monitoring with 5% platform commission tracking
- Payment request approval and processing
- Analytics dashboard for platform performance

## Technical Stack

**Frontend**
- HTML5, CSS3, Tailwind CSS
- JavaScript (ES6+)
- Responsive design for mobile and desktop

**Backend**
- PHP 7.4+
- MySQL database
- RESTful API architecture

**Third-Party Integrations**
- Paystack payment gateway
- Email notification system

**Infrastructure**
- Cloud hosting (AWS-ready, currently Ashesi Server)
- SSL encryption for secure transactions
- Automated database backups

## System Architecture

### Database Schema
The platform uses MySQL with the following core tables:
- `pb_customer` - User accounts and authentication
- `pb_service_providers` - Photographer and vendor profiles
- `pb_products` - Service listings and vendor products
- `pb_orders` - Purchase transactions
- `pb_bookings` - Photography service bookings
- `pb_commissions` - 5% platform commission tracking
- `pb_payment_requests` - Provider payout management
- `pb_payment` - Payment transaction records

### Commission System
The platform operates on a 5% commission model:
- Platform retains 5% of all transactions
- Providers receive 95% of gross revenue
- Commissions are calculated automatically upon payment verification
- Providers can request payouts when earnings are available
- Admin approval required before payment processing

## Installation

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- Composer for dependency management

### Setup Instructions

1. Clone the repository
```bash
git clone https://github.com/your-org/glamphotobooth-1.git
cd glamphotobooth-1
```

2. Configure database
```bash
# Import database schema
mysql -u your_user -p your_database < db/dbfinal.sql
mysql -u your_user -p your_database < db/payment_requests_table.sql
```

3. Backfill commission records for existing orders
```bash
mysql -u your_user -p your_database < db/backfill_vendor_commissions.sql
```

4. Configure environment settings
```bash
# Update settings/db_cred.php with your database credentials
# Update settings/paystack_config.php with Paystack API keys
```

5. Set file permissions
```bash
chmod 755 uploads/
chmod 755 uploads/products/
```

6. Access the platform
- Navigate to your configured domain
- Default admin login credentials should be changed immediately after first login

## Payment Request Workflow

### For Providers (Photographers/Vendors)
1. View available earnings in dashboard earnings page
2. Submit payment request with bank or mobile money details
3. Track request status (Pending, Approved, Paid, Rejected)
4. Receive notification upon payment processing

### For Administrators
1. Review payment requests in admin panel
2. Verify provider details and commission calculations
3. Approve or reject requests with optional notes
4. Mark approved requests as paid with payment reference
5. System automatically tracks requested amounts to prevent duplicate withdrawals

## Security Considerations

- All transactions use SSL encryption
- PCI DSS compliant payment processing via Paystack
- SQL injection prevention through prepared statements
- XSS protection with input sanitization
- Password hashing using PHP password functions
- CSRF token protection on sensitive forms
- Role-based access control (Customer, Photographer, Vendor, Admin)

## Verification Scripts

### Commission Verification
Access `/admin/verify_commissions.php` as admin to:
- View all commission records
- Check earnings by provider
- Identify orders missing commission records
- Verify commission calculations

## Development Methodology

The platform was developed using Agile methodology with iterative sprints focusing on:
- Modular feature development
- Continuous user feedback integration
- Rapid deployment cycles
- Stakeholder-driven prioritization

## Performance Requirements

- Page load times under 3 seconds
- Support for concurrent users during peak periods
- Optimized database queries with indexing
- Image optimization for gallery delivery
- Mobile-responsive design

## Roadmap

**Phase 1: Foundation** (Complete)
- User authentication and profile management
- Database schema and core architecture

**Phase 2: Provider Modules** (Complete)
- Photographer and vendor dashboards
- Product and service management

**Phase 3: Marketplace Features** (Complete)
- Search and filtering
- Payment integration
- Booking and checkout flows

**Phase 4: Gallery Delivery** (Complete)
- Gallery upload system
- Client access and downloads

**Phase 5: Commission System** (Complete)
- Automated commission calculation
- Payment request management
- Admin approval workflow

**Phase 6: Mobile Optimization** (In Progress)
- iOS and Android applications
- Performance optimization
- Analytics integration

**Phase 7: Regional Expansion** (Planned)
- Multi-region support
- Provider onboarding campaigns
- Marketing automation

## Support and Maintenance

For technical issues or feature requests, contact the development team or submit an issue through the project repository.

## License

Proprietary software. All rights reserved.
