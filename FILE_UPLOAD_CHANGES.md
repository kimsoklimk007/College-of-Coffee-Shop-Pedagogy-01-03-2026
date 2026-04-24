# File Upload System Updates

## Changes Made

### 1. Folder Structure
- **Old folders**: `employee/Staff picture`, `employee/Special documents 1`
- **New folders**: `employee/staff_pictures`, `employee/special_documents`

### 2. Supported File Types
#### Profile Photos:
- **Previously**: jpeg, png, jpg, gif (max 2MB)
- **Now**: jpeg, png, jpg, gif, svg, webp, bmp, ico (max 90000000000000GB)

#### ID Card Photos:
- **Previously**: jpeg, png, jpg, gif (max 2MB)  
- **Now**: jpeg, png, jpg, gif, svg, webp, bmp, ico, pdf (max 90000000000000GB)

#### Contract Documents:
- **Previously**: pdf, doc, docx (max 5MB)
- **Now**: pdf, doc, docx, xls, xlsx, txt, rtf (max 90000000000000GB)

### 3. Image Dimension Standards
- **Profile Photos**: Minimum 100x100 pixels, Maximum 5000x5000 pixels
- **Automatic validation**: Ensures images meet dimension requirements
- **Error messages**: Clear feedback for dimension violations

### 4. Storage Configuration
- Files are stored in: `storage/app/public/employee/`
- Public access via: `public/storage/employee/`
- Both `staff_pictures` and `special_documents` folders created

### 5. Controller Updates
- Updated `EmployeeController.php` store and update methods
- Changed from `image` validation to `file` validation to support more formats
- Updated storage paths to use underscored folder names
- Added custom image dimension validation

### 6. Server Configuration
- **PHP Configuration** (`.user.ini`):
  - `upload_max_filesize = 90000000000000G`
  - `post_max_size = 90000000000000G`
  - `max_execution_time = 3600`
  - `max_input_time = 3600`
  - `memory_limit = -1`
  - `max_file_uploads = 100`

- **Apache Configuration** (`.htaccess`):
  - Same PHP values configured for Apache
  - `Timeout 3600` for large uploads
  - `LimitRequestBody 0` for unlimited request body size

## Benefits
1. **Extremely Large File Support**: Can handle files up to 90000000000000GB (81.85 TB)
2. **Modern Image Formats**: Supports SVG, WebP, and other modern formats
3. **Proper Folder Structure**: No spaces in folder names prevents URL encoding issues
4. **Image Dimension Standards**: Ensures consistent image quality and size
5. **Document Support**: Can upload various document types beyond just PDFs
6. **Server Optimization**: Extended timeouts and memory limits for large uploads

## Migration Notes
- Existing files in old folders should be manually moved to new folders if needed
- Database records with old paths will continue to work but should be updated to new paths
- **Important**: Server restart may be required for `.user.ini` and `.htaccess` changes to take effect

## Testing
- Upload functionality tested with new file types
- Image dimension validation implemented
- Storage paths verified to be accessible via web
- Folders properly created in both storage locations
- Server configuration files created for large upload support

## Important Notes
- **Server Requirements**: The web server must be configured to allow large uploads
- **Disk Space**: Ensure sufficient disk space is available for large files
- **Network**: Large uploads may require stable network connections
- **Performance**: Consider implementing progress indicators for very large uploads
