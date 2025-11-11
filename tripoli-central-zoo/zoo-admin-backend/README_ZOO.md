# Tripoli Central Zoo - Admin Backend

Laravel-based admin backend and RESTful API for the Tripoli Central Zoo mobile application.

## Features

- 🔐 **Secure Authentication**: Laravel Sanctum API authentication
- 📊 **Content Management**: CRUD operations for all zoo content
- 🗃️ **Relational Database**: MySQL with comprehensive schema
- 📡 **RESTful API**: JSON API for mobile app integration
- 🔄 **Data Synchronization**: Efficient sync endpoint with timestamps
- 🔍 **Search Functionality**: Full-text search across content
- 📈 **Scalable Architecture**: Built for growth and expansion

## Tech Stack

- **Framework**: Laravel 10+
- **Authentication**: Laravel Sanctum
- **Database**: MySQL (SQLite for development)
- **PHP Version**: 8.1+
- **API Style**: RESTful JSON

## Database Schema

### Core Tables

**animals**
- Comprehensive animal information
- Linked to categories
- Gallery and feeding schedule support
- Soft deletes enabled

**animal_categories**
- Animal classification
- Display ordering
- Icon support

**facilities**
- Zoo facilities (restrooms, restaurants, shops)
- Opening hours (JSON field)
- Amenities list
- Accessibility features

**facility_types**
- Facility categorization
- Display ordering

**activities**
- Shows, feeding times, tours, events
- Datetime scheduling
- Recurrence support
- Booking and pricing information

**map_locations**
- Coordinate-based positioning
- SVG path support for complex shapes
- Multi-level map support
- Polymorphic relationships

**personal_access_tokens**
- Sanctum authentication tokens

## API Documentation

### Authentication

Protected endpoints require Bearer token:
```
Authorization: Bearer {token}
```

### Public Endpoints

#### Animals

**List Animals**
```
GET /api/v1/animals
```

Response:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "category_id": 1,
      "name": "African Lion",
      "scientific_name": "Panthera leo",
      "description": "...",
      "image": "...",
      "gallery": ["..."],
      "habitat": "Grasslands",
      "conservation_status": "Vulnerable",
      "diet": ["Large ungulates"],
      "feeding_times": ["10:00 AM", "3:00 PM"],
      "is_visible": true,
      "is_featured": true
    }
  ]
}
```

**Get Animal**
```
GET /api/v1/animals/{id}
```

#### Facilities

**List Facilities**
```
GET /api/v1/facilities
```

**Get Facility**
```
GET /api/v1/facilities/{id}
```

#### Activities

**List Activities**
```
GET /api/v1/activities
```

**Today's Activities**
```
GET /api/v1/activities/today
```

**Get Activity**
```
GET /api/v1/activities/{id}
```

#### Map Locations

**List Map Locations**
```
GET /api/v1/map-locations?location_type=animal&map_level=1
```

Query Parameters:
- `location_type`: animal, facility, or activity
- `map_level`: Floor/level number

#### Sync

**Synchronize Data**
```
POST /api/v1/sync
Content-Type: application/json

{
  "last_sync": "2024-01-01T00:00:00Z"
}
```

Returns only data updated since `last_sync` timestamp.

#### Search

**Search Content**
```
GET /api/v1/search?q=lion
```

Searches across animals, facilities, and activities.

### Admin Endpoints (Protected)

All admin endpoints require authentication with Sanctum token.

#### Animals
- `POST /api/admin/animals` - Create animal
- `PUT /api/admin/animals/{id}` - Update animal
- `DELETE /api/admin/animals/{id}` - Delete animal

#### Facilities
- `POST /api/admin/facilities` - Create facility
- `PUT /api/admin/facilities/{id}` - Update facility
- `DELETE /api/admin/facilities/{id}` - Delete facility

#### Activities
- `POST /api/admin/activities` - Create activity
- `PUT /api/admin/activities/{id}` - Update activity
- `DELETE /api/admin/activities/{id}` - Delete activity

#### Map Locations
- `POST /api/admin/map-locations` - Create location
- `PUT /api/admin/map-locations/{id}` - Update location
- `DELETE /api/admin/map-locations/{id}` - Delete location

## Setup Instructions

### Requirements

- PHP 8.1 or higher
- Composer
- MySQL 5.7+ or SQLite
- Web server (Apache/Nginx)

### Installation

1. Clone and navigate to backend:
```bash
cd zoo-admin-backend
```

2. Install dependencies:
```bash
composer install
```

3. Environment configuration:
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure `.env` file:
```env
APP_NAME="Tripoli Zoo Admin"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tripoli_zoo
DB_USERNAME=your_username
DB_PASSWORD=your_password

# For development, you can use SQLite:
# DB_CONNECTION=sqlite
```

5. Run migrations:
```bash
php artisan migrate
```

6. Seed sample data:
```bash
php artisan db:seed
```

This creates:
- Admin user (admin@tripolizoo.com)
- Sample animals (Lion, Eagle, Python)
- Sample facilities (Café, Restrooms)
- Sample activities (Feeding shows, Tours)
- Map locations

7. Start development server:
```bash
php artisan serve
```

API available at: `http://localhost:8000/api/v1`

## Development

### Creating Models

```bash
php artisan make:model ModelName -m
```

### Creating Controllers

```bash
php artisan make:controller Api/ControllerName --api
```

### Creating Migrations

```bash
php artisan make:migration create_table_name_table
```

### Running Tests

```bash
php artisan test
```

### Code Style

```bash
# Install Pint (Laravel's PHP CS Fixer)
composer require laravel/pint --dev

# Format code
./vendor/bin/pint
```

## Models and Relationships

### Animal Model

```php
// Relationships
$animal->category // BelongsTo AnimalCategory
$animal->mapLocations // HasMany MapLocation

// Attributes
$animal->gallery // Array
$animal->diet // Array
$animal->feeding_times // Array
```

### Facility Model

```php
// Relationships
$facility->facilityType // BelongsTo FacilityType
$facility->activities // HasMany Activity

// Attributes
$facility->opening_hours // Array
$facility->amenities // Array
```

### Activity Model

```php
// Relationships
$activity->facility // BelongsTo Facility
$activity->animal // BelongsTo Animal

// Attributes
$activity->start_time // DateTime
$activity->end_time // DateTime
$activity->recurrence // Array
```

## Deployment

### Production Setup

1. Configure production environment:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.tripolizoo.com
```

2. Optimize for production:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Set up queue worker:
```bash
php artisan queue:work --daemon
```

4. Set up scheduled tasks:
```bash
# Add to crontab
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Security Checklist

- [ ] Set strong `APP_KEY`
- [ ] Disable debug mode in production
- [ ] Configure CORS properly
- [ ] Set up SSL certificate
- [ ] Configure firewall rules
- [ ] Regular database backups
- [ ] Keep dependencies updated
- [ ] Use environment variables for secrets

## Monitoring and Logs

### View Logs

```bash
tail -f storage/logs/laravel.log
```

### Clear Logs

```bash
php artisan log:clear
```

## Backup and Restore

### Database Backup

```bash
# MySQL
mysqldump -u username -p tripoli_zoo > backup.sql

# Restore
mysql -u username -p tripoli_zoo < backup.sql
```

### Application Backup

Include:
- Database
- `.env` file
- `storage/` directory (uploaded files)
- `public/` directory (assets)

## Troubleshooting

### Common Issues

**Issue**: Permission denied errors
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

**Issue**: Migration errors
```bash
# Rollback and re-run
php artisan migrate:rollback
php artisan migrate
```

**Issue**: Cache issues
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## API Rate Limiting

Default rate limit: 60 requests per minute per IP

Configure in `app/Http/Kernel.php`:
```php
'api' => [
    'throttle:60,1',
],
```

## Contributing

1. Follow PSR-12 coding standard
2. Write tests for new features
3. Update API documentation
4. Use meaningful commit messages

## License

This project is proprietary software for Tripoli Central Zoo.

---

**Version**: 1.0.0  
**Last Updated**: November 2025
