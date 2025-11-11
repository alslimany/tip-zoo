# Quick Start Guide

Get the Tripoli Central Zoo application running in 5 minutes!

## Backend (2 minutes)

```bash
cd zoo-admin-backend

# Install & Setup
composer install
cp .env.example .env
php artisan key:generate

# Database (SQLite for quick start)
php artisan migrate
php artisan db:seed

# Start server
php artisan serve
# ✅ Backend running at http://localhost:8000
```

## Mobile App (3 minutes)

```bash
cd zoo-mobile-app

# Install dependencies
flutter pub get

# Update API URL in lib/services/api_service.dart
# For Android Emulator: http://10.0.2.2:8000/api/v1
# For iOS Simulator: http://localhost:8000/api/v1

# Run app
flutter run
# ✅ App running on your device/emulator
```

## Test It Works

### Backend Test
```bash
curl http://localhost:8000/api/v1/animals
# Should return JSON with 3 animals
```

### Mobile Test
1. Open app
2. Navigate to Animals tab → should show 3 animals
3. Try offline: disable WiFi, app still works!

## Default Credentials

**Admin Backend**
- Email: admin@tripolizoo.com
- Password: (set during seeding)

## Sample Data Included

- ✅ 3 Animals (Lion, Eagle, Python)
- ✅ 2 Facilities (Café, Restrooms)
- ✅ 3 Activities (Feeding, Show, Tour)
- ✅ 4 Map Locations

## Troubleshooting

**Backend won't start?**
```bash
chmod -R 775 storage bootstrap/cache
```

**Mobile won't connect?**
- Check API URL in `lib/services/api_service.dart`
- For emulator: use `10.0.2.2` instead of `localhost`

**Need detailed help?**
- See [INSTALLATION.md](INSTALLATION.md) for full guide
- Check [README.md](README.md) for documentation

## What's Next?

1. Add your own zoo data
2. Upload real images
3. Customize the map
4. Deploy to production

---

**Full Docs**: [README.md](README.md) | **Install Guide**: [INSTALLATION.md](INSTALLATION.md)
