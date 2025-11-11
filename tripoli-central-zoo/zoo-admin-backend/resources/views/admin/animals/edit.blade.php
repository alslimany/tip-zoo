@extends('layouts.admin')

@section('title', 'Edit Animal')
@section('page-title', 'Edit Animal: ' . $animal->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.animals.index') }}">Animals</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card">
        <form action="{{ route('admin.animals.update', $animal) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $animal->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="species">Species <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('species') is-invalid @enderror" 
                                   id="species" name="species" value="{{ old('species', $animal->species) }}" required>
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
                                   id="scientific_name" name="scientific_name" value="{{ old('scientific_name', $animal->scientific_name) }}">
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
                                    <option value="{{ $category->id }}" {{ old('category_id', $animal->category_id) == $category->id ? 'selected' : '' }}>
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
                              id="description" name="description" rows="3">{{ old('description', $animal->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="habitat">Habitat</label>
                            <input type="text" class="form-control @error('habitat') is-invalid @enderror" 
                                   id="habitat" name="habitat" value="{{ old('habitat', $animal->habitat) }}">
                            @error('habitat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="conservation_status">Conservation Status</label>
                            <input type="text" class="form-control @error('conservation_status') is-invalid @enderror" 
                                   id="conservation_status" name="conservation_status" value="{{ old('conservation_status', $animal->conservation_status) }}">
                            @error('conservation_status')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="text" class="form-control @error('age') is-invalid @enderror" 
                                   id="age" name="age" value="{{ old('age', $animal->age) }}">
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
                                   id="weight" name="weight" value="{{ old('weight', $animal->weight) }}">
                            @error('weight')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="size">Size</label>
                            <input type="text" class="form-control @error('size') is-invalid @enderror" 
                                   id="size" name="size" value="{{ old('size', $animal->size) }}">
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
                                <option value="active" {{ old('status', $animal->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $animal->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status', $animal->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
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
                              id="facts" name="facts" rows="2">{{ old('facts', $animal->facts) }}</textarea>
                    @error('facts')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="featured" name="featured" value="1" {{ old('featured', $animal->featured) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="featured">Featured Animal</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="image">Image</label>
                    @if($animal->image_url)
                        <div class="mb-2">
                            <img src="{{ $animal->image_url }}" alt="{{ $animal->name }}" style="max-width: 200px; max-height: 200px;" class="img-thumbnail">
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

                @if($animal->isMapped)
                    <div class="alert alert-success">
                        <i class="fas fa-map-marker-alt"></i>
                        <strong>Mapped:</strong> This animal is placed on the zoo map. 
                        <a href="{{ route('admin.map-editor.index') }}" class="alert-link">Edit map location</a>
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Not Mapped:</strong> This animal hasn't been placed on the zoo map yet. 
                        <a href="{{ route('admin.map-editor.index') }}" class="alert-link">Add to map</a>
                    </div>
                @endif

                <div class="form-group">
                    <label for="display_order">Display Order</label>
                    <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                           id="display_order" name="display_order" value="{{ old('display_order', $animal->display_order) }}">
                    @error('display_order')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Animal
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
</script>
@endpush
