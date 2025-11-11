# Tripoli Central Zoo - Mobile App

A feature-rich Flutter mobile application for Tripoli Central Zoo with offline support, interactive maps, and real-time synchronization.

## Features

- 🦁 **Animal Directory**: Browse and search zoo animals with detailed information
- 🗺️ **Interactive Map**: Custom canvas-based map with zoom, pan, and location markers
- 🏛️ **Facilities Guide**: Find restrooms, restaurants, gift shops, and other amenities
- 📅 **Activity Schedules**: View shows, feeding times, tours, and special events
- 🔄 **Offline Support**: Full functionality without internet connection
- 🔍 **Smart Search**: Search across animals, facilities, and activities
- 📱 **Material Design 3**: Modern, beautiful UI following Material Design guidelines

## Tech Stack

- **Framework**: Flutter 3.0+
- **State Management**: Provider
- **Local Database**: SQLite (sqflite)
- **HTTP Client**: http & dio
- **Map System**: Custom Canvas painter
- **JSON Serialization**: json_annotation & json_serializable

## Project Structure

```
lib/
├── main.dart                 # App entry point
├── models/                   # Data models
│   ├── animal.dart
│   ├── facility.dart
│   └── activity.dart
├── providers/                # State management
│   ├── animal_provider.dart
│   ├── facility_provider.dart
│   ├── activity_provider.dart
│   └── sync_provider.dart
├── screens/                  # UI screens
│   ├── home/
│   ├── animals/
│   ├── facilities/
│   ├── activities/
│   └── map/
├── services/                 # Business logic
│   ├── api_service.dart
│   └── database_service.dart
├── widgets/                  # Reusable widgets
│   ├── common/
│   └── map/
└── utils/                    # Utilities and helpers
```

## Getting Started

### Prerequisites

- Flutter 3.0 or higher
- Dart 3.0 or higher
- Android Studio / VS Code with Flutter extensions
- Android SDK or Xcode (for iOS)

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd zoo-mobile-app
```

2. Install dependencies:
```bash
flutter pub get
```

3. Configure API endpoint:

Edit `lib/services/api_service.dart` and update the base URL:
```dart
static const String baseUrl = 'http://your-server-ip:8000/api/v1';
```

4. Run the app:
```bash
# Check available devices
flutter devices

# Run on specific device
flutter run -d <device-id>

# Run in debug mode
flutter run

# Run in release mode
flutter run --release
```

## Building

### Android

Generate debug APK:
```bash
flutter build apk --debug
```

Generate release APK:
```bash
flutter build apk --release
```

Generate App Bundle for Play Store:
```bash
flutter build appbundle --release
```

### iOS

Build for iOS:
```bash
flutter build ios --release
```

**Note**: iOS builds require a macOS machine with Xcode installed.

## Architecture

### State Management

The app uses the Provider pattern for state management:

- **AnimalProvider**: Manages animal data, fetching, and caching
- **FacilityProvider**: Manages facility data and operations
- **ActivityProvider**: Manages activities and schedules
- **SyncProvider**: Handles online/offline synchronization

### Offline-First Architecture

1. **Initial Load**: Data fetched from API on first launch
2. **Local Caching**: All data stored in SQLite database
3. **Offline Access**: App reads from local database when offline
4. **Background Sync**: Automatic synchronization when connection restored
5. **Conflict Resolution**: Server timestamps used for data consistency

### Database Schema

Local SQLite database mirrors the backend schema:

- `animals` - Animal information
- `animal_categories` - Animal categorization
- `facilities` - Zoo facilities
- `facility_types` - Facility categorization
- `activities` - Events and schedules
- `map_locations` - Map coordinates
- `sync_metadata` - Synchronization tracking

## API Integration

### Endpoints Used

- `GET /api/v1/animals` - Fetch all animals
- `GET /api/v1/facilities` - Fetch all facilities
- `GET /api/v1/activities` - Fetch all activities
- `GET /api/v1/activities/today` - Today's activities
- `GET /api/v1/map-locations` - Map coordinates
- `POST /api/v1/sync` - Synchronize data
- `GET /api/v1/search?q={query}` - Search

### Error Handling

The app implements graceful error handling:
- Network errors: Fallback to cached data
- API errors: Display user-friendly messages
- Sync errors: Queue for retry when online

## Testing

Run tests:
```bash
flutter test
```

Run tests with coverage:
```bash
flutter test --coverage
```

## Performance Optimization

- **Lazy Loading**: Lists load data on demand
- **Image Caching**: Network images cached using `cached_network_image`
- **Database Indexing**: Optimized queries with proper indexes
- **Widget Optimization**: Use of `const` constructors
- **State Management**: Efficient provider updates

## Customization

### Changing Theme

Edit `lib/main.dart`:
```dart
theme: ThemeData(
  colorScheme: ColorScheme.fromSeed(
    seedColor: const Color(0xFF2E7D32), // Change this color
    brightness: Brightness.light,
  ),
  useMaterial3: true,
),
```

### Updating Map Design

Edit `lib/screens/map/map_screen.dart` in the `ZooMapPainter` class to customize map appearance.

## Troubleshooting

### Common Issues

**Problem**: Build fails with dependency errors
```bash
Solution: flutter clean && flutter pub get
```

**Problem**: API connection fails
```bash
Solution: Check baseUrl in api_service.dart and network permissions
```

**Problem**: Database errors
```bash
Solution: Uninstall and reinstall the app to reset database
```

## Contributing

1. Follow Flutter style guide
2. Add tests for new features
3. Update documentation
4. Create pull request with clear description

## License

This project is proprietary software for Tripoli Central Zoo.

## Support

For technical support:
- Email: support@tripolizoo.com
- Create an issue in the repository

---

**Version**: 1.0.0  
**Last Updated**: November 2025
