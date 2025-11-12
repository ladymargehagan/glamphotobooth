# Glam PhotoBooth Accra - E-Commerce Platform

A luxurious, multi-role photography services marketplace built with PHP, HTML, CSS, and JavaScript using the MVC architecture.

## Project Overview

Glam PhotoBooth Accra is a comprehensive photography services platform where users can book photographers, rent photobooths, and purchase add-on services. The platform serves three distinct user roles with elegant, luxury-focused design.

## Design System

### Color Palette
- **Primary Colors:**
  - Navy Blue: `#1B2B4D`, `#2C3E5F`
  - Gold/Champagne: `#D4AF78`, `#C9A961`
- **Neutral Colors:**
  - White: `#FFFFFF`
  - Cream: `#F5F5F0`
- **Typography:**
  - Headings: `Playfair Display` (serif)
  - Body: `Inter` (sans-serif)

## Project Structure

```
ECommerceFinalProject/
│
├── assets/
│   ├── css/
│   │   ├── global.css          # Global styles, colors, utilities
│   │   ├── auth.css            # Authentication pages styles
│   │   ├── dashboard.css       # Dashboard styles for all roles
│   │   └── gallery.css         # Gallery page styles
│   ├── js/
│   │   ├── main.js             # Common JavaScript functionality
│   │   ├── auth.js             # Authentication handling
│   │   ├── dashboard.js        # Dashboard interactions
│   │   ├── packages.js         # Package filtering
│   │   └── gallery.js          # Gallery interactions
│   ├── images/                 # Image assets
│   └── fonts/                  # Custom fonts
│
├── views/
│   ├── public/                 # Public pages
│   ├── customer/               # Customer dashboard & pages
│   │   └── dashboard.php
│   ├── provider/               # Service provider dashboard & pages
│   │   └── dashboard.php
│   ├── admin/                  # Admin dashboard & pages
│   │   └── dashboard.php
│   ├── auth/                   # Authentication views
│   └── components/             # Reusable components
│       ├── navbar.php
│       └── footer.php
│
├── models/                     # Database models (to be implemented)
├── controllers/                # Application controllers (to be implemented)
├── db/                         # Database configuration
├── config/                     # Application configuration
├── includes/                   # Shared includes
│
├── index.php                   # Landing page
├── packages.php                # Services/packages page
├── gallery.php                 # Gallery showcase
├── login.php                   # Login page
├── register.php                # Customer registration
├── provider-signup.php         # Provider application form
└── README.md                   # This file
```

## User Roles

### 1. Customer (Regular Users)
**Access:** Public registration with email/social login

**Features:**
- Browse photographers, photobooths, and add-ons
- Book and customize packages
- Secure payment processing
- View upcoming and past bookings
- Access digital photo galleries
- Review and rate service providers
- Loyalty points system

**Dashboard:** `/views/customer/dashboard.php`

### 2. Service Provider (Photographers/Vendors)
**Access:** Application-based registration with admin approval

**Features:**
- Profile and portfolio management
- Service listings (packages, pricing)
- Booking calendar and availability
- Client communication tools
- Upload and deliver photo galleries
- Revenue tracking and payout history
- Performance analytics

**Dashboard:** `/views/provider/dashboard.php`

### 3. Administrator
**Access:** Secure admin login (separate from public registration)

**Features:**
- Full platform analytics
- User management (customers & providers)
- Provider approval workflow
- Booking oversight and dispute resolution
- Content management
- Financial reporting
- System settings and security controls

**Dashboard:** `/views/admin/dashboard.php`

## Key Pages

### Public Pages
1. **Landing Page** (`index.php`)
   - Hero section with CTAs
   - Featured services
   - How it works section
   - Featured packages
   - Testimonials

2. **Packages Page** (`packages.php`)
   - Photography packages
   - Photobooth rentals
   - Premium add-ons
   - Filtering by category

3. **Gallery** (`gallery.php`)
   - Event galleries by category
   - Filterable by event type
   - Stats showcase

### Authentication Pages
1. **Login** (`login.php`)
   - Email/password authentication
   - Social login options
   - Remember me functionality

2. **Register** (`register.php`)
   - Customer account creation
   - Email verification
   - Terms acceptance

3. **Provider Signup** (`provider-signup.php`)
   - Business information
   - Portfolio upload
   - Admin approval required

### Dashboard Pages
Each role has a dedicated dashboard with:
- Stats overview
- Quick actions
- Data tables
- Recent activity
- Role-specific tools

## Design Features

### UI Components
- Luxury pricing cards with "Recommended" badges
- Interactive navigation with role-based menus
- Responsive sidebar dashboards
- Filterable galleries
- Smooth animations and transitions
- Mobile-responsive design

### Color System
All colors are defined as CSS variables in `global.css`:
```css
--navy-dark: #1B2B4D;
--gold-primary: #D4AF78;
--cream: #F5F5F0;
```

### Typography Scale
- H1: 3.5rem (hero titles)
- H2: 2.75rem (section headings)
- Body: 1rem with 1.6 line-height

## Features to Implement (Backend)

### Database Models
- Users (customers, providers, admins)
- Bookings
- Services/Packages
- Galleries
- Reviews
- Payments
- Notifications

### Controllers
- AuthController (login, register, logout)
- BookingController
- ServiceController
- GalleryController
- PaymentController
- AdminController

### Additional Functionality
- Email verification
- Password reset
- Payment gateway integration (Stripe/Paystack)
- File upload handling
- Image optimization
- Search functionality
- Real-time notifications
- Admin approval workflow

## Security Considerations

1. **Authentication:**
   - Separate admin authentication (not through public signup)
   - Password hashing (bcrypt)
   - Session management
   - CSRF protection

2. **Authorization:**
   - Role-based access control
   - Route protection
   - API endpoint security

3. **Data Protection:**
   - SQL injection prevention
   - XSS protection
   - File upload validation
   - HTTPS enforcement

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Responsive Breakpoints

- Desktop: 1024px+
- Tablet: 768px - 1023px
- Mobile: < 768px

## Future Enhancements

1. Real-time chat between customers and providers
2. AI-powered photographer recommendations
3. Virtual tour of photobooth setups
4. Advanced analytics dashboard
5. Mobile app (iOS/Android)
6. Multi-language support
7. Automated email campaigns
8. Referral program
9. Gift cards and vouchers
10. Integration with social media platforms

## Credits

**Design:** Luxury photography services aesthetic
**Fonts:** Google Fonts (Playfair Display, Inter)
**Icons:** Emoji icons for rapid prototyping

## License

Proprietary - All rights reserved

---

**Note:** This is the UI/Frontend implementation. Backend functionality (database, controllers, authentication logic) needs to be implemented separately using PHP and MySQL.
