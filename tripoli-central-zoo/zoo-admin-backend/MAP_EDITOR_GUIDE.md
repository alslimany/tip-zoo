# Map Editor - Geographic Coordinate System

## Overview

The map editor has been completely refactored to use a geographic coordinate system based on Leaflet.js with ImageOverlay. This ensures that node positions remain accurate regardless of window resize, similar to professional indoor mapping applications like the Nashville Zoo app.

## Key Changes

### Previous Implementation (Pixel-Based)
- Nodes positioned using absolute pixel coordinates
- Image displayed with `object-fit: contain`
- **Problem**: When window resized, image scaled but pixel positions didn't adjust, causing misalignment

### New Implementation (Geographic Coordinates)
- Nodes use latitude/longitude coordinates
- Map image georeferenced as Leaflet ImageOverlay
- Geographic bounds define image placement
- **Solution**: Positions remain accurate on any window size

## How It Works

### 1. Geographic Coordinate System

Every node is stored with geographic coordinates:
```php
// Example node in database
{
    "id": 1,
    "x": 32.8872,  // Latitude
    "y": 13.1913,  // Longitude
    "type": "entrance",
    "name": "Main Entrance"
}
```

### 2. Map Image as Georeferenced Layer

The uploaded map image is displayed as a Leaflet ImageOverlay with defined bounds:

```javascript
// Map bounds define the geographic area covered by your image
let mapBounds = [
    [32.88, 13.18],  // Southwest corner [lat, lng]
    [32.9, 13.2]      // Northeast corner [lat, lng]
];

// Image overlay maintains these bounds regardless of zoom/resize
imageOverlay = L.imageOverlay(imageUrl, mapBounds, {
    opacity: 0.8,
    interactive: false
}).addTo(map);
```

### 3. Calibration Tool

The calibration tool allows you to set accurate geographic bounds for your map:

1. Upload your zoo floor plan or aerial image
2. Click "Calibrate" button
3. Enter the four bounds:
   - **North Latitude**: Top edge of your map
   - **South Latitude**: Bottom edge of your map
   - **East Longitude**: Right edge of your map
   - **West Longitude**: Left edge of your map
4. Click "Apply Calibration"

The bounds are saved to the database and persist across sessions.

## Usage Guide

### Setting Up Your Map

1. **Upload Map Image**
   - Click "Upload Map" button
   - Select your zoo floor plan (JPG, PNG, or SVG)
   - Image is saved and displayed on the map

2. **Calibrate Geographic Bounds**
   - Click "Calibrate" button
   - Set the four corner coordinates
   - These define the real-world area your map represents
   - Example for Tripoli Central Zoo:
     - North: 32.9
     - South: 32.88
     - East: 13.2
     - West: 13.18
   - Click "Apply Calibration"

3. **Add Nodes**
   - Click "Add Node" tool
   - Click anywhere on the map
   - Node is created with geographic coordinates
   - Edit node properties (type, name, description)

4. **Draw Paths**
   - Click "Draw Path" tool
   - Click first node
   - Click second node
   - Path is created connecting the nodes
   - Set distance and accessibility

### Node Types

- **Waypoint** (Blue): Path intersection points for navigation
- **Entrance** (Green): Zoo entry points
- **Exit** (Yellow): Zoo exit points
- **POI** (Cyan): Points of Interest (animal exhibits, facilities)

### Benefits of Geographic System

1. **Resize Safety**: Positions remain accurate when window resizes
2. **Zoom Consistency**: Nodes stay in correct positions at any zoom level
3. **Real Coordinates**: Can integrate with GPS if outdoor mapping
4. **Professional**: Same approach used by Nashville Zoo and other professional apps
5. **Accurate Routing**: Distance calculations based on real coordinates

## Technical Details

### Database Schema

Nodes table stores geographic coordinates:
```sql
CREATE TABLE map_nodes (
    id BIGINT PRIMARY KEY,
    x DECIMAL(10, 6),  -- Latitude
    y DECIMAL(10, 6),  -- Longitude
    type VARCHAR(50),
    name VARCHAR(255),
    description TEXT
);
```

### Map Settings

The `map_settings` table stores configuration:
```sql
INSERT INTO map_settings (key, value) VALUES
('map_background_image', '/storage/maps/zoo-plan.png'),
('map_bounds', '[[32.88, 13.18], [32.9, 13.2]]');
```

### Leaflet.js Integration

```javascript
// Initialize map
map = L.map('mapEditor', {
    center: [32.8872, 13.1913],
    zoom: 16,
    minZoom: 14,
    maxZoom: 20
});

// Add base layer (OpenStreetMap)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Add georeferenced image
imageOverlay = L.imageOverlay(imageUrl, bounds).addTo(map);

// Add node markers
nodes.forEach(node => {
    L.marker([node.x, node.y], {
        icon: customIcon
    }).addTo(map);
});
```

## API Endpoints

### Store Node
```
POST /admin/map-editor/nodes
{
    "x": 32.8872,
    "y": 13.1913,
    "type": "entrance",
    "name": "Main Entrance"
}
```

### Upload Map & Save Bounds
```
POST /admin/map-editor/upload-map
FormData: map_image (file)

POST /admin/map-editor/upload-map
{
    "bounds": "[[32.88, 13.18], [32.9, 13.2]]"
}
```

## Best Practices

1. **Always Calibrate**: After uploading a new map image, immediately calibrate the bounds
2. **Use Real Coordinates**: If possible, use actual GPS coordinates from your zoo
3. **Test Resize**: After placing nodes, resize the browser window to verify accuracy
4. **Save Often**: Changes are auto-saved, but verify after major edits
5. **Clear Naming**: Give nodes descriptive names for easier management

## Troubleshooting

### Nodes appear in wrong positions after resize
- **Solution**: Re-calibrate the map bounds using the Calibrate tool

### Map image doesn't appear
- **Solution**: Check that the image uploaded successfully and bounds are set

### Can't click on map to add nodes
- **Solution**: Ensure "Add Node" tool is selected (button should be highlighted)

### Paths not drawing
- **Solution**: Make sure "Draw Path" tool is active and you're clicking on existing nodes

## Future Enhancements

- Import GeoJSON for bulk node creation
- Export map data to GeoJSON format
- Integration with mobile app via API
- Real-time collaboration for multiple editors
- Automatic route optimization
- Accessibility routing for wheelchair users
