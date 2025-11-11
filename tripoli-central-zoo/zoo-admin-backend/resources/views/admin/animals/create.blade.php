@extends('layouts.admin')

@section('title', 'Create Animal')
@section('page-title', 'Create New Animal')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.animals.index') }}">Animals</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.animals.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="species">Species <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('species') is-invalid @enderror" 
                                   id="species" name="species" value="{{ old('species') }}" required>
                            @error('species')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="scientific_name">Scientific Name</label>
                            <input type="text" class="form-control @error('scientific_name') is-invalid @enderror" 
                                   id="scientific_name" name="scientific_name" value="{{ old('scientific_name') }}">
                            @error('scientific_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category <span class="text-danger">*</span></label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="habitat">Habitat</label>
                            <input type="text" class="form-control @error('habitat') is-invalid @enderror" 
                                   id="habitat" name="habitat" value="{{ old('habitat') }}">
                            @error('habitat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="conservation_status">Conservation Status</label>
                            <input type="text" class="form-control @error('conservation_status') is-invalid @enderror" 
                                   id="conservation_status" name="conservation_status" value="{{ old('conservation_status') }}">
                            @error('conservation_status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="text" class="form-control @error('age') is-invalid @enderror" 
                                   id="age" name="age" value="{{ old('age') }}">
                            @error('age')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="weight">Weight</label>
                            <input type="text" class="form-control @error('weight') is-invalid @enderror" 
                                   id="weight" name="weight" value="{{ old('weight') }}">
                            @error('weight')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="size">Size</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" 
                                   id="size" name="size" value="{{ old('size') }}">
                            @error('size')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="facts">Fun Facts</label>
                    <textarea class="form-control @error('facts') is-invalid @enderror" 
                              id="facts" name="facts" rows="2">{{ old('facts') }}</textarea>
                    @error('facts')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1" {{ old('featured') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="featured">Featured Animal</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*" onchange="previewImage(event)">
                        <label class="custom-file-label" for="image">Choose file</label>
                        @error('image')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div id="imagePreview" class="mt-2"></div>
                </div>

                <hr>

                <h5>Location on Map</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location_x">Latitude (X)</label>
                            <input type="number" step="0.000001" class="form-control @error('location_x') is-invalid @enderror" 
                                   id="location_x" name="location_x" value="{{ old('location_x') }}">
                            @error('location_x')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location_y">Longitude (Y)</label>
                            <input type="number" step="0.000001" class="form-control @error('location_y') is-invalid @enderror" 
                                   id="location_y" name="location_y" value="{{ old('location_y') }}">
                            @error('location_y')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div id="map" style="height: 400px; border: 1px solid #ddd;"></div>
                    <small class="form-text text-muted">Click on the map to set the animal's location</small>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                           id="display_order" name="display_order" value="{{ old('display_order', 0) }}">
                    @error('display_order')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Create Animal
                </button>
                <a href="{{ route('admin.animals.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
// Image preview
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '<img src="' + reader.result + '" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">';
    };
    reader.readAsDataURL(event.target.files[0]);
    
    // Update file label
    const fileName = event.target.files[0].name;
    $(event.target).next('.custom-file-label').html(fileName);
}

// Initialize map
let map, marker;
$(document).ready(function() {
    // Initialize Leaflet map centered on Tripoli, Libya
    map = L.map('map').setView([32.8872, 13.1913], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Set marker on existing coordinates if available
    const existingX = parseFloat($('#location_x').val());
    const existingY = parseFloat($('#location_y').val());
    
    if (!isNaN(existingX) && !isNaN(existingY)) {
        marker = L.marker([existingX, existingY]).addTo(map);
        map.setView([existingX, existingY], 15);
    }

    // Add marker on map click
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        // Update form fields
        $('#location_x').val(lat.toFixed(6));
        $('#location_y').val(lng.toFixed(6));

        // Update or create marker
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng]).addTo(map);
        }
    });

    // Update marker when coordinates are manually entered
    $('#location_x, #location_y').on('change', function() {
        const lat = parseFloat($('#location_x').val());
        const lng = parseFloat($('#location_y').val());

        if (!isNaN(lat) && !isNaN(lng)) {
            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng]).addTo(map);
            }
            map.setView([lat, lng], 15);
        }
    });
});
</script>
@endpush
