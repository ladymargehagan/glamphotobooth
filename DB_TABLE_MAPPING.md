# Database Table Mapping

## Authentication System Table

The authentication system uses the existing `pb_users` table in your database.

### Table: `pb_users`

**Purpose:** Stores all user accounts (customers, admins, providers)

**Required Columns:**
- `id` - Primary key (auto-increment)
- `full_name` - User's full name
- `email` - Unique email address (indexed)
- `phone` - Phone number
- `password` - Bcrypt hashed password
- `role` - User role (1=customer, 2=admin, 3=provider)
- `status` - Account status ('active', 'inactive', 'suspended')
- `created_at` - Account creation timestamp
- `updated_at` - Last update timestamp

**Optional Columns:**
- `remember_token` - Token for "remember me" functionality
- `token_expiry` - Remember me token expiration time

### Example SQL Create Table (if needed)

```sql
CREATE TABLE IF NOT EXISTS pb_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role INT NOT NULL DEFAULT 1,
    status VARCHAR(20) DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    remember_token VARCHAR(255),
    token_expiry DATETIME,
    INDEX(email),
    INDEX(role),
    INDEX(status)
);
```

## User Roles

| Role ID | Role Name | Description |
|---------|-----------|-------------|
| 1 | ROLE_CUSTOMER | Regular customers who book services |
| 2 | ROLE_ADMIN | Platform administrators |
| 3 | ROLE_PROVIDER | Photographers and service vendors |

## Data Types

| Column | Type | Constraints |
|--------|------|-----------|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT |
| `full_name` | VARCHAR(100) | NOT NULL |
| `email` | VARCHAR(100) | UNIQUE, NOT NULL, INDEXED |
| `phone` | VARCHAR(20) | NULL |
| `password` | VARCHAR(255) | NOT NULL (bcrypt hash) |
| `role` | INT | NOT NULL, DEFAULT 1, INDEXED |
| `status` | VARCHAR(20) | DEFAULT 'active', INDEXED |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP |
| `updated_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP ON UPDATE |
| `remember_token` | VARCHAR(255) | NULL |
| `token_expiry` | DATETIME | NULL |

## Authentication Flow with pb_users

### Registration
1. User submits form → register.php
2. Form validates client-side (JS)
3. AJAX sends to actions/register_customer_action.php
4. Server validates all inputs
5. Email uniqueness checked against pb_users
6. Password hashed with bcrypt (cost 12)
7. Row inserted into pb_users with:
   - `role = 1` (ROLE_CUSTOMER)
   - `status = 'active'`
   - Auto-login on success

### Login
1. User submits email + password → login.php
2. Form validates client-side (JS)
3. AJAX sends to actions/login_customer_action.php
4. Server queries pb_users by email
5. Password verified against bcrypt hash
6. Session created if valid
7. Redirect to appropriate dashboard

### Session Data
After successful login, these are stored in `$_SESSION`:
- `user_id` - From pb_users.id
- `user_name` - From pb_users.full_name
- `user_email` - From pb_users.email
- `user_role` - From pb_users.role (1, 2, or 3)

## Important Notes

- **Password Hashing:** Uses bcrypt with cost 12 for security
- **Email Uniqueness:** UNIQUE constraint on email column
- **Role-Based Access:** Pages check pb_users.role to determine permissions
- **Status Control:** Only 'active' users can login
- **Timestamps:** Automatically managed by database

## All Other Tables (Not Modified)

Your authentication system does NOT touch these tables:
- brands
- cart
- categories
- orderdetails
- orders
- payment
- pb_bookings
- pb_cart
- pb_galleries
- pb_gallery_photos
- pb_payments
- pb_photobooths
- pb_photographers
- pb_photographer_packages
- pb_props_addons
- pb_reviews
- pb_subscriptions
- pb_vendors
- products
- customer (legacy - use pb_users instead)

## Troubleshooting Database Issues

### "No table 'pb_users' found"
- Check database name in settings/db_cred.php
- Verify pb_users table exists in your database
- Run the CREATE TABLE query above if needed

### "Column 'full_name' doesn't exist"
- Your pb_users table might have different column names
- Check actual column names: `DESCRIBE pb_users;`
- Update customer_class.php to match your schema

### "Duplicate entry for key 'email'"
- User tried to register with existing email
- Email must be unique in pb_users
- Check for existing account or reset password instead

### "Unknown column 'role' in where clause"
- Your pb_users table might not have role column
- Run ALTER TABLE to add it if missing:
  ```sql
  ALTER TABLE pb_users ADD COLUMN role INT DEFAULT 1;
  ```

---

**All queries have been updated to use `pb_users` instead of `users`.**
