# Admin Panel Documentation

## Overview

The Tripoli Central Zoo Admin Panel is a comprehensive Laravel-based administration system for managing zoo content, including animals, facilities, activities, and interactive maps.

## Features

### Dashboard
- **Statistics Cards**: Display total counts for animals, facilities, and activities
- **Recent Activity Log**: Shows the 10 most recent activities
- **Quick Actions**: One-click access to create new content
- **System Stats**: Real-time status of active animals, open facilities, and scheduled activities

### Animal Management
Complete CRUD interface for managing zoo animals:
- **List View**: Paginated table with filtering and bulk operations
- **Create/Edit Forms**: Comprehensive forms with:
  - Basic information (name, species, scientific name)
  - Category assignment
  - Physical attributes (age, weight, size)
  - Conservation details (habitat, status)
  - Image upload with preview
  - Interactive map picker for location coordinates
  - Featured status toggle
- **Bulk Operations**: 
  - Delete multiple animals
  - Update status for multiple animals
- **Image Management**: Automatic upload, storage, and cleanup

### Facility Management
Full CRUD system for zoo facilities:
- **Type-Based Forms**: Support for multiple facility types:
  - Restrooms
  - Dining areas
  - Gift shops
  - Information centers
  - First aid stations
  - Parking areas
- **Opening Hours Manager**: Interactive Livewire component for managing daily schedules
- **Location Management**: Map-based coordinate picker
- **Accessibility**: Wheelchair accessibility indicators
- **Contact Information**: Phone and email fields
- **Capacity Management**: Track facility capacity

### Activity Management
Event and activity scheduling system:
- **Activity Types**:
  - Feeding shows
  - Tours
  - Educational programs
  - Special events
- **Scheduling**: Start/end times with duration
- **Relations**: Link to animals or facilities
- **Booking**: Booking requirements and pricing
- **Status Tracking**: Scheduled, cancelled, or completed

### Map Editor
Interactive map editing tool:
- **Visual Tools**:
  - **Select Tool**: Pan and select existing elements
  - **Add Node**: Click to place waypoints on the map
  - **Draw Path**: Connect nodes to create pathways
  - **Upload Map**: Set background map image
- **Node Management**:
  - Different node types (waypoint, entrance, exit, POI)
  - Name and description
  - Visual representation on canvas
- **Path Management**:
  - Connect any two nodes
  - Set distance and accessibility
  - Visual path representation
- **Real-time Updates**: AJAX-based operations for instant feedback

## Technology Stack

- **Framework**: Laravel 12
- **Admin Theme**: AdminLTE 3.2
- **Interactive Components**: Livewire 3.6
- **Maps**: Leaflet.js 1.9
- **Frontend**: 
  - Bootstrap 4.6
  - Font Awesome 6.4
  - jQuery 3.6
- **Database**: SQLite (development), MySQL (production)
- **PHP**: 8.2+

## Routes

### Web Routes
- `GET /` - Redirects to admin dashboard
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/animals` - Animals list
- `GET /admin/animals/create` - Create animal form
- `GET /admin/animals/{id}/edit` - Edit animal form
- `POST /admin/animals` - Store new animal
- `PUT /admin/animals/{id}` - Update animal
- `DELETE /admin/animals/{id}` - Delete animal
- `POST /admin/animals/bulk-delete` - Bulk delete animals
- `POST /admin/animals/bulk-update-status` - Bulk update status
- Similar routes exist for facilities and activities
- `GET /admin/map-editor` - Map editor interface
- `POST /admin/map-editor/nodes` - Create node
- `POST /admin/map-editor/paths` - Create path
- `POST /admin/map-editor/upload-map` - Upload map image

## File Structure

```
resources/
├── views/
│   ├── layouts/
│   │   └── admin.blade.php          # Main admin layout
│   ├── admin/
│   │   ├── dashboard.blade.php      # Dashboard view
│   │   ├── animals/
│   │   │   ├── index.blade.php      # Animals list
│   │   │   ├── create.blade.php     # Create animal
│   │   │   └── edit.blade.php       # Edit animal
│   │   ├── facilities/
│   │   │   ├── index.blade.php      # Facilities list
│   │   │   ├── create.blade.php     # Create facility
│   │   │   └── edit.blade.php       # Edit facility
│   │   ├── activities/
│   │   │   ├── index.blade.php      # Activities list
│   │   │   ├── create.blade.php     # Create activity
│   │   │   └── edit.blade.php       # Edit activity
│   │   └── map-editor/
│   │       └── index.blade.php      # Map editor
│   └── livewire/
│       └── opening-hours-manager.blade.php  # Hours component
app/
├── Http/
│   └── Controllers/
│       └── Admin/
│           ├── DashboardController.php
│           ├── AnimalController.php
│           ├── FacilityController.php
│           ├── ActivityController.php
│           └── MapEditorController.php
└── Livewire/
    └── OpeningHoursManager.php
```

## Usage

### Starting the Server
```bash
cd tripoli-central-zoo/zoo-admin-backend
php artisan serve
```

Access the admin panel at: `http://localhost:8000/admin/dashboard`

### Creating an Animal
1. Navigate to Animals → Add New Animal
2. Fill in required fields (name, species, category)
3. Upload an image (optional)
4. Click on the map to set location
5. Add additional details as needed
6. Click "Create Animal"

### Managing Facilities
1. Navigate to Facilities → Add New Facility
2. Select facility type
3. Configure opening hours using the interactive manager
4. Set location on map
5. Save facility

### Using the Map Editor
1. Navigate to Map Editor
2. Upload a base map image (optional)
3. Select "Add Node" tool and click on map to place nodes
4. Select "Draw Path" tool and click two nodes to connect them
5. Nodes and paths are saved automatically

## Security Features

- **CSRF Protection**: All forms include CSRF tokens
- **Input Validation**: Server-side validation on all inputs
- **Image Validation**: File type and size restrictions
- **SQL Injection Protection**: Eloquent ORM prevents SQL injection
- **XSS Protection**: Blade template escaping
- **File Upload Security**: Restricted file types and storage location

## Configuration

### Environment Variables
```env
APP_NAME="Tripoli Zoo Admin"
APP_URL=http://localhost:8000
DB_CONNECTION=sqlite
```

### Storage
Images are stored in `storage/app/public/` and symlinked to `public/storage/`:
- Animals: `storage/app/public/animals/`
- Facilities: `storage/app/public/facilities/`
- Maps: `storage/app/public/maps/`

## Future Enhancements

- User authentication and roles
- Activity calendar view
- Advanced reporting and analytics
- Bulk import/export functionality
- Multi-language support
- Mobile app integration
- Real-time notifications
- Audit logging
- Advanced search and filtering

## Support

For issues or questions, please refer to the main project README or contact the development team.
