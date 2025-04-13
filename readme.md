# Travel Adventure - Web Security Learning Platform

A PHP-based web application designed to teach web security concepts through practical examples of common vulnerabilities. This is an offline deployment that doesn't require internet connection.

## Features

### 1. Vulnerability Configuration System
- Uses `.env` file to enable/disable vulnerabilities
- Centralized configuration through `config.php`
- Easy toggling of security features
- All dependencies included locally

### 2. SQL Injection Vulnerability
- **Location**: `login.php`
- **Description**: Demonstrates SQL injection through user authentication
- **Vulnerable Code**: Direct concatenation of user input in SQL queries
- **Secure Alternative**: Prepared statements with parameterized queries
- **Educational Content**: 
  - Example payloads provided
  - Code comparison between vulnerable and secure implementations
  - Flag: `ELE{SQL_Inj3ct10n_1s_Fun}`

### 3. Local File Inclusion (LFI) Vulnerability
- **Location**: `places.php`
- **Description**: Demonstrates LFI through place details viewing
- **Vulnerable Code**: Direct file inclusion without proper validation
- **Educational Content**:
  - Example file paths for exploitation
  - Directory traversal examples
  - Flag: `ELE{LFI_Fl@g_1s_H3r3}`

### 4. File Upload Vulnerability
- **Location**: `contact.php`
- **Description**: Demonstrates insecure file upload handling
- **Vulnerable Code**: Insufficient file type validation
- **Educational Content**:
  - Example malicious file uploads
  - Secure file handling practices
  - Flag: `ELE{F1l3_UpL0@d_1s_C00L}`

### 5. Role-Based Access Control
- **Admin Features**:
  - View all bookings
  - Access to admin dashboard
  - Special admin flag: `ELE{ADMin_bookIn9s_FOUnD}`
- **User Features**:
  - View personal bookings
  - Access to user dashboard
  - Limited file upload capabilities

## Project Structure

```
Travel-Adventure/
├── admin/
│   ├── bookings.php    # Admin bookings view
│   └── dashboard.php   # Admin dashboard
├── user/
│   ├── bookings.php    # User bookings view
│   └── dashboard.php   # User dashboard
├── database/
│   └── database.sqlite # SQLite database
├── places/
│   ├── bali.txt
│   ├── seychelles.txt
│   ├── bora_bora.txt
│   ├── anguilla.txt
│   └── aruba.txt
├── .env               # Environment configuration
├── config.php         # Configuration loader
├── header.php         # Common header
├── index.php          # Main page
├── login.php          # Login with SQL injection
├── places.php         # LFI vulnerability
└── contact.php        # File upload vulnerability
```

## Setup Instructions

1. **Requirements**:
   - PHP 7.4 or higher
   - SQLite3 extension
   - Web server (Apache/Nginx)
   - No internet connection required
   - All assets and dependencies included locally

2. **Installation**:
   ```bash
   # Copy the project to your local machine
   # No git clone required as this is an offline deployment
   
   # Configure .env file
   cp .env.example .env
   # Edit .env to enable/disable vulnerabilities
   ```

3. **Database Setup**:
   ```bash
   # The database will be created automatically
   # Sample data will be inserted on first run
   # All database operations are local
   ```

4. **Web Server Configuration**:
   - Point your web server to the project directory
   - Ensure write permissions for the `database` and `uploads` directories
   - Configure your web server to serve the application locally
   - No external dependencies or CDN required

## Offline Usage Notes

- All resources are served locally
- No external API calls or CDN dependencies
- Database operations are performed locally using SQLite
- File uploads are stored in the local `uploads` directory
- All educational content is available offline

## Vulnerability Configuration

Edit the `.env` file to enable/disable vulnerabilities:

```env
SQL_INJECTION=true
LFI=true
FILE_UPLOAD=true
```

## Educational Content

Each vulnerability includes:
- Vulnerable code examples
- Secure alternatives
- Exploitation hints
- Hidden flags
- Best practices
- All content available offline

## Security Notes

- This application contains intentional vulnerabilities for educational purposes
- DO NOT deploy to production environments
- Use only in controlled, isolated environments
- All vulnerabilities should be disabled when not in use
- No internet connection required for operation
- All operations are performed locally

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- Created for educational purposes
- Inspired by real-world vulnerabilities
- Designed to teach secure coding practices
- Optimized for offline usage

