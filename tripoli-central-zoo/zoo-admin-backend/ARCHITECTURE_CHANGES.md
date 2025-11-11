# Architecture Changes: Map Editor Refactoring

## Problem Statement

The previous implementation had several issues:
1. **Duplicate Data Entry**: Animals and Facilities stored coordinates (`location_x`, `location_y`) directly, requiring admins to enter location data twice (once in entity form, once on map)
2. **Disconnected Systems**: Map nodes were standalone entities with no relationship to Animals or Facilities
3. **No Place Information**: Visitors couldn't see what each map node represented
4. **Poor UX**: No ability to edit node positions after creation

## Solution: Places-Based Architecture

### Core Concept
- **Places First, Map Second**: Create Animals/Facilities first, then map them on the zoo map
- **Single Source of Truth**: Map nodes reference Places (Animals/Facilities) via polymorphic relationship
- **Unified Visitor Experience**: Each map marker shows Place information (name, description, image, etc.)

### Database Changes

#### New Polymorphic Relationship
```sql
ALTER TABLE map_nodes ADD COLUMN placeable_type VARCHAR(255);
ALTER TABLE map_nodes ADD COLUMN placeable_id BIGINT;
CREATE INDEX idx_placeable ON map_nodes(placeable_type, placeable_id);
```

#### Schema
```
map_nodes:
  - id
  - x (latitude)
  - y (longitude)
  - type (waypoint, entrance, exit, poi)
  - placeable_type (App\Models\Animal or App\Models\Facility) - NEW
  - placeable_id (references animals.id or facilities.id) - NEW
  - name (optional, for waypoints without places)
  - description
```

### Model Changes

#### MapNode Model
```php
class MapNode extends Model
{
    // Polymorphic relationship to Animal or Facility
    public function placeable(): MorphTo
    {
        return $this->morphTo();
    }
    
    // Helper: Get display name (from place or fallback)
    public function getDisplayNameAttribute(): string
    {
        return $this->placeable ? $this->placeable->name : $this->name ?? "Node #{$this->id}";
    }
    
    // Helper: Check if node is a place
    public function getIsPlaceAttribute(): bool
    {
        return !is_null($this->placeable_type) && !is_null($this->placeable_id);
    }
}
```

#### Animal/Facility Models
```php
class Animal extends Model
{
    // Inverse relationship
    public function mapNode()
    {
        return $this->morphOne(MapNode::class, 'placeable');
    }
    
    // Helper: Check if mapped
    public function getIsMappedAttribute(): bool
    {
        return $this->mapNode()->exists();
    }
}
```

### Controller Changes

#### MapEditorController
```php
public function index()
{
    // Load nodes with their associated places
    $nodes = MapNode::with('placeable')->get();
    
    // Get unmapped animals and facilities for selection
    $unmappedAnimals = Animal::whereDoesntHave('mapNode')->where('status', 'active')->get();
    $unmappedFacilities = Facility::whereDoesntHave('mapNode')->where('status', 'open')->get();
    
    return view('admin.map-editor.index', compact('nodes', '...', 'unmappedAnimals', 'unmappedFacilities'));
}

public function storeNode(Request $request)
{
    $validated = $request->validate([
        'x' => 'required|numeric',
        'y' => 'required|numeric',
        'type' => 'required|string',
        'placeable_type' => 'nullable|string|in:animal,facility',
        'placeable_id' => 'nullable|integer',
        // ...
    ]);
    
    // Convert short names to full class names
    if (!empty($validated['placeable_type'])) {
        $validated['placeable_type'] = $validated['placeable_type'] === 'animal' 
            ? Animal::class 
            : Facility::class;
    }
    
    $node = MapNode::create($validated);
    return response()->json(['success' => true, 'node' => $node->load('placeable')]);
}
```

### View Changes

#### Animal/Facility Create Forms
**Before:**
- Had `location_x` and `location_y` input fields
- Included interactive Leaflet map for coordinate selection
- Required admin to enter coordinates during creation

**After:**
- Removed location input fields
- Removed map preview
- Added info alert: "After creating this animal, you can place it on the zoo map using the Map Editor"
- Cleaner, simpler form focused on entity properties

#### Animal/Facility Edit Forms
**Before:**
- Same as create forms - location fields and map

**After:**
- Shows mapping status: "Mapped" or "Not Mapped"
- Link to Map Editor to add or edit location
- Example:
  ```php
  @if($animal->isMapped)
      <div class="alert alert-success">
          <i class="fas fa-map-marker-alt"></i>
          <strong>Mapped:</strong> This animal is placed on the zoo map. 
          <a href="{{ route('admin.map-editor.index') }}">Edit map location</a>
      </div>
  @else
      <div class="alert alert-info">
          <i class="fas fa-info-circle"></i>
          <strong>Not Mapped:</strong> This animal hasn't been placed on the zoo map yet. 
          <a href="{{ route('admin.map-editor.index') }}">Add to map</a>
      </div>
  @endif
  ```

#### Map Editor View
**NEW Features:**
1. **Place Selection Panel**
   - Dropdown/list showing unmapped animals
   - Dropdown/list showing unmapped facilities
   - When adding node, select place to link

2. **Node Editing**
   - Click existing node to select
   - Drag node to new position (updates coordinates)
   - Edit panel shows place information if linked

3. **Visual Indicators**
   - Different icons for animals vs facilities
   - Color coding for mapped vs unmapped
   - Place name displayed on node label

## Workflow Comparison

### Old Workflow (Problematic)
1. Admin creates Animal with name, species, description, etc.
2. Admin enters latitude/longitude in Animal form
3. Admin clicks on map to set coordinates
4. Admin saves Animal
5. Admin goes to Map Editor
6. Admin manually creates MapNode at same coordinates
7. **Problem**: Two separate entities with duplicate location data

### New Workflow (Improved)
1. Admin creates Animal with name, species, description, etc.
2. Admin saves Animal (no location fields)
3. Admin goes to Map Editor
4. Admin clicks "Add Node" → Selects "Animal" from unmapped list
5. Admin clicks on map to place the animal
6. **Result**: One MapNode linked to Animal via polymorphic relationship

## Benefits

### 1. Single Data Entry
- Admin creates place (animal/facility) first
- Then maps it once in Map Editor
- No duplicate coordinate entry

### 2. Unified Information
- Mobile app can fetch node + place data in one query:
  ```sql
  SELECT nodes.*, animals.* 
  FROM map_nodes nodes
  LEFT JOIN animals ON nodes.placeable_type = 'App\\Models\\Animal' AND nodes.placeable_id = animals.id
  ```
- Each map marker has rich information (name, image, description, hours, etc.)

### 3. Easier Management
- See which animals/facilities are not yet mapped
- Edit place details without touching map
- Edit map position without affecting place
- Delete place auto-removes from map (optional cascade)

### 4. Better Mobile Experience
- Tap map node → See full place information
- Navigate to specific animal/facility
- View photos, feeding times, etc.
- Consistent data between admin and mobile

## Mobile App Integration

### API Endpoint
```javascript
GET /api/map-editor/data

Response:
{
  "nodes": [
    {
      "id": 1,
      "x": 32.8872,
      "y": 13.1913,
      "type": "poi",
      "placeable_type": "App\\Models\\Animal",
      "placeable_id": 5,
      "placeable": {
        "id": 5,
        "name": "African Lion",
        "species": "Panthera leo",
        "image_url": "/storage/animals/lion.jpg",
        "description": "...",
        "feeding_times": ["10:00", "16:00"]
      }
    },
    {
      "id": 2,
      "x": 32.8875,
      "y": 13.1915,
      "type": "poi",
      "placeable_type": "App\\Models\\Facility",
      "placeable_id": 3,
      "placeable": {
        "id": 3,
        "name": "Snack Bar",
        "type": "dining",
        "opening_hours": {...},
        "amenities": ["WiFi", "Outdoor Seating"]
      }
    }
  ],
  "paths": [...],
  "mapBounds": [[32.88, 13.18], [32.9, 13.2]]
}
```

### Mobile Usage
```kotlin
// Android example
nodes.forEach { node ->
    val marker = when (node.placeable_type) {
        "App\\Models\\Animal" -> {
            map.addMarker(
                position = LatLng(node.x, node.y),
                title = node.placeable.name,
                snippet = node.placeable.species,
                icon = BitmapDescriptorFactory.fromResource(R.drawable.ic_animal)
            )
        }
        "App\\Models\\Facility" -> {
            map.addMarker(
                position = LatLng(node.x, node.y),
                title = node.placeable.name,
                snippet = node.placeable.type,
                icon = BitmapDescriptorFactory.fromResource(R.drawable.ic_facility)
            )
        }
        else -> {
            // Waypoint (no place attached)
            map.addMarker(
                position = LatLng(node.x, node.y),
                title = node.name ?: "Waypoint",
                icon = BitmapDescriptorFactory.fromResource(R.drawable.ic_waypoint)
            )
        }
    }
}
```

## Migration Path

### For Existing Data
If you have existing animals/facilities with `location_x` and `location_y`:

```php
// Migration script to convert existing data
$animals = Animal::whereNotNull('location_x')->whereNotNull('location_y')->get();

foreach ($animals as $animal) {
    MapNode::create([
        'x' => $animal->location_x,
        'y' => $animal->location_y,
        'type' => 'poi',
        'placeable_type' => Animal::class,
        'placeable_id' => $animal->id,
        'name' => $animal->name,
    ]);
}

// Repeat for facilities
```

## Future Enhancements

1. **Activity Integration**: Add activities to map nodes (feeding shows, tours)
2. **Multi-Level Maps**: Support indoor/outdoor, multiple floors
3. **Real-Time Updates**: Push notifications when place information changes
4. **Visitor Routing**: Calculate shortest path between two places
5. **Accessibility Routing**: Routes for wheelchair users
6. **AR Integration**: Overlay place information using phone camera

## Conclusion

This refactoring provides a clean separation of concerns:
- **Place Management**: CRUD for animals/facilities (attributes, images, descriptions)
- **Map Management**: Visual placement and navigation (coordinates, paths, routing)
- **Mobile Integration**: Unified API providing place information at geographic locations

The result is less duplicate data entry, easier management, and a better experience for both admins and zoo visitors.
