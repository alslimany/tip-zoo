@extends('layouts.admin')

@section('title', 'Edit Facility')
@section('page-title', 'Edit Facility: ' . $facility->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.facilities.index') }}">Facilities</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.facilities.update', $facility) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $facility->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="type">Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('type') is-invalid @enderror" 
                                    id="type" name="type" required>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type', $facility->type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $facility->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="open" {{ old('status', $facility->status) == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ old('status', $facility->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                <option value="maintenance" {{ old('status', $facility->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                            @error('status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $facility->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="contact_phone">Contact Phone</label>
                            <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                   id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $facility->contact_phone) }}">
                            @error('contact_phone')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="contact_email">Contact Email</label>
                            <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                   id="contact_email" name="contact_email" value="{{ old('contact_email', $facility->contact_email) }}">
                            @error('contact_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="capacity">Capacity</label>
                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                   id="capacity" name="capacity" value="{{ old('capacity', $facility->capacity) }}">
                            @error('capacity')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="is_accessible" 
                               name="is_accessible" value="1" {{ old('is_accessible', $facility->is_accessible) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_accessible">
                            <i class="fas fa-wheelchair"></i> Wheelchair Accessible
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    @if($facility->image_url)
                        <div class="mb-2">
                            <img src="{{ $facility->image_url }}" alt="{{ $facility->name }}" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" 
                               id="image" name="image" accept="image/*" onchange="previewImage(event)">
                        <label class="custom-file-label" for="image">Choose new file</label>
                        @error('image')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <div id="imagePreview" class="mt-2"></div>
                </div>

                <hr>
                
                @livewire('opening-hours-manager', ['existingHours' => $facility->opening_hours ?? []])

                <hr>

                <h5>Location on Map</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location_x">Latitude (X)</label>
                            <input type="number" step="0.000001" class="form-control @error('location_x') is-invalid @enderror" 
                                   id="location_x" name="location_x" value="{{ old('location_x', $facility->location_x) }}">
                            @error('location_x')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="location_y">Longitude (Y)</label>
                            <input type="number" step="0.000001" class="form-control @error('location_y') is-invalid @enderror" 
                                   id="location_y" name="location_y" value="{{ old('location_y', $facility->location_y) }}">
                            @error('location_y')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div id="map" style="height: 400px; border: 1px solid #ddd;"></div>
                    <small class="form-text text-muted">Click on the map to update the facility's location</small>
                </div>

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                           id="display_order" name="display_order" value="{{ old('display_order', $facility->display_order) }}">
                    @error('display_order')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Facility
                </button>
                <a href="{{ route('admin.facilities.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const preview = document.getElementById('imagePreview');
        preview.innerHTML = '<img src="' + reader.result + '" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">';
    };
    reader.readAsDataURL(event.target.files[0]);
    const fileName = event.target.files[0].name;
    $(event.target).next('.custom-file-label').html(fileName);
}

let map, marker;
$(document).ready(function() {
    map = L.map('map').setView([32.8872, 13.1913], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    const existingX = parseFloat($('#location_x').val());
    const existingY = parseFloat($('#location_y').val());
    
    if (!isNaN(existingX) && !isNaN(existingY)) {
        marker = L.marker([existingX, existingY]).addTo(map);
        map.setView([existingX, existingY], 15);
    }

    map.on('click', function(e) {
        $('#location_x').val(e.latlng.lat.toFixed(6));
        $('#location_y').val(e.latlng.lng.toFixed(6));
        if (marker) {
            marker.setLatLng([e.latlng.lat, e.latlng.lng]);
        } else {
            marker = L.marker([e.latlng.lat, e.latlng.lng]).addTo(map);
        }
    });

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
