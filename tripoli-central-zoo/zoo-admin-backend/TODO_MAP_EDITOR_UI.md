# TODO: Map Editor UI Updates

## Completed ✅
- [x] Database schema: Add polymorphic columns to map_nodes
- [x] Models: Add relationships (MapNode::placeable(), Animal::mapNode(), Facility::mapNode())  
- [x] Controller: Load unmapped animals/facilities
- [x] Routes: Add unmapped places endpoint
- [x] Forms: Remove location fields from Animal/Facility create/edit
- [x] Forms: Add map status indicators

## Remaining Tasks 🔨

### 1. Place Selection UI
**File**: `resources/views/admin/map-editor/index.blade.php`

Add place selection panel to the sidebar:
```html
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add Place to Map</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Select Place Type:</label>
            <select id="placeType" class="form-control">
                <option value="">-- Select Type --</option>
                <option value="animal">Animal</option>
                <option value="facility">Facility</option>
                <option value="waypoint">Waypoint (No Place)</option>
            </select>
        </div>
        
        <div class="form-group" id="animalSelect" style="display:none;">
            <label>Select Animal:</label>
            <select id="animalId" class="form-control">
                <option value="">-- Select Animal --</option>
                @foreach($unmappedAnimals as $animal)
                    <option value="{{ $animal->id }}">{{ $animal->name }} ({{ $animal->species }})</option>
                @endforeach
            </select>
        </div>
        
        <div class="form-group" id="facilitySelect" style="display:none;">
            <label>Select Facility:</label>
            <select id="facilityId" class="form-control">
                <option value="">-- Select Facility --</option>
                @foreach($unmappedFacilities as $facility)
                    <option value="{{ $facility->id }}">{{ $facility->name }} ({{ $facility->type }})</option>
                @endforeach
            </select>
        </div>
        
        <button type="button" class="btn btn-primary" onclick="setAddPlaceMode()">
            <i class="fas fa-map-marker-alt"></i> Add to Map
        </button>
    </div>
</div>
```

JavaScript:
```javascript
$('#placeType').on('change', function() {
    const type = $(this).val();
    $('#animalSelect, #facilitySelect').hide();
    if (type === 'animal') {
        $('#animalSelect').show();
    } else if (type === 'facility') {
        $('#facilitySelect').show();
    }
});

function setAddPlaceMode() {
    const placeType = $('#placeType').val();
    if (!placeType) {
        alert('Please select a place type');
        return;
    }
    
    if (placeType !== 'waypoint') {
        const placeId = placeType === 'animal' 
            ? $('#animalId').val() 
            : $('#facilityId').val();
        
        if (!placeId) {
            alert(`Please select a ${placeType}`);
            return;
        }
        
        pendingPlace = {
            type: placeType,
            id: placeId
        };
    }
    
    setTool('add_node');
    alert('Click on the map to place this location');
}
```

Update `addNodeAtPosition()` to include place data:
```javascript
function addNodeAtPosition(latlng) {
    let nodeData = {
        x: latlng.lat,
        y: latlng.lng,
        type: $('#nodeType').val() || 'waypoint'
    };
    
    // Add place data if selected
    if (pendingPlace) {
        nodeData.placeable_type = pendingPlace.type;
        nodeData.placeable_id = pendingPlace.id;
    }
    
    $.ajax({
        url: '{{ route("admin.map-editor.nodes.store") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            ...nodeData
        },
        success: function(response) {
            // Add node to map
            const node = response.node;
            const marker = L.marker([node.x, node.y], {
                icon: getNodeIcon(node),
                draggable: true // Enable dragging
            }).addTo(map);
            
            marker.nodeId = node.id;
            marker.on('dragend', handleNodeDragEnd);
            marker.on('click', () => selectNode(node.id));
            
            // Reset pending place
            pendingPlace = null;
            $('#placeType').val('');
            $('#animalSelect, #facilitySelect').hide();
        }
    });
}
```

### 2. Drag-to-Edit Nodes
**File**: `resources/views/admin/map-editor/index.blade.php`

Make existing node markers draggable:
```javascript
// When loading existing nodes
nodes.forEach(node => {
    const marker = L.marker([node.x, node.y], {
        icon: getNodeIcon(node),
        draggable: true // Make draggable
    }).addTo(map);
    
    marker.nodeId = node.id;
    marker.on('dragend', handleNodeDragEnd);
    marker.on('click', () => selectNode(node.id));
    nodesLayer.addLayer(marker);
});

// Handle drag end
function handleNodeDragEnd(event) {
    const marker = event.target;
    const newLatLng = marker.getLatLng();
    
    $.ajax({
        url: `/admin/map-editor/nodes/${marker.nodeId}`,
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            x: newLatLng.lat,
            y: newLatLng.lng
        },
        success: function(response) {
            showNotification('Node position updated', 'success');
        },
        error: function() {
            // Revert position on error
            marker.setLatLng([originalPosition.lat, originalPosition.lng]);
            showNotification('Failed to update node position', 'error');
        }
    });
}
```

### 3. Place Information Display
**File**: `resources/views/admin/map-editor/index.blade.php`

Show place details when node is selected:
```javascript
function selectNode(nodeId) {
    const node = allNodes.find(n => n.id === nodeId);
    if (!node) return;
    
    selectedNode = node;
    
    // Update info panel
    let html = `
        <h5>Node #${node.id}</h5>
        <p><strong>Type:</strong> ${node.type}</p>
        <p><strong>Coordinates:</strong> ${node.x.toFixed(6)}, ${node.y.toFixed(6)}</p>
    `;
    
    // Show place information if linked
    if (node.placeable) {
        html += `
            <hr>
            <h6>Linked Place</h6>
            <p><strong>Type:</strong> ${node.placeable_type === 'App\\Models\\Animal' ? 'Animal' : 'Facility'}</p>
            <p><strong>Name:</strong> ${node.placeable.name}</p>
        `;
        
        if (node.placeable.species) {
            html += `<p><strong>Species:</strong> ${node.placeable.species}</p>`;
        }
        
        if (node.placeable.type) {
            html += `<p><strong>Facility Type:</strong> ${node.placeable.type}</p>`;
        }
        
        if (node.placeable.image_url) {
            html += `<img src="${node.placeable.image_url}" class="img-thumbnail mt-2" style="max-width: 100%;">`;
        }
    }
    
    html += `
        <hr>
        <button class="btn btn-danger btn-sm" onclick="deleteNode(${node.id})">
            <i class="fas fa-trash"></i> Delete Node
        </button>
    `;
    
    $('#nodeInfo').html(html);
}
```

### 4. Node Icons by Type
```javascript
function getNodeIcon(node) {
    let iconColor, iconSymbol;
    
    if (node.placeable_type === 'App\\Models\\Animal') {
        iconColor = 'green';
        iconSymbol = 'paw';
    } else if (node.placeable_type === 'App\\Models\\Facility') {
        iconColor = 'blue';
        iconSymbol = 'building';
    } else {
        // Waypoint
        iconColor = 'gray';
        iconSymbol = 'circle';
    }
    
    return L.divIcon({
        className: 'custom-node-icon',
        html: `<div style="background: ${iconColor};" class="node-marker">
                 <i class="fas fa-${iconSymbol}"></i>
               </div>`,
        iconSize: [30, 30]
    });
}
```

### 5. Refresh Unmapped Lists
After adding a place to the map, remove it from the unmapped list:
```javascript
function refreshUnmappedLists() {
    $.get('{{ route("admin.map-editor.unmapped-places") }}', function(data) {
        // Update animal select
        let animalOptions = '<option value="">-- Select Animal --</option>';
        data.animals.forEach(animal => {
            animalOptions += `<option value="${animal.id}">${animal.name} (${animal.species})</option>`;
        });
        $('#animalId').html(animalOptions);
        
        // Update facility select
        let facilityOptions = '<option value="">-- Select Facility --</option>';
        data.facilities.forEach(facility => {
            facilityOptions += `<option value="${facility.id}">${facility.name} (${facility.type})</option>`;
        });
        $('#facilityId').html(facilityOptions);
    });
}
```

## Testing Checklist
- [ ] Create new animal → appears in unmapped animals list
- [ ] Select animal from list → click map → animal placed with correct icon
- [ ] Drag node → position updates in database
- [ ] Click node → shows animal information in sidebar
- [ ] Delete node → animal reappears in unmapped list
- [ ] Create facility → same workflow as animal
- [ ] Create waypoint (no place) → gray icon, no place info
- [ ] Refresh page → all nodes persist with correct place links

## Notes
- Node markers should be draggable by default in edit mode
- Use different colored icons for animals (green) vs facilities (blue) vs waypoints (gray)
- Show place thumbnail image in node popup
- Update Quick Guide to explain new workflow
- Consider adding search/filter for large lists of unmapped places
