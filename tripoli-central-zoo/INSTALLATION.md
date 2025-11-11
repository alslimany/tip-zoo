# Tripoli Central Zoo - Installation Guide

Complete installation guide for setting up both the Laravel backend and Flutter mobile application.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Backend Setup](#backend-setup)
3. [Mobile App Setup](#mobile-app-setup)
4. [Connecting Mobile to Backend](#connecting-mobile-to-backend)
5. [Testing the Installation](#testing-the-installation)
6. [Troubleshooting](#troubleshooting)

## System Requirements

### For Backend Development

- **Operating System**: Linux, macOS, or Windows
- **PHP**: 8.1 or higher
- **Composer**: Latest version
- **Database**: MySQL 5.7+ or MariaDB 10.3+ (SQLite for development)
- **Web Server**: Apache or Nginx (optional for development)
- **Memory**: Minimum 512MB RAM

### For Mobile Development

- **Operating System**: Linux, macOS, or Windows
- **Flutter**: 3.0 or higher
- **Dart**: 3.0 or higher
- **Android Studio** (for Android development):
  - Android SDK 21+
  - Android SDK Tools
  - Android Emulator or physical device
- **Xcode** (for iOS development - macOS only):
  - Xcode 14+
  - iOS 12+
  - iOS Simulator or physical device
- **Memory**: Minimum 8GB RAM recommended

## Backend Setup

### Step 1: Install PHP and Composer

#### On Ubuntu/Debian:
```bash
sudo apt update
sudo apt install php8.1 php8.1-cli php8.1-common php8.1-mysql \
  php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### On macOS:
```bash
brew install php@8.1
brew install composer
```

#### On Windows:
- Download PHP from [windows.php.net](https://windows.php.net/download/)
- Download Composer from [getcomposer.org](https://getcomposer.org/download/)

### Step 2: Install MySQL (or use SQLite)

#### MySQL Installation:

**Ubuntu/Debian:**
```bash
sudo apt install mysql-server
sudo mysql_secure_installation
```

**macOS:**
```bash
brew install mysql
brew services start mysql
```

**Windows:**
- Download from [MySQL Downloads](https://dev.mysql.com/downloads/installer/)

#### Create Database:
```bash
mysql -u root -p
CREATE DATABASE tripoli_zoo;
CREATE USER 'zoo_admin'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON tripoli_zoo.* TO 'zoo_admin'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 3: Set Up Backend

```bash
# Navigate to backend directory
cd tripoli-central-zoo/zoo-admin-backend

# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Configure Environment

Edit `.env` file:

```env
APP_NAME="Tripoli Zoo Admin"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tripoli_zoo
DB_USERNAME=zoo_admin
DB_PASSWORD=secure_password

# OR for SQLite (easier for development)
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# Sanctum Configuration
SANCTUM_STATEFUL_DOMAINS=localhost:8000
```

### Step 5: Run Migrations and Seed Data

```bash
# Create database tables
php artisan migrate

# Populate with sample data
php artisan db:seed

# Output should show:
# - Admin user created (admin@tripolizoo.com)
# - 3 animals (Lion, Eagle, Python)
# - 2 facilities (Café, Restrooms)
# - 3 activities
# - Map locations
```

### Step 6: Start Backend Server

```bash
php artisan serve

# Server will start at http://127.0.0.1:8000
# API endpoints available at http://127.0.0.1:8000/api/v1
```

### Step 7: Test Backend API

```bash
# Test animals endpoint
curl http://localhost:8000/api/v1/animals

# Expected response:
# {"success":true,"data":[...]}
```

## Mobile App Setup

### Step 1: Install Flutter

#### On Linux:
```bash
# Download Flutter
git clone https://github.com/flutter/flutter.git -b stable
export PATH="$PATH:`pwd`/flutter/bin"

# Run flutter doctor
flutter doctor
```

#### On macOS:
```bash
# Download Flutter
git clone https://github.com/flutter/flutter.git -b stable
export PATH="$PATH:`pwd`/flutter/bin"

# For iOS development, install Xcode from App Store
sudo xcode-select --switch /Applications/Xcode.app/Contents/Developer
sudo xcodebuild -runFirstLaunch
```

#### On Windows:
- Download Flutter SDK from [flutter.dev](https://flutter.dev/docs/get-started/install/windows)
- Extract to `C:\src\flutter`
- Add to PATH: `C:\src\flutter\bin`

### Step 2: Install Dependencies

```bash
# Android Studio (for Android development)
# Download from: https://developer.android.com/studio

# Install Android SDK and create virtual device

# For iOS (macOS only)
# Install CocoaPods
sudo gem install cocoapods
```

### Step 3: Verify Flutter Installation

```bash
flutter doctor

# Should show:
# ✓ Flutter (Channel stable)
# ✓ Android toolchain
# ✓ Xcode (macOS only)
# ✓ Android Studio
# ✓ VS Code
```

### Step 4: Set Up Mobile Project

```bash
# Navigate to mobile app directory
cd tripoli-central-zoo/zoo-mobile-app

# Get Flutter dependencies
flutter pub get

# This will download all packages specified in pubspec.yaml
```

### Step 5: Configure API Connection

Edit `lib/services/api_service.dart`:

```dart
class ApiService {
  // For Android Emulator
  static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
  
  // For iOS Simulator
  // static const String baseUrl = 'http://localhost:8000/api/v1';
  
  // For Physical Device (replace with your computer's IP)
  // static const String baseUrl = 'http://192.168.1.100:8000/api/v1';
  
  // For Production
  // static const String baseUrl = 'https://api.tripolizoo.com/api/v1';
  
  // ... rest of the code
}
```

**Finding Your IP Address:**

Linux/macOS:
```bash
ifconfig | grep "inet "
```

Windows:
```bash
ipconfig
```

### Step 6: Run Mobile App

```bash
# Check available devices
flutter devices

# Run on Android Emulator
flutter run

# Run on specific device
flutter run -d <device-id>

# Run in release mode (for testing performance)
flutter run --release
```

## Connecting Mobile to Backend

### For Android Emulator

The Android emulator maps `10.0.2.2` to your host machine's `localhost`.

```dart
static const String baseUrl = 'http://10.0.2.2:8000/api/v1';
```

### For iOS Simulator

iOS Simulator can access `localhost` directly.

```dart
static const String baseUrl = 'http://localhost:8000/api/v1';
```

### For Physical Device

1. Connect device and computer to same WiFi network
2. Find your computer's IP address
3. Update API base URL:

```dart
static const String baseUrl = 'http://192.168.1.100:8000/api/v1';
```

4. Update backend CORS settings in `config/cors.php`:

```php
'allowed_origins' => ['*'], // For development only
```

5. Restart backend server:

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## Testing the Installation

### 1. Test Backend API

```bash
# Animals endpoint
curl http://localhost:8000/api/v1/animals

# Facilities endpoint  
curl http://localhost:8000/api/v1/facilities

# Activities endpoint
curl http://localhost:8000/api/v1/activities

# Search endpoint
curl http://localhost:8000/api/v1/search?q=lion
```

### 2. Test Mobile App

1. Launch the app
2. Check if home screen loads with welcome message
3. Navigate to Animals tab - should show 3 animals
4. Navigate to Facilities tab - should show 2 facilities
5. Navigate to Activities tab - should show 3 activities
6. Test offline mode:
   - Load data once (with internet)
   - Turn off internet/WiFi
   - Reopen app - data should still be available

### 3. Test Sync Functionality

1. With internet enabled, open app
2. Check sync status icon (cloud icon in app bar)
3. Pull down to refresh on any list
4. Verify data updates

## Troubleshooting

### Backend Issues

#### Issue: "Class not found"
```bash
Solution:
composer dump-autoload
php artisan clear-compiled
```

#### Issue: Database connection error
```bash
Solution:
1. Check database credentials in .env
2. Verify MySQL is running: sudo systemctl status mysql
3. Test connection: php artisan tinker, then DB::connection()->getPdo();
```

#### Issue: Permission denied
```bash
Solution:
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:www-data storage bootstrap/cache
```

#### Issue: Migration errors
```bash
Solution:
php artisan migrate:fresh --seed
```

### Mobile App Issues

#### Issue: "Unable to locate Android SDK"
```bash
Solution:
1. Set ANDROID_HOME environment variable
2. export ANDROID_HOME=$HOME/Android/Sdk
3. export PATH=$PATH:$ANDROID_HOME/tools:$ANDROID_HOME/platform-tools
```

#### Issue: "CocoaPods not installed" (iOS)
```bash
Solution:
sudo gem install cocoapods
cd ios && pod install
```

#### Issue: "Gradle build failed"
```bash
Solution:
cd android
./gradlew clean
cd ..
flutter clean
flutter pub get
```

#### Issue: API connection timeout
```bash
Solution:
1. Check API baseUrl in api_service.dart
2. Verify backend is running: curl http://localhost:8000/api/v1/animals
3. For emulator, use correct IP (10.0.2.2 for Android)
4. Check firewall settings
5. Ensure phone and computer on same network
```

#### Issue: Build fails with dependency conflicts
```bash
Solution:
flutter clean
flutter pub cache repair
flutter pub get
```

### Network Issues

#### Can't connect from physical device

1. Check both devices on same WiFi
2. Disable firewall temporarily:
   ```bash
   # Linux
   sudo ufw disable
   
   # macOS
   sudo /usr/libexec/ApplicationFirewall/socketfilterfw --setglobalstate off
   ```
3. Start server on all interfaces:
   ```bash
   php artisan serve --host=0.0.0.0
   ```

#### CORS errors in mobile app

Update `config/cors.php` in backend:
```php
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

Then restart Laravel:
```bash
php artisan config:clear
php artisan serve
```

## Next Steps

After successful installation:

1. **Customize Content**: Add real zoo data through API
2. **Upload Images**: Add actual animal and facility images
3. **Configure Map**: Create actual zoo map SVG/coordinates
4. **Test Features**: Thoroughly test all app features
5. **Optimize**: Review performance and optimize as needed
6. **Deploy**: Follow deployment guides for production

## Getting Help

- Check logs: `storage/logs/laravel.log` (backend)
- Run Flutter doctor: `flutter doctor -v`
- Check Flutter logs: `flutter logs`
- Review error messages carefully
- Search GitHub issues
- Contact development team

---

**Installation Guide Version**: 1.0.0  
**Last Updated**: November 2025
