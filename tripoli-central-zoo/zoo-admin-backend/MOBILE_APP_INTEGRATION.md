# Mobile App Integration Guide

## Overview

The map editor now provides interactive calibration tools that allow you to precisely align your zoo floor plan or aerial imagery with real-world GPS coordinates. This enables accurate indoor/outdoor navigation and location tracking in your mobile application.

## Interactive Calibration Tool

### Features

1. **4 Draggable Corner Markers** - Visual alignment of map image
2. **Real-Time Coordinate Updates** - See coordinates change as you drag
3. **Opacity Control** - Adjust image transparency to see underlying OpenStreetMap
4. **Export to JSON** - Download calibration data for mobile app

### How to Calibrate Your Map

#### Step 1: Upload Your Map
1. Click "Upload Map" button
2. Select your zoo floor plan or aerial image
3. The image will appear on the map canvas

#### Step 2: Enter Calibration Mode
1. Click the "Calibrate" button in the toolbar
2. You will see 4 red circular markers appear at the corners:
   - **NW** (Northwest - Top-Left)
   - **NE** (Northeast - Top-Right)
   - **SW** (Southwest - Bottom-Left)
   - **SE** (Southeast - Bottom-Right)

#### Step 3: Align the Map Image
1. **Adjust Opacity**: Use the slider to make the image semi-transparent (50-70%) so you can see the OpenStreetMap underneath
2. **Identify Reference Points**: Find landmarks on both your image and the base map:
   - Building corners
   - Gates/Entrances
   - Major pathways
   - Distinctive features
3. **Drag Corner Markers**: 
   - Click and hold a red corner marker
   - Drag it to align with the corresponding corner of your zoo area on the base map
   - The image will resize and reposition in real-time
   - Repeat for all 4 corners
4. **Fine-Tune**: Make small adjustments until the image perfectly overlays your zoo area

#### Step 4: Verify Alignment
- Check that major features on your image align with features on the base map
- All corner coordinates are displayed in the sidebar
- Center point is automatically calculated

#### Step 5: Save Calibration
1. Click "Save Calibration" button
2. Calibration is saved to the database
3. Map will stay aligned even if you resize the browser window

#### Step 6: Export for Mobile App
1. Click "Export for Mobile App" button
2. A JSON file `zoo-map-calibration.json` will download
3. This file contains all coordinates needed for mobile integration

## Exported Data Format

The exported JSON file contains:

```json
{
  "mapImageUrl": "/storage/maps/zoo-plan.png",
  "bounds": {
    "southwest": {
      "latitude": 32.88,
      "longitude": 13.18
    },
    "northeast": {
      "latitude": 32.90,
      "longitude": 13.20
    }
  },
  "corners": {
    "northwest": {
      "latitude": 32.90,
      "longitude": 13.18
    },
    "northeast": {
      "latitude": 32.90,
      "longitude": 13.20
    },
    "southwest": {
      "latitude": 32.88,
      "longitude": 13.18
    },
    "southeast": {
      "latitude": 32.88,
      "longitude": 13.20
    }
  },
  "center": {
    "latitude": 32.89,
    "longitude": 13.19
  },
  "exportDate": "2025-11-11T17:00:00.000Z",
  "zooName": "Tripoli Central Zoo"
}
```

## Mobile App Integration

### Android (Kotlin) Example

```kotlin
// Load calibration data
val calibrationData = loadCalibrationFromAssets()

// Create overlay on Google Maps
val bounds = LatLngBounds(
    LatLng(calibrationData.bounds.southwest.latitude, 
           calibrationData.bounds.southwest.longitude),
    LatLng(calibrationData.bounds.northeast.latitude, 
           calibrationData.bounds.northeast.longitude)
)

val overlay = GroundOverlayOptions()
    .image(BitmapDescriptorFactory.fromResource(R.drawable.zoo_map))
    .positionFromBounds(bounds)
    .transparency(0.2f)

googleMap.addGroundOverlay(overlay)
```

### iOS (Swift) Example

```swift
// Load calibration data
let calibrationData = loadCalibrationData()

// Create overlay on Apple Maps
let topLeft = CLLocationCoordinate2D(
    latitude: calibrationData.corners.northwest.latitude,
    longitude: calibrationData.corners.northwest.longitude
)
let bottomRight = CLLocationCoordinate2D(
    latitude: calibrationData.corners.southeast.latitude,
    longitude: calibrationData.corners.southeast.longitude
)

let overlay = MKMapRect(
    origin: MKMapPoint(topLeft),
    size: MKMapSize(
        width: MKMapPoint(bottomRight).x - MKMapPoint(topLeft).x,
        height: MKMapPoint(bottomRight).y - MKMapPoint(topLeft).y
    )
)

let mapOverlay = CustomImageOverlay(
    coordinate: calibrationData.center,
    boundingMapRect: overlay
)

mapView.addOverlay(mapOverlay)
```

### React Native Example

```javascript
import MapView, { GroundOverlay } from 'react-native-maps';

const calibrationData = require('./zoo-map-calibration.json');

<MapView>
  <GroundOverlay
    image={require('./zoo_map.png')}
    bounds={[
      [calibrationData.bounds.southwest.latitude, 
       calibrationData.bounds.southwest.longitude],
      [calibrationData.bounds.northeast.latitude, 
       calibrationData.bounds.northeast.longitude]
    ]}
    opacity={0.8}
  />
</MapView>
```

## GPS Tracking and Navigation

### User Location on Calibrated Map

With the calibrated coordinates, you can accurately show the user's GPS location on your zoo map:

```javascript
// Check if user is within zoo bounds
function isUserInZoo(userLat, userLng) {
  const bounds = calibrationData.bounds;
  return (
    userLat >= bounds.southwest.latitude &&
    userLat <= bounds.northeast.latitude &&
    userLng >= bounds.southwest.longitude &&
    userLng <= bounds.northeast.longitude
  );
}

// Convert GPS to pixel position on map image
function gpsToImagePosition(lat, lng) {
  const bounds = calibrationData.bounds;
  const imageWidth = 1000; // your image width in pixels
  const imageHeight = 800; // your image height in pixels
  
  const x = ((lng - bounds.southwest.longitude) / 
             (bounds.northeast.longitude - bounds.southwest.longitude)) * imageWidth;
  const y = ((bounds.northeast.latitude - lat) / 
             (bounds.northeast.latitude - bounds.southwest.latitude)) * imageHeight;
  
  return { x, y };
}
```

### Navigation Between Points

Use the node coordinates (also available via API) to provide turn-by-turn navigation:

```javascript
// Fetch nodes from API
const nodes = await fetch('/api/map-editor/data').then(r => r.json()).nodes;

// Find nearest node to user
function findNearestNode(userLat, userLng) {
  let nearest = null;
  let minDistance = Infinity;
  
  nodes.forEach(node => {
    const distance = calculateDistance(userLat, userLng, node.x, node.y);
    if (distance < minDistance) {
      minDistance = distance;
      nearest = node;
    }
  });
  
  return nearest;
}

// Calculate route using A* or Dijkstra with path network
```

## API Endpoints for Mobile App

### Get Map Configuration
```
GET /api/map-editor/data
```

Response:
```json
{
  "nodes": [...],
  "paths": [...],
  "locations": [...],
  "mapBounds": [[32.88, 13.18], [32.9, 13.2]]
}
```

### Get Map Image
```
GET /storage/maps/zoo-plan.png
```

## Best Practices

### 1. Calibration Accuracy
- Use high-resolution aerial imagery if available
- Calibrate during daytime for better visibility
- Verify alignment with multiple reference points
- Test with GPS device at known locations

### 2. Mobile Performance
- Optimize map image size (recommended: 2048x2048px max)
- Use appropriate compression (JPEG for photos, PNG for diagrams)
- Cache calibration data locally
- Update only when calibration changes

### 3. User Experience
- Show calibration date to users
- Provide manual recalibration if GPS accuracy is poor
- Display accuracy indicator based on GPS signal strength
- Gracefully degrade to base map if overlay unavailable

### 4. Testing
- Test at zoo entrance (known GPS coordinates)
- Walk path and verify alignment
- Test in various lighting conditions
- Verify indoor positioning if applicable

## Troubleshooting

### Map Image Not Aligning
- **Problem**: Image doesn't match the zoo area
- **Solution**: Recalibrate by dragging corner markers more carefully
- **Tip**: Use satellite view as base layer for better alignment

### GPS Inaccurate Indoors
- **Problem**: GPS signal weak inside buildings
- **Solution**: Use WiFi/Bluetooth beacons for indoor positioning
- **Alternative**: Provide manual "I'm here" markers at key locations

### Image Too Large
- **Problem**: Map image file size too big for mobile
- **Solution**: Optimize image before upload (compress, resize)
- **Tool**: Use ImageOptim, TinyPNG, or similar tools

### Coordinates Shifted
- **Problem**: User location appears offset from actual position
- **Solution**: Verify corner markers are at exact corners
- **Check**: Ensure GPS coordinates are in correct format (decimal degrees)

## Support

For additional help:
- Review `MAP_EDITOR_GUIDE.md` for detailed calibration instructions
- Check `ADMIN_PANEL.md` for general admin panel documentation
- Contact zoo IT support for GPS coordinate verification

## Updates

When you update your zoo map (new buildings, paths):
1. Upload new map image
2. Re-calibrate using the same process
3. Export new calibration JSON
4. Update mobile app with new data
5. Notify users to download updated map
