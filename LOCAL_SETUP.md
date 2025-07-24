# DJ Land Local Development Setup Guide

## Prerequisites

1. **XAMPP** (recommended for Windows)
   - Download: https://www.apachefriends.org/
   - Install with default settings
   - Start Apache and MySQL services

2. **Composer**
   - Download: https://getcomposer.org/download/
   - Install globally

3. **Git** (if not already installed)
   - Download: https://git-scm.com/

## Quick Setup (Windows)

1. **Run the setup script:**
   ```bash
   setup_local.bat
   ```

2. **Manual steps after running the script:**
   - Start XAMPP (Apache + MySQL)
   - Create database `djland` in phpMyAdmin
   - Visit: `http://localhost/your-project/setup/setup_database.php`
   - Generate Laravel key: `cd app/api2 && php artisan key:generate`

## Manual Setup (All Platforms)

### 1. Database Setup

1. **Start your local server** (XAMPP/WAMP/MAMP)
2. **Create database:**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Create new database: `djland`

### 2. Configuration Files

1. **Create main config:**
   ```bash
   cp config.php.sample config.php
   ```

2. **Edit `config.php`:**
   ```php
   $db = array(
       "address" => "localhost:3306",
       "username" => "root",        // default XAMPP username
       "password" => "",            // default XAMPP password (empty)
       "database" => "djland",
   );
   ```

3. **Create Laravel .env file:**
   ```bash
   cd app/api2
   # Create .env file with database settings
   ```

### 3. Database Structure

1. **Import database structure:**
   - Visit: `http://localhost/your-project/setup/setup_database.php`
   - This will create all necessary tables

2. **Or manually import SQL files:**
   - Import files from `setup/database_structures/`
   - Import files from `setup/defaults/`

### 4. Laravel API Setup

1. **Install dependencies:**
   ```bash
   cd app/api2
   composer install
   ```

2. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

3. **Set permissions (if on Linux/Mac):**
   ```bash
   chmod -R 755 storage/
   chmod -R 755 bootstrap/cache/
   ```

### 5. Access the Application

1. **Main application:**
   - URL: `http://localhost/your-project/`

2. **Default admin login:**
   - Username: `admin`
   - Password: `pass`

3. **Laravel API:**
   - URL: `http://localhost/your-project/app/api2/public/`

## Troubleshooting

### Common Issues

1. **Database connection errors:**
   - Verify MySQL is running
   - Check credentials in `config.php`
   - Ensure database `djland` exists

2. **Laravel errors:**
   - Run `composer install` in `app/api2/`
   - Generate app key: `php artisan key:generate`
   - Check `.env` file exists and has correct database settings

3. **Permission errors (Linux/Mac):**
   - Set proper permissions on `storage/` and `bootstrap/cache/`

4. **404 errors:**
   - Ensure Apache mod_rewrite is enabled
   - Check `.htaccess` files are present

### File Structure

```
newdjland/
├── app/                    # Main PHP application
│   ├── api2/              # Laravel API
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── ...
├── setup/                 # Database setup files
├── config.php             # Main configuration (create from sample)
└── setup_local.bat        # Windows setup script
```

## Features

The application includes:
- **Playsheets**: DJ logging and compliance tracking
- **Music Library**: Searchable music catalog
- **Membership Management**: Volunteer and member database
- **Show Management**: Radio show scheduling
- **CRTC/SOCAN Reports**: Compliance reporting
- **Podcasting Tools**: Audio management (if enabled)

## Development Notes

- PHP version: 7.4+ (Laravel 5.1 requirement)
- MySQL database required
- Apache with mod_rewrite recommended
- SAM Broadcaster integration available (optional)

## Support

For issues specific to this installation, check:
- Laravel logs: `app/api2/storage/logs/`
- PHP error logs: Check your server configuration
- Database logs: Check MySQL error logs 