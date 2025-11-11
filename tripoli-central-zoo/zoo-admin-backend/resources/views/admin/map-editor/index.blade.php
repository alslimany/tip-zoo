@extends('layouts.admin')

@section('title', 'Map Editor')
@section('page-title', 'Interactive Map Editor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Map Editor</li>
@endsection

@push('styles')
<style>
    #mapEditor {
        height: 600px;
        width: 100%;
        border: 2px solid #ddd;
        position: relative;
    }

    .leaflet-container {
        background: #f8f9fa;
        font-family: inherit;
    }

    .tool-btn {
        margin: 5px;
    }

    .tool-btn.active {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
        color: white !important;
    }

    /* Custom divIcon styles for different node types */
    .node-marker {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        cursor: pointer;
    }

    .node-marker.waypoint {
        background: #007bff;
    }

    .node-marker.entrance {
        background: #28a745;
    }

    .node-marker.exit {
        background: #ffc107;
    }

    .node-marker.poi {
        background: #17a2b8;
    }

    .node-marker.selected {
        background: #dc3545 !important;
        border-color: #c82333;
        box-shadow: 0 3px 8px rgba(220,53,69,0.5);
    }

    .node-label {
        background: rgba(255,255,255,0.9);
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 500;
        border: 1px solid #ddd;
        white-space: nowrap;
        margin-left: 20px;
        margin-top: -5px;
    }
    
    /* Calibration corner markers */
    .calibration-corner-marker {
        background: transparent;
        border: none;
    }
    
    .corner-marker-icon {
        width: 40px;
        height: 40px;
        background: #dc3545;
        border: 3px solid #fff;
        border-radius: 50%;
        box-shadow: 0 3px 10px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 10px;
        cursor: move;
        transition: all 0.2s;
    }
    
    .corner-marker-icon:hover {
        background: #c82333;
        transform: scale(1.2);
        box-shadow: 0 4px 15px rgba(220,53,69,0.6);
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Map Canvas</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-info tool-btn" id="panTool" title="Pan/Select">
                        <i class="fas fa-hand-paper"></i> Select
                    </button>
                    <button type="button" class="btn btn-sm btn-primary tool-btn" id="nodeTool" title="Add Node">
                        <i class="fas fa-map-marker-alt"></i> Add Node
                    </button>
                    <button type="button" class="btn btn-sm btn-success tool-btn" id="pathTool" title="Draw Path">
                        <i class="fas fa-route"></i> Draw Path
                    </button>
                    <button type="button" class="btn btn-sm btn-warning tool-btn" id="uploadMapBtn" title="Upload Map Image">
                        <i class="fas fa-upload"></i> Upload Map
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary tool-btn" id="calibrateBtn" title="Calibrate Map">
                        <i class="fas fa-crosshairs"></i> Calibrate
                    </button>
                    <button type="button" class="btn btn-sm btn-danger tool-btn" id="clearSelection" title="Clear Selection">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="mapEditor"></div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tools</h3>
            </div>
            <div class="card-body">
                <div id="toolInfo">
                    <p class="text-muted">Select a tool to begin editing the map.</p>
                </div>
                
                <div id="nodeForm" style="display: none;">
                    <h6>Node Properties</h6>
                    <div class="form-group">
                        <label>Type</label>
                        <select class="form-control form-control-sm" id="nodeType">
                            <option value="waypoint">Waypoint</option>
                            <option value="entrance">Entrance</option>
                            <option value="exit">Exit</option>
                            <option value="poi">Point of Interest</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control form-control-sm" id="nodeName">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control form-control-sm" id="nodeDescription" rows="2"></textarea>
                    </div>
                    <div class="alert alert-success alert-sm p-2 mb-2">
                        <i class="fas fa-arrows-alt"></i> <strong>Drag the marker</strong> on the map to reposition this node
                    </div>
                    <div class="form-group">
                        <label>Coordinates</label>
                        <input type="text" class="form-control form-control-sm" id="nodeCoords" readonly>
                        <small class="form-text text-muted">Updates automatically as you drag</small>
                    </div>
                    <button class="btn btn-sm btn-primary btn-block" id="saveNode">Save Node</button>
                    <button class="btn btn-sm btn-danger btn-block" id="deleteNode">Delete Node</button>
                </div>

                <div id="pathForm" style="display: none;">
                    <h6>Path Properties</h6>
                    <p class="text-muted small">Click two nodes to create a path</p>
                    <div class="form-group">
                        <label>Distance (meters)</label>
                        <input type="number" class="form-control form-control-sm" id="pathDistance" value="0">
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="pathAccessible" checked>
                            <label class="custom-control-label" for="pathAccessible">Accessible</label>
                        </div>
                    </div>
                </div>

                <div id="calibrateForm" style="display: none;">
                    <h6>Map Calibration</h6>
                    <p class="text-muted small">Drag corners to align map to zoo area</p>
                    
                    <div class="alert alert-info small p-2 mb-3">
                        <i class="fas fa-info-circle"></i> Drag the 4 corner markers on the map to align your image with the zoo area. Coordinates update automatically.
                    </div>
                    
                    <div class="form-group">
                        <label>Image Opacity</label>
                        <input type="range" class="custom-range" id="imageOpacity" min="0" max="100" value="80">
                        <small class="text-muted">Adjust to see underlying map</small>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group">
                        <label class="font-weight-bold">Corner Coordinates</label>
                    </div>
                    
                    <div class="form-group">
                        <label>NW Corner (Top-Left)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.000001" class="form-control" id="nwLat" readonly>
                            <input type="number" step="0.000001" class="form-control" id="nwLng" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>NE Corner (Top-Right)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.000001" class="form-control" id="neLat" readonly>
                            <input type="number" step="0.000001" class="form-control" id="neLng" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>SW Corner (Bottom-Left)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.000001" class="form-control" id="swLat" readonly>
                            <input type="number" step="0.000001" class="form-control" id="swLng" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>SE Corner (Bottom-Right)</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.000001" class="form-control" id="seLat" readonly>
                            <input type="number" step="0.000001" class="form-control" id="seLng" readonly>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group">
                        <label>Center Point</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.000001" class="form-control" id="centerLat" readonly>
                            <input type="number" step="0.000001" class="form-control" id="centerLng" readonly>
                        </div>
                    </div>
                    
                    <button class="btn btn-sm btn-success btn-block" id="applyCalibration">
                        <i class="fas fa-save"></i> Save Calibration
                    </button>
                    <button class="btn btn-sm btn-secondary btn-block" id="resetCalibration">
                        <i class="fas fa-undo"></i> Reset to Zoo Center
                    </button>
                    <button class="btn btn-sm btn-info btn-block" id="exportCalibration">
                        <i class="fas fa-download"></i> Export for Mobile App
                    </button>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Nodes <span class="badge badge-primary" id="nodeCount">0</span></h3>
            </div>
            <div class="card-body p-0" style="max-height: 300px; overflow-y: auto;">
                <ul class="list-group list-group-flush" id="nodeList">
                    <li class="list-group-item text-muted text-center">No nodes yet</li>
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-question-circle"></i> Quick Guide
                </h3>
            </div>
            <div class="card-body">
                <h6><i class="fas fa-upload text-warning"></i> 1. Upload Map</h6>
                <p class="small">Upload your zoo floor plan or aerial image.</p>
                
                <h6><i class="fas fa-crosshairs text-secondary"></i> 2. Calibrate</h6>
                <p class="small"><strong>Drag the 4 corner markers</strong> to align your map image with the zoo area. Adjust image opacity to see the underlying base map. This ensures accurate GPS tracking and navigation.</p>
                
                <h6><i class="fas fa-map-marker-alt text-primary"></i> 3. Add Nodes</h6>
                <p class="small">Place waypoints using geographic coordinates.</p>
                
                <h6><i class="fas fa-route text-success"></i> 4. Draw Paths</h6>
                <p class="small">Connect nodes to create navigation paths.</p>
                
                <h6><i class="fas fa-download text-info"></i> 5. Export</h6>
                <p class="small">Export calibration data for mobile app integration with GPS tracking.</p>
                
                <hr>
                
                <h6>Node Types:</h6>
                <ul class="small">
                    <li><span class="badge badge-primary">●</span> Waypoint: Path intersections</li>
                    <li><span class="badge badge-success">●</span> Entrance: Entry points</li>
                    <li><span class="badge badge-warning">●</span> Exit: Exit points</li>
                    <li><span class="badge badge-info">●</span> POI: Points of Interest</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Map Upload Modal -->
<div class="modal fade" id="mapUploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Map Image</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="mapUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label>Select Map Image</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="mapImageFile" name="map_image" accept="image/*" required>
                            <label class="custom-file-label" for="mapImageFile">Choose file</label>
                        </div>
                        <small class="form-text text-muted">Supported: JPG, PNG, SVG (max 5MB)</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="uploadMapSubmit">Upload</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Global variables
let map, imageOverlay, currentTool = 'pan';
let nodes = @json($nodes);
let paths = @json($paths);
let nodeMarkers = {};
let pathLines = {};
let selectedNode = null;
let pathStartNode = null;
let mapImageUrl = '{{ $mapImageUrl }}';
let mapBounds = {!! $mapBounds !!}; // Load saved bounds from database
let cornerMarkers = {};
let isCalibrating = false;

$(document).ready(function() {
    initializeMap();
    loadMapSettings();
    loadNodes();
    loadPaths();
    setupEventHandlers();
});

function initializeMap() {
    // Initialize Leaflet map with Tripoli, Libya coordinates
    map = L.map('mapEditor', {
        center: [32.8872, 13.1913],
        zoom: 16,
        minZoom: 14,
        maxZoom: 20,
        zoomControl: true,
        attributionControl: false
    });

    // Add OpenStreetMap base layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19
    }).addTo(map);

    // Add image overlay if map image exists
    if (mapImageUrl) {
        addImageOverlay(mapImageUrl);
    }

    // Set default tool
    setTool('pan');
}

function loadMapSettings() {
    // Update calibration form with current bounds
    if (mapBounds && mapBounds.length === 2) {
        updateCalibrationForm();
    }
}

function updateCalibrationForm() {
    if (!mapBounds || mapBounds.length !== 2) return;
    
    const sw = mapBounds[0]; // [lat, lng]
    const ne = mapBounds[1]; // [lat, lng]
    
    // Update corner coordinates
    $('#nwLat').val(ne[0].toFixed(6));
    $('#nwLng').val(sw[1].toFixed(6));
    
    $('#neLat').val(ne[0].toFixed(6));
    $('#neLng').val(ne[1].toFixed(6));
    
    $('#swLat').val(sw[0].toFixed(6));
    $('#swLng').val(sw[1].toFixed(6));
    
    $('#seLat').val(sw[0].toFixed(6));
    $('#seLng').val(ne[1].toFixed(6));
    
    // Calculate and display center
    const centerLat = (sw[0] + ne[0]) / 2;
    const centerLng = (sw[1] + ne[1]) / 2;
    $('#centerLat').val(centerLat.toFixed(6));
    $('#centerLng').val(centerLng.toFixed(6));
}

function addImageOverlay(url) {
    if (imageOverlay) {
        map.removeLayer(imageOverlay);
    }
    
    imageOverlay = L.imageOverlay(url, mapBounds, {
        opacity: 0.8,
        interactive: false
    }).addTo(map);
    
    map.fitBounds(mapBounds);
}

function setupEventHandlers() {
    // Tool selection
    $('#panTool').click(() => setTool('pan'));
    $('#nodeTool').click(() => setTool('node'));
    $('#pathTool').click(() => setTool('path'));
    $('#calibrateBtn').click(() => setTool('calibrate'));
    $('#clearSelection').click(clearSelection);
    
    // Map click handler
    map.on('click', function(e) {
        if (currentTool === 'node') {
            addNodeAt(e.latlng);
        }
    });

    // Upload map
    $('#uploadMapBtn').click(() => $('#mapUploadModal').modal('show'));
    $('#mapImageFile').change(function() {
        $(this).next('.custom-file-label').html(this.files[0].name);
    });
    $('#uploadMapSubmit').click(uploadMapImage);

    // Calibration
    $('#applyCalibration').click(applyCalibration);
    $('#resetCalibration').click(resetCalibration);
    $('#exportCalibration').click(exportCalibration);
    $('#imageOpacity').on('input', function() {
        if (imageOverlay) {
            imageOverlay.setOpacity($(this).val() / 100);
        }
    });

    // Node operations
    $('#saveNode').click(updateNode);
    $('#deleteNode').click(deleteNodeConfirm);
}

function setTool(tool) {
    currentTool = tool;
    $('.tool-btn').removeClass('active');
    
    $('#toolInfo, #nodeForm, #pathForm, #calibrateForm').hide();
    
    if (tool === 'pan') {
        $('#panTool').addClass('active');
        map.dragging.enable();
        $('#toolInfo').show().html('<p class="text-muted">Click and drag to pan the map. Click nodes to select them.</p>');
    } else if (tool === 'node') {
        $('#nodeTool').addClass('active');
        map.dragging.disable();
        $('#toolInfo').show().html('<p class="text-muted">Click on the map to place a new node.</p>');
    } else if (tool === 'path') {
        $('#pathTool').addClass('active');
        map.dragging.disable();
        $('#pathForm').show();
        pathStartNode = null;
    } else if (tool === 'calibrate') {
        $('#calibrateBtn').addClass('active');
        $('#calibrateForm').show();
        map.dragging.enable();
        enterCalibrationMode();
    }
    
    clearSelection();
}

function enterCalibrationMode() {
    isCalibrating = true;
    
    // Remove existing corner markers
    removeCornerMarkers();
    
    // Create draggable corner markers
    if (!mapBounds || mapBounds.length !== 2) {
        // Set default bounds around zoo center
        const center = map.getCenter();
        const offset = 0.01;
        mapBounds = [
            [center.lat - offset, center.lng - offset],
            [center.lat + offset, center.lng + offset]
        ];
    }
    
    createCornerMarkers();
    updateCalibrationForm();
    
    // Make image semi-transparent to see underlying map
    if (imageOverlay) {
        imageOverlay.setOpacity(0.6);
        $('#imageOpacity').val(60);
    }
}

function exitCalibrationMode() {
    isCalibrating = false;
    removeCornerMarkers();
    
    // Restore image opacity
    if (imageOverlay) {
        imageOverlay.setOpacity(0.8);
        $('#imageOpacity').val(80);
    }
}

function createCornerMarkers() {
    const sw = mapBounds[0];
    const ne = mapBounds[1];
    
    const corners = {
        'nw': [ne[0], sw[1]],
        'ne': [ne[0], ne[1]],
        'sw': [sw[0], sw[1]],
        'se': [sw[0], ne[1]]
    };
    
    const cornerLabels = {
        'nw': 'NW (Top-Left)',
        'ne': 'NE (Top-Right)',
        'sw': 'SW (Bottom-Left)',
        'se': 'SE (Bottom-Right)'
    };
    
    Object.keys(corners).forEach(corner => {
        const icon = L.divIcon({
            className: 'calibration-corner-marker',
            html: `<div class="corner-marker-icon">${corner.toUpperCase()}</div>`,
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });
        
        const marker = L.marker(corners[corner], {
            icon: icon,
            draggable: true,
            zIndexOffset: 1000
        }).addTo(map);
        
        marker.bindTooltip(cornerLabels[corner], {
            permanent: false,
            direction: 'top'
        });
        
        marker.on('drag', function(e) {
            updateBoundsFromMarkers();
            if (imageOverlay) {
                imageOverlay.setBounds(mapBounds);
            }
            updateCalibrationForm();
        });
        
        cornerMarkers[corner] = marker;
    });
}

function removeCornerMarkers() {
    Object.values(cornerMarkers).forEach(marker => {
        if (marker) {
            map.removeLayer(marker);
        }
    });
    cornerMarkers = {};
}

function updateBoundsFromMarkers() {
    if (!cornerMarkers.nw || !cornerMarkers.ne || !cornerMarkers.sw || !cornerMarkers.se) return;
    
    const nw = cornerMarkers.nw.getLatLng();
    const ne = cornerMarkers.ne.getLatLng();
    const sw = cornerMarkers.sw.getLatLng();
    const se = cornerMarkers.se.getLatLng();
    
    // Calculate bounds from markers
    const north = Math.max(nw.lat, ne.lat);
    const south = Math.min(sw.lat, se.lat);
    const west = Math.min(nw.lng, sw.lng);
    const east = Math.max(ne.lng, se.lng);
    
    mapBounds = [[south, west], [north, east]];
}

function addNodeAt(latlng) {
    $.ajax({
        url: '{{ route("admin.map-editor.nodes.store") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            x: latlng.lat,
            y: latlng.lng,
            type: 'waypoint',
            name: 'Node ' + (nodes.length + 1)
        },
        success: function(response) {
            nodes.push(response.node);
            addNodeMarker(response.node);
            updateNodeList();
        }
    });
}

function addNodeMarker(node) {
    // Ensure x and y are numbers
    node.x = parseFloat(node.x);
    node.y = parseFloat(node.y);
    
    const iconHtml = `<div class="node-marker ${node.type}" data-id="${node.id}"></div>`;
    const icon = L.divIcon({
        className: 'custom-div-icon',
        html: iconHtml,
        iconSize: [16, 16],
        iconAnchor: [8, 8]
    });

    const marker = L.marker([node.x, node.y], { 
        icon: icon,
        draggable: false  // Start as non-draggable
    })
        .addTo(map)
        .on('click', function(e) {
            L.DomEvent.stopPropagation(e);
            if (currentTool === 'path') {
                handlePathClick(node);
            } else if (currentTool === 'pan') {
                selectNode(node);
            }
        })
        .on('dragend', function(e) {
            // Update node position when dragged
            const latlng = e.target.getLatLng();
            node.x = latlng.lat;
            node.y = latlng.lng;
            updateNodeCoordinatesDisplay(node);
            updatePathsForNode(node);
        });

    // Add tooltip with node name
    if (node.name) {
        marker.bindTooltip(node.name, {
            permanent: false,
            direction: 'right',
            className: 'node-label'
        });
    }

    nodeMarkers[node.id] = marker;
}

function selectNode(node) {
    clearSelection();
    selectedNode = node;
    
    // Ensure x and y are numbers
    node.x = parseFloat(node.x);
    node.y = parseFloat(node.y);
    
    // Update marker appearance and make it draggable
    const markerEl = $(`.node-marker[data-id="${node.id}"]`);
    markerEl.addClass('selected');
    
    // Enable dragging for the selected node
    const marker = nodeMarkers[node.id];
    if (marker) {
        marker.dragging.enable();
        marker.setOpacity(0.8);  // Make it slightly transparent to show it's selected
    }
    
    // Show node form
    $('#nodeType').val(node.type);
    $('#nodeName').val(node.name);
    $('#nodeDescription').val(node.description);
    $('#nodeCoords').val(`${node.x.toFixed(6)}, ${node.y.toFixed(6)}`);
    $('#nodeForm').show();
    $('#toolInfo, #pathForm, #calibrateForm').hide();
}

function updateNodeCoordinatesDisplay(node) {
    if (selectedNode && selectedNode.id === node.id) {
        $('#nodeCoords').val(`${node.x.toFixed(6)}, ${node.y.toFixed(6)}`);
    }
}

function updatePathsForNode(node) {
    // Update all paths connected to this node
    paths.forEach(path => {
        if (path.start_node_id == node.id || path.end_node_id == node.id) {
            // Remove old path line
            if (pathLines[path.id]) {
                map.removeLayer(pathLines[path.id]);
            }
            // Redraw path line with new coordinates
            addPathLine(path);
        }
    });
}

function clearSelection() {
    $('.node-marker').removeClass('selected');
    
    // Disable dragging for previously selected node
    if (selectedNode && nodeMarkers[selectedNode.id]) {
        const marker = nodeMarkers[selectedNode.id];
        marker.dragging.disable();
        marker.setOpacity(1.0);  // Restore full opacity
    }
    
    selectedNode = null;
    pathStartNode = null;
    $('#nodeForm').hide();
}

function updateNode() {
    if (!selectedNode) return;
    
    $.ajax({
        url: `/admin/map-editor/nodes/${selectedNode.id}`,
        method: 'PUT',
        data: {
            _token: '{{ csrf_token() }}',
            x: selectedNode.x,
            y: selectedNode.y,
            type: $('#nodeType').val(),
            name: $('#nodeName').val(),
            description: $('#nodeDescription').val()
        },
        success: function(response) {
            const index = nodes.findIndex(n => n.id === selectedNode.id);
            if (index !== -1) {
                nodes[index] = response.node;
                
                // Update marker
                const marker = nodeMarkers[selectedNode.id];
                if (marker) {
                    marker.setTooltipContent(response.node.name);
                    const iconHtml = `<div class="node-marker ${response.node.type} selected" data-id="${response.node.id}"></div>`;
                    const icon = L.divIcon({
                        className: 'custom-div-icon',
                        html: iconHtml,
                        iconSize: [16, 16],
                        iconAnchor: [8, 8]
                    });
                    marker.setIcon(icon);
                }
                
                updateNodeList();
                alert('Node updated successfully');
            }
        }
    });
}

function deleteNodeConfirm() {
    if (!selectedNode || !confirm('Delete this node and all connected paths?')) return;
    
    $.ajax({
        url: `/admin/map-editor/nodes/${selectedNode.id}`,
        method: 'DELETE',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function() {
            // Remove marker
            if (nodeMarkers[selectedNode.id]) {
                map.removeLayer(nodeMarkers[selectedNode.id]);
                delete nodeMarkers[selectedNode.id];
            }
            
            // Remove from nodes array
            nodes = nodes.filter(n => n.id !== selectedNode.id);
            
            // Remove connected paths
            paths = paths.filter(p => {
                if (p.start_node_id === selectedNode.id || p.end_node_id === selectedNode.id) {
                    if (pathLines[p.id]) {
                        map.removeLayer(pathLines[p.id]);
                        delete pathLines[p.id];
                    }
                    return false;
                }
                return true;
            });
            
            updateNodeList();
            clearSelection();
        }
    });
}

function handlePathClick(node) {
    if (!pathStartNode) {
        pathStartNode = node;
        $(`.node-marker[data-id="${node.id}"]`).addClass('selected');
    } else if (pathStartNode.id !== node.id) {
        createPath(pathStartNode.id, node.id);
        $(`.node-marker[data-id="${pathStartNode.id}"]`).removeClass('selected');
        pathStartNode = null;
    }
}

function createPath(startId, endId) {
    $.ajax({
        url: '{{ route("admin.map-editor.paths.store") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            start_node_id: startId,
            end_node_id: endId,
            distance: parseFloat($('#pathDistance').val()) || 0,
            accessible: $('#pathAccessible').is(':checked')
        },
        success: function(response) {
            paths.push(response.path);
            addPathLine(response.path);
        }
    });
}

function addPathLine(path) {
    const startNode = nodes.find(n => n.id == path.start_node_id);
    const endNode = nodes.find(n => n.id == path.end_node_id);
    
    if (startNode && endNode) {
        // Ensure coordinates are numbers
        const startX = parseFloat(startNode.x);
        const startY = parseFloat(startNode.y);
        const endX = parseFloat(endNode.x);
        const endY = parseFloat(endNode.y);
        
        const pathLine = L.polyline(
            [[startX, startY], [endX, endY]],
            {
                color: path.accessible ? '#28a745' : '#dc3545',
                weight: 3,
                opacity: 0.8,
                dashArray: path.accessible ? null : '5, 5'
            }
        ).addTo(map);
        
        pathLines[path.id] = pathLine;
    }
}

function loadNodes() {
    nodes.forEach(node => addNodeMarker(node));
    updateNodeList();
}

function loadPaths() {
    paths.forEach(path => addPathLine(path));
}

function updateNodeList() {
    const $list = $('#nodeList');
    $list.empty();
    
    $('#nodeCount').text(nodes.length);
    
    if (nodes.length === 0) {
        $list.append('<li class="list-group-item text-muted text-center">No nodes yet</li>');
    } else {
        nodes.forEach(node => {
            const typeColors = {
                waypoint: 'primary',
                entrance: 'success',
                exit: 'warning',
                poi: 'info'
            };
            $list.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                    <small>${node.name || 'Node ' + node.id}</small>
                    <span class="badge badge-${typeColors[node.type] || 'primary'} badge-pill">${node.type}</span>
                </li>
            `);
        });
    }
}

function uploadMapImage() {
    const formData = new FormData($('#mapUploadForm')[0]);
    
    $.ajax({
        url: '{{ route("admin.map-editor.upload-map") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                mapImageUrl = response.path;
                addImageOverlay(mapImageUrl);
                $('#mapUploadModal').modal('hide');
                alert('Map image uploaded successfully. Please calibrate the map bounds.');
                setTool('calibrate');
            }
        },
        error: function() {
            alert('Error uploading map image');
        }
    });
}

function applyCalibration() {
    if (!mapBounds || mapBounds.length !== 2) {
        alert('Please calibrate the map by dragging the corner markers');
        return;
    }
    
    // Update image overlay with final bounds
    if (imageOverlay) {
        imageOverlay.setBounds(mapBounds);
    }
    
    // Save bounds to server
    $.ajax({
        url: '{{ route("admin.map-editor.upload-map") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            bounds: JSON.stringify(mapBounds)
        },
        success: function() {
            alert('✓ Map calibration saved successfully!\n\nCorner coordinates are now available for the mobile application.\n\nYou can now add nodes and paths on the calibrated map.');
            exitCalibrationMode();
            setTool('pan');
        },
        error: function() {
            alert('Error saving map calibration');
        }
    });
}

function resetCalibration() {
    // Reset to Tripoli Central Zoo area
    const zooCenter = [32.8872, 13.1913];
    const offset = 0.01;
    
    mapBounds = [
        [zooCenter[0] - offset, zooCenter[1] - offset],
        [zooCenter[0] + offset, zooCenter[1] + offset]
    ];
    
    if (imageOverlay) {
        imageOverlay.setBounds(mapBounds);
    }
    
    // Recreate corner markers at new positions
    removeCornerMarkers();
    createCornerMarkers();
    updateCalibrationForm();
    
    map.setView(zooCenter, 16);
}

function exportCalibration() {
    if (!mapBounds || mapBounds.length !== 2) {
        alert('Please calibrate the map first');
        return;
    }
    
    const sw = mapBounds[0];
    const ne = mapBounds[1];
    
    const calibrationData = {
        mapImageUrl: mapImageUrl,
        bounds: {
            southwest: {
                latitude: sw[0],
                longitude: sw[1]
            },
            northeast: {
                latitude: ne[0],
                longitude: ne[1]
            }
        },
        corners: {
            northwest: {
                latitude: ne[0],
                longitude: sw[1]
            },
            northeast: {
                latitude: ne[0],
                longitude: ne[1]
            },
            southwest: {
                latitude: sw[0],
                longitude: sw[1]
            },
            southeast: {
                latitude: sw[0],
                longitude: ne[1]
            }
        },
        center: {
            latitude: (sw[0] + ne[0]) / 2,
            longitude: (sw[1] + ne[1]) / 2
        },
        exportDate: new Date().toISOString(),
        zooName: 'Tripoli Central Zoo'
    };
    
    // Create downloadable JSON file
    const dataStr = JSON.stringify(calibrationData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'zoo-map-calibration.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
    
    alert('✓ Map calibration exported successfully!\n\nFile: zoo-map-calibration.json\n\nThis file contains all corner coordinates and bounds for integration with the mobile application and GPS tracking.');
}
</script>
@endpush
