# Photo Gallery System Documentation

## Overview

The PhotoMarket gallery system enables providers (photographers) to upload and organize photos from completed bookings. Customers can securely view galleries using access codes without requiring login credentials.

## Components

### Backend Files (6 files)

#### Classes
- **`classes/gallery_class.php`** (250+ lines)
  - Core gallery and photo management
  - Methods:
    - `create_gallery()` - Create new gallery for booking
    - `get_gallery_by_id()` - Retrieve gallery with booking details
    - `get_gallery_by_access_code()` - Public gallery access
    - `get_provider_galleries()` - Provider's all galleries
    - `get_gallery_photos()` - Retrieve photos in gallery
    - `add_photo()` - Add photo to gallery
    - `delete_photo()` - Remove photo
    - `get_photo_by_id()` - Retrieve single photo details
    - `get_gallery_by_booking()` - Get or verify gallery for booking
    - `update_gallery_title()` - Update gallery title
    - `delete_gallery()` - Full gallery cleanup

#### Controllers
- **`controllers/gallery_controller.php`** (150+ lines)
  - Business logic and validation
  - Methods:
    - `create_gallery_ctr()` - Validate and create
    - `validate_photo_upload()` - File type/size validation
    - `save_photo()` - Process and store photo
    - `validate_gallery_access()` - Access code verification

#### Actions (API Endpoints)
- **`actions/create_gallery_action.php`** (70+ lines)
  - POST endpoint for gallery creation
  - Only for completed bookings
  - Provider authorization check

- **`actions/upload_photos_action.php`** (100+ lines)
  - POST endpoint for multi-file uploads
  - Handles bulk photo processing
  - Creates folder structure: `uploads/u{provider_id}/g{gallery_id}/`
  - Returns upload results with photo IDs

- **`actions/fetch_gallery_photos_action.php`** (60+ lines)
  - GET endpoint for public photo retrieval
  - Requires valid access code
  - Returns photo URLs and gallery metadata

- **`actions/delete_photo_action.php`** (50+ lines)
  - POST endpoint for photo deletion
  - Provider authorization required
  - File cleanup from uploads folder

### Frontend Files (2 files)

- **`customer/upload_photos.php`** (350+ lines with CSS)
  - Upload interface for providers
  - Features:
    - Drag-and-drop upload
    - File input selector
    - Booking information display
    - Photo grid preview
    - Delete buttons with confirmation
    - Share link generation
    - Responsive design

- **`customer/view_gallery.php`** (400+ lines with CSS)
  - Public gallery viewer
  - Features:
    - Access code validation
    - Responsive masonry grid
    - Full-screen lightbox
    - Keyboard navigation (arrows, ESC)
    - Previous/next photo navigation
    - Photo counter
    - Zoom on hover
    - Mobile responsive

### JavaScript (1 file)

- **`js/gallery.js`** (150+ lines)
  - Drag-and-drop handler
  - Upload progress tracking
  - File selection management
  - Photo deletion with confirmation
  - Error/success messaging

### Database

- **`db/gallery_migration.sql`**
  - `pb_galleries` table
    - Stores gallery metadata
    - Foreign keys to bookings and providers
    - Unique access codes for security
  - `pb_gallery_photos` table
    - Stores photo references
    - Cascading deletes with gallery

## Folder Structure

Images stored with strict folder organization:

```
uploads/
├── u{provider_id}/
│   ├── g{gallery_id}/
│   │   ├── photo1_1732507200_1234.jpg
│   │   ├── photo2_1732507201_5678.jpg
│   │   └── ...
│   └── g{another_gallery_id}/
│       └── ...
└── u{another_provider_id}/
    └── ...
```

**Example**: `uploads/u5/g42/wedding_photo_1732507200_9876.jpg`

**Key Points:**
- All uploads only go in `/uploads/` folder
- Subdirectories created automatically
- Files never leave `/uploads/` tree
- Provider ID (user_id) prevents cross-access
- Gallery ID provides additional isolation
- Filename includes timestamp + random for uniqueness

## Database Schema

### pb_galleries Table
| Column | Type | Notes |
|--------|------|-------|
| gallery_id | INT | Auto-increment primary key |
| booking_id | INT | FK to pb_bookings |
| provider_id | INT | FK to pb_service_providers |
| title | VARCHAR(255) | Optional gallery title |
| access_code | VARCHAR(50) | Unique, 16-char hex string |
| created_at | TIMESTAMP | Creation timestamp |
| updated_at | TIMESTAMP | Auto-updated on changes |

### pb_gallery_photos Table
| Column | Type | Notes |
|--------|------|-------|
| photo_id | INT | Auto-increment primary key |
| gallery_id | INT | FK to pb_galleries (CASCADE) |
| file_path | VARCHAR(500) | Relative path in /uploads/ |
| original_name | VARCHAR(255) | Original uploaded filename |
| photo_order | INT | Display order (0 = first) |
| created_at | TIMESTAMP | Upload timestamp |

## Workflow

### 1. Provider Completes Booking
- Booking status changed to "completed"
- "Upload Photos" button appears in manage_bookings.php

### 2. Provider Uploads Photos
- Navigate to upload_photos.php?booking_id=X
- View booking information
- Drag-and-drop or click to select photos
- Multiple files supported
- Real-time progress display
- Photos stored in `uploads/u{id}/g{id}/`
- Gallery auto-created if needed

### 3. Share Gallery Link
- Access code auto-generated and displayed
- Link format: `/customer/view_gallery.php?id={id}&code={code}`
- Provider copies and shares with customer
- No login required to view

### 4. Customer Views Gallery
- Opens gallery link with access code
- Validates access code
- Displays masonry grid of photos
- Click photo to open lightbox
- Navigate with arrows or keyboard
- Close with ESC key

## Security Features

### Access Control
- ✓ CSRF token required on all uploads
- ✓ User authentication on upload pages
- ✓ Provider ownership verification
- ✓ Access code randomization (16-char hex)
- ✓ Access code validated on gallery view
- ✓ No direct file enumeration possible

### File Safety
- ✓ MIME type validation (image/* only)
- ✓ getimagesize() verification
- ✓ File size limits (5MB per file)
- ✓ Unique filename generation (timestamp + random)
- ✓ Folder permissions set to 0755

### Data Protection
- ✓ Prepared SQL statements (no injection)
- ✓ htmlspecialchars() on all output
- ✓ Input sanitization and validation
- ✓ Cascade deletes prevent orphaned photos

## API Endpoints

### Create Gallery
**POST** `/actions/create_gallery_action.php`

```php
Parameters:
- booking_id (required, integer)
- title (optional, string)
- csrf_token (required)

Response:
{
  "success": true,
  "gallery_id": 42
}
```

Requirements:
- Booking must be completed
- User must be provider for booking
- Gallery must not already exist

### Upload Photos
**POST** `/actions/upload_photos_action.php`

```php
Parameters:
- gallery_id (required)
- photos[] (required, multiple files)
- csrf_token (required)

Response:
{
  "success": true,
  "uploaded_count": 3,
  "error_count": 0,
  "uploaded_photos": [
    {"photo_id": 1, "file_path": "u5/g42/photo1.jpg"},
    ...
  ]
}
```

Features:
- Processes multiple files
- Returns individual results
- Creates folder structure
- Returns photo IDs and paths

### Fetch Gallery Photos
**GET/POST** `/actions/fetch_gallery_photos_action.php`

```php
Parameters:
- gallery_id (required)
- code (required, access code)

Response:
{
  "success": true,
  "gallery": {
    "gallery_id": 42,
    "title": "Wedding Photos",
    "customer_name": "John Doe",
    "booking_date": "2024-11-20",
    "created_at": "2024-11-26T10:00:00Z"
  },
  "photos": [
    {
      "photo_id": 1,
      "file_path": "u5/g42/photo.jpg",
      "url": "/uploads/u5/g42/photo.jpg",
      ...
    }
  ],
  "photo_count": 3
}
```

### Delete Photo
**POST** `/actions/delete_photo_action.php`

```php
Parameters:
- photo_id (required)
- csrf_token (required)

Response:
{
  "success": true,
  "message": "Photo deleted successfully"
}
```

Requirements:
- User must be provider who owns photo
- Deletes file and database record

## Installation

### 1. Create Database Tables

```bash
mysql -u username -p database_name < db/gallery_migration.sql
```

Or copy SQL from gallery_migration.sql into your database admin tool.

### 2. Verify Upload Folder

```bash
ls -la /path/to/project/uploads/
# Should be writable by web server
```

### 3. Verify Files Exist

- [ ] classes/gallery_class.php
- [ ] controllers/gallery_controller.php
- [ ] actions/create_gallery_action.php
- [ ] actions/upload_photos_action.php
- [ ] actions/fetch_gallery_photos_action.php
- [ ] actions/delete_photo_action.php
- [ ] customer/upload_photos.php
- [ ] customer/view_gallery.php
- [ ] js/gallery.js

## Usage

### For Providers

1. Complete a booking (status → completed)
2. In manage_bookings.php, click "📸 Upload Photos"
3. Drag photos or click to select
4. View upload progress
5. Copy and share gallery link with customer

### For Customers

1. Receive gallery link from provider
2. Open link in browser (no login needed)
3. View photos in responsive grid
4. Click photo for full-screen lightbox
5. Use arrows or keyboard to navigate
6. Press ESC to close lightbox

## File Validation

- **Allowed Types**: JPEG, PNG, GIF, WebP
- **Max Size**: 5MB per file
- **Validation Methods**:
  - MIME type check (finfo_file)
  - getimagesize() verification
  - File extension validation

## Performance Considerations

- Lazy loading images (loading="lazy")
- Responsive grid with CSS Grid
- Minimal JavaScript (vanilla JS)
- No external dependencies
- AJAX uploads (no page reload)
- Database indexes on foreign keys

## Troubleshooting

### "Access Denied" on view_gallery.php
- Check access code matches gallery_id
- Verify gallery exists in database
- Check for typos in URL

### Upload fails silently
- Check browser console for JavaScript errors
- Verify CSRF token is valid
- Check file size (max 5MB)
- Check file is valid image

### Photos don't appear
- Check uploads folder exists and is writable
- Verify file permissions (should be 0644)
- Check database has photo records
- Check file_path is correct in database

### "Invalid booking" error
- Booking must be "completed" status
- User must be provider for booking
- Check booking_id parameter

### Lightbox not working
- Ensure js/gallery.js is loaded
- Check browser console for errors
- Verify photos have correct paths
- Try different browser

## Future Enhancements

1. **Photo Ordering**
   - Drag-to-reorder photos
   - Update photo_order in database

2. **Thumbnails**
   - Generate thumbnails on upload
   - Faster loading for grids

3. **Image Optimization**
   - Compress images on upload
   - Generate multiple sizes

4. **Watermarks**
   - Add provider watermark
   - Configurable watermark

5. **Password Protection**
   - Optional password for gallery
   - Additional security layer

6. **Bulk Download**
   - Download all photos as ZIP
   - Format options (JPEG, original)

7. **Sharing Options**
   - Social media sharing buttons
   - Email sharing
   - QR code generation

8. **EXIF Data**
   - Display camera settings
   - Remove EXIF on download
   - Photo metadata display

## Security Checklist

- [ ] Database migration executed
- [ ] /uploads/ folder writable by web server
- [ ] CSRF tokens validated on all forms
- [ ] Provider authorization checked on all operations
- [ ] File uploads validated (type, size, content)
- [ ] Access codes randomly generated
- [ ] SQL statements use prepared statements
- [ ] Output properly escaped (htmlspecialchars)
- [ ] Folder permissions correct (u5/g42/ structure)

## Testing Scenarios

### Scenario 1: Happy Path Upload
1. Complete booking
2. Click "Upload Photos"
3. Drag 3 photos
4. Verify upload progress
5. Check photos appear in gallery
6. Visit gallery link
7. Verify all photos display

### Scenario 2: Access Code Validation
1. Try to access gallery with wrong code
2. Should get "Access Denied"
3. Try with correct code
4. Should display gallery

### Scenario 3: Authorization
1. Log in as different provider
2. Try to access another's photos
3. Should get "Permission Denied"
4. Own uploads should work

### Scenario 4: File Validation
1. Try to upload non-image (text file)
2. Should reject with error
3. Try to upload > 5MB image
4. Should reject with error
5. Upload valid image
6. Should succeed

### Scenario 5: Deletion
1. Upload photos
2. Delete one photo
3. Should disappear from grid
4. Check file removed from disk
5. Check database record deleted

## Support

For issues or questions:
1. Check Troubleshooting section
2. Review Security Checklist
3. Check browser console
4. Check server error logs
5. Verify database migration completed

---

**Gallery System Implementation Complete**

All files in place and ready for database migration and testing.
