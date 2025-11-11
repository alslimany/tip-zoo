# Tripoli Central Zoo Mobile Application

A complete zoo management system with Laravel admin backend and Flutter mobile application featuring offline support, interactive maps, and real-time synchronization.

## Project Overview

The Tripoli Central Zoo application provides visitors with an interactive mobile experience while giving administrators full control over content management through a web-based admin panel.

## Project Structure

```
tripoli-central-zoo/
├── zoo-admin-backend/     # Laravel 10+ backend API and admin panel
├── zoo-mobile-app/        # Flutter 3.0+ mobile application
└── shared/                # Shared assets and resources
    ├── assets/
    │   ├── images/
    │   ├── icons/
    │   └── maps/
    └── exports/
```

## Features

### Mobile App Features
- 📱 **Offline-First Architecture**: Full functionality without internet connection
- 🗺️ **Interactive Zoo Map**: Custom SVG/Canvas-based map with zoom and pan
- 🦁 **Animal Directory**: Browse and search zoo animals with detailed information
- 🏛️ **Facilities Guide**: Find restrooms, restaurants, and other amenities
- 📅 **Activity Schedules**: View daily shows, feeding times, and special events
- 🔄 **Auto-Sync**: Background synchronization when online
- 🔍 **Smart Search**: Search across animals, facilities, and activities

### Admin Backend Features
- 🔐 **Secure Authentication**: Laravel Sanctum API authentication
- 📊 **Content Management**: Full CRUD operations for all zoo content
- 🗃️ **Database Management**: MySQL database with relationships
- 📡 **RESTful API**: Complete API for mobile app communication
- 📈 **Analytics Ready**: Built for future analytics integration

## Technology Stack

### Backend (zoo-admin-backend/)
- **Framework**: Laravel 10+
- **Authentication**: Laravel Sanctum
- **Database**: MySQL with SQLite for development
- **API**: RESTful JSON API
- **PHP Version**: 8.1+

### Mobile App (zoo-mobile-app/)
- **Framework**: Flutter 3.0+
- **State Management**: Provider pattern
- **Local Database**: SQLite via sqflite
- **HTTP Client**: http & dio packages
- **Map System**: Custom Canvas painter
- **Dart Version**: 3.0+

## Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL (or SQLite for development)
- Flutter 3.0 or higher
- Dart 3.0 or higher

### Backend Setup

1. Navigate to the backend directory:
```bash
cd zoo-admin-backend
```

2. Install dependencies:
```bash
composer install
```

3. Copy environment file and generate key:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure database in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tripoli_zoo
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. (Optional) Seed sample data:
```bash
php artisan db:seed
```

7. Start development server:
```bash
php artisan serve
```

The API will be available at `http://localhost:8000/api/v1`

### Mobile App Setup

1. Navigate to mobile app directory:
```bash
cd zoo-mobile-app
```

2. Install dependencies:
```bash
flutter pub get
```

3. Update API base URL in `lib/services/api_service.dart`:
```dart
static const String baseUrl = 'http://your-server-ip:8000/api/v1';
```

4. Run the app:
```bash
# For Android
flutter run

# For iOS
flutter run -d ios

# For specific device
flutter devices
flutter run -d <device-id>
```

## Database Schema

### Core Tables

#### animals
- Animal information including name, species, habitat, diet
- Links to animal categories
- Feeding times and fun facts
- Image gallery support

#### facilities
- Zoo facilities (restrooms, restaurants, gift shops)
- Opening hours and contact information
- Accessibility features
- Capacity management

#### activities
- Shows, feeding sessions, tours, events
- Schedule with start/end times
- Recurrence support for daily/weekly events
- Booking requirements and pricing

#### map_locations
- Coordinate-based positioning (X, Y)
- SVG path data for complex shapes
- Multi-level map support
- Links to animals, facilities, or activities

#### animal_categories & facility_types
- Categorization for better organization
- Display order management
- Icon support

## API Endpoints

### Public Endpoints

#### Animals
- `GET /api/v1/animals` - List all animals
- `GET /api/v1/animals/{id}` - Get animal details

#### Facilities
- `GET /api/v1/facilities` - List all facilities
- `GET /api/v1/facilities/{id}` - Get facility details

#### Activities
- `GET /api/v1/activities` - List all activities
- `GET /api/v1/activities/{id}` - Get activity details
- `GET /api/v1/activities/today` - Get today's activities

#### Map Locations
- `GET /api/v1/map-locations` - List all map locations
- `GET /api/v1/map-locations/{id}` - Get location details

#### Sync & Search
- `POST /api/v1/sync` - Sync data (send last_sync timestamp)
- `GET /api/v1/search?q={query}` - Search across all content

### Admin Endpoints (Requires Authentication)
- Full CRUD operations under `/api/admin/*`
- Requires valid Sanctum token

## Mobile App Architecture

### State Management
Uses Provider pattern with separate providers for:
- `AnimalProvider` - Manages animal data and state
- `FacilityProvider` - Manages facility data and state
- `ActivityProvider` - Manages activity data and state
- `SyncProvider` - Handles online/offline sync

### Offline Support
- All data cached in local SQLite database
- Automatic fallback to offline data when network unavailable
- Background sync when connection restored
- Conflict resolution with server-side timestamps

### Custom Map System
- Canvas-based rendering for performance
- Touch gestures for zoom and pan
- Interactive location markers
- Support for multiple map levels/floors

## Development Guidelines

### Laravel Backend
- Follow PSR-12 coding standards
- Use Laravel best practices
- Implement proper validation
- Add comprehensive tests
- Document all API endpoints

### Flutter Mobile
- Follow Dart style guide
- Use Provider for state management
- Implement proper error handling
- Add widget tests
- Optimize for offline-first experience

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Testing

### Backend Tests
```bash
cd zoo-admin-backend
php artisan test
```

### Mobile Tests
```bash
cd zoo-mobile-app
flutter test
```

## Deployment

### Backend Deployment
1. Set up production environment
2. Configure production database
3. Run migrations on production
4. Set up SSL certificate
5. Configure web server (Nginx/Apache)
6. Set up queues and schedulers

### Mobile App Deployment

#### Android
```bash
flutter build apk --release
flutter build appbundle --release
```

#### iOS
```bash
flutter build ios --release
```

## License

This project is proprietary software for Tripoli Central Zoo.

## Support

For support, please contact the development team or open an issue in the repository.

## Acknowledgments

- Laravel Framework
- Flutter Framework
- Open source community

---

**Version**: 1.0.0  
**Last Updated**: November 2025
