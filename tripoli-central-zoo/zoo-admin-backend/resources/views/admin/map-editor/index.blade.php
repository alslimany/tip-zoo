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
        border: 2px solid #ddd;
        cursor: crosshair;
        position: relative;
        background: #f8f9fa;
    }
    .map-node {
        position: absolute;
        width: 20px;
        height: 20px;
        background: #007bff;
        border: 2px solid #fff;
        border-radius: 50%;
        cursor: move;
        transform: translate(-10px, -10px);
        z-index: 10;
    }
    .map-node:hover {
        background: #0056b3;
        width: 24px;
        height: 24px;
        transform: translate(-12px, -12px);
    }
    .map-node.selected {
        background: #dc3545;
        border-color: #c82333;
    }
    .map-path {
        stroke: #28a745;
        stroke-width: 3;
        fill: none;
        cursor: pointer;
    }
    .map-path:hover {
        stroke: #218838;
        stroke-width: 4;
    }
    #mapImage {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: contain;
        z-index: 1;
    }
    #mapSvg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 5;
    }
    .tool-btn {
        margin: 5px;
    }
    .node-label {
        position: absolute;
        font-size: 10px;
        background: rgba(255,255,255,0.8);
        padding: 2px 4px;
        border-radius: 3px;
        white-space: nowrap;
        pointer-events: none;
        z-index: 11;
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
                    <button type="button" class="btn btn-sm btn-danger tool-btn" id="clearSelection" title="Clear Selection">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div id="mapEditor">
                    <img id="mapImage" src="" alt="Map" style="display: none;">
                    <svg id="mapSvg"></svg>
                </div>
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
                    <button class="btn btn-sm btn-primary" id="saveNode">Save Node</button>
                    <button class="btn btn-sm btn-danger" id="deleteNode">Delete Node</button>
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
                        <small class="form-text text-muted">Supported formats: JPG, PNG, SVG (max 5MB)</small>
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
let nodes = @json($nodes);
let paths = @json($paths);
let currentTool = 'pan';
let selectedNode = null;
let selectedPath = null;
let pathStartNode = null;
let mapImage = null;

$(document).ready(function() {
    initializeMap();
    loadNodes();
    loadPaths();

    // Tool selection
    $('#panTool').click(() => setTool('pan'));
    $('#nodeTool').click(() => setTool('node'));
    $('#pathTool').click(() => setTool('path'));
    $('#clearSelection').click(clearSelection);
    
    // Map click handler
    $('#mapEditor').click(function(e) {
        if (currentTool === 'node') {
            addNode(e);
        }
    });

    // Upload map
    $('#uploadMapBtn').click(() => $('#mapUploadModal').modal('show'));
    $('#mapImageFile').change(function() {
        $(this).next('.custom-file-label').html(this.files[0].name);
    });
    $('#uploadMapSubmit').click(uploadMapImage);

    // Node operations
    $('#saveNode').click(updateNode);
    $('#deleteNode').click(deleteNodeConfirm);
});

function setTool(tool) {
    currentTool = tool;
    $('.tool-btn').removeClass('active');
    if (tool === 'pan') {
        $('#panTool').addClass('active');
        $('#mapEditor').css('cursor', 'default');
        $('#toolInfo').show();
        $('#nodeForm, #pathForm').hide();
    } else if (tool === 'node') {
        $('#nodeTool').addClass('active');
        $('#mapEditor').css('cursor', 'crosshair');
        $('#toolInfo').hide();
        $('#nodeForm, #pathForm').hide();
    } else if (tool === 'path') {
        $('#pathTool').addClass('active');
        $('#mapEditor').css('cursor', 'crosshair');
        $('#toolInfo').hide();
        $('#nodeForm').hide();
        $('#pathForm').show();
        pathStartNode = null;
    }
    clearSelection();
}

function initializeMap() {
    // Set default tool
    setTool('pan');
}

function addNode(e) {
    const offset = $('#mapEditor').offset();
    const x = e.pageX - offset.left;
    const y = e.pageY - offset.top;
    
    $.ajax({
        url: '{{ route("admin.map-editor.nodes.store") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            x: x,
            y: y,
            type: 'waypoint',
            name: 'Node ' + (nodes.length + 1)
        },
        success: function(response) {
            nodes.push(response.node);
            renderNode(response.node);
            updateNodeList();
        }
    });
}

function renderNode(node) {
    const $node = $('<div>')
        .addClass('map-node')
        .attr('data-id', node.id)
        .css({
            left: node.x + 'px',
            top: node.y + 'px'
        })
        .click(function(e) {
            e.stopPropagation();
            if (currentTool === 'path') {
                handlePathClick(node);
            } else {
                selectNode(node);
            }
        });
    
    if (node.name) {
        const $label = $('<div>')
            .addClass('node-label')
            .text(node.name)
            .css({
                left: (parseFloat(node.x) + 15) + 'px',
                top: (parseFloat(node.y) - 5) + 'px'
            });
        $('#mapEditor').append($label);
    }
    
    $('#mapEditor').append($node);
}

function handlePathClick(node) {
    if (!pathStartNode) {
        pathStartNode = node;
        $(`.map-node[data-id="${node.id}"]`).addClass('selected');
    } else if (pathStartNode.id !== node.id) {
        createPath(pathStartNode.id, node.id);
        $(`.map-node[data-id="${pathStartNode.id}"]`).removeClass('selected');
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
            renderPath(response.path);
        }
    });
}

function renderPath(path) {
    const startNode = nodes.find(n => n.id === path.start_node_id);
    const endNode = nodes.find(n => n.id === path.end_node_id);
    
    if (startNode && endNode) {
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', startNode.x);
        line.setAttribute('y1', startNode.y);
        line.setAttribute('x2', endNode.x);
        line.setAttribute('y2', endNode.y);
        line.setAttribute('class', 'map-path');
        line.setAttribute('data-id', path.id);
        line.onclick = () => selectPath(path);
        $('#mapSvg').append(line);
    }
}

function selectNode(node) {
    clearSelection();
    selectedNode = node;
    $(`.map-node[data-id="${node.id}"]`).addClass('selected');
    
    $('#nodeType').val(node.type);
    $('#nodeName').val(node.name);
    $('#nodeDescription').val(node.description);
    $('#nodeForm').show();
    $('#pathForm').hide();
}

function selectPath(path) {
    clearSelection();
    selectedPath = path;
    $(`.map-path[data-id="${path.id}"]`).css('stroke', '#dc3545');
}

function clearSelection() {
    $('.map-node').removeClass('selected');
    $('.map-path').css('stroke', '#28a745');
    selectedNode = null;
    selectedPath = null;
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
                updateNodeList();
            }
            alert('Node updated successfully');
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
            nodes = nodes.filter(n => n.id !== selectedNode.id);
            paths = paths.filter(p => p.start_node_id !== selectedNode.id && p.end_node_id !== selectedNode.id);
            loadNodes();
            loadPaths();
            clearSelection();
        }
    });
}

function loadNodes() {
    $('#mapEditor .map-node, #mapEditor .node-label').remove();
    nodes.forEach(renderNode);
    updateNodeList();
}

function loadPaths() {
    $('#mapSvg').empty();
    paths.forEach(renderPath);
}

function updateNodeList() {
    const $list = $('#nodeList');
    $list.empty();
    
    $('#nodeCount').text(nodes.length);
    
    if (nodes.length === 0) {
        $list.append('<li class="list-group-item text-muted text-center">No nodes yet</li>');
    } else {
        nodes.forEach(node => {
            $list.append(`
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <small>${node.name || 'Node ' + node.id}</small>
                    <span class="badge badge-primary badge-pill">${node.type}</span>
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
                $('#mapImage').attr('src', response.path).show();
                $('#mapUploadModal').modal('hide');
                alert('Map image uploaded successfully');
            }
        },
        error: function() {
            alert('Error uploading map image');
        }
    });
}
</script>
@endpush
