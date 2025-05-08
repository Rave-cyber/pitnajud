@extends('layouts.app') {{-- Change this to your actual layout --}}
<style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@section('content')
<div class="container">
    <div class="form-container mt-4 p-4 bg-light rounded shadow-sm">
        <h2 class="mb-4">Edit Inventory Item</h2>

        <form action="{{ route('admin.inventory.update', $inventory->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group mb-3">
                <label for="name" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       value="{{ old('name', $inventory->name) }}" required>
                @error('name')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="">Select Category</option>
                    <option value="Cleaning Supplies" {{ old('category', $inventory->category) == 'Cleaning Supplies' ? 'selected' : '' }}>Cleaning Supplies</option>
                    <option value="Equipment" {{ old('category', $inventory->category) == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                    <option value="Packaging" {{ old('category', $inventory->category) == 'Packaging' ? 'selected' : '' }}>Packaging</option>
                    <option value="Other" {{ old('category', $inventory->category) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('category')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity"
                               min="0" value="{{ old('quantity', $inventory->quantity) }}" required>
                        @error('quantity')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label for="price" class="form-label">Price ($)</label>
                        <input type="number" class="form-control" id="price" name="price"
                               min="0" step="0.01" value="{{ old('price', $inventory->price) }}" required>
                        @error('price')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="In Stock" {{ old('status', $inventory->status) == 'In Stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="Low Stock" {{ old('status', $inventory->status) == 'Low Stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="Out of Stock" {{ old('status', $inventory->status) == 'Out of Stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
                @error('status')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Item</button>
            </div>
        </form>
    </div>
</div>
@endsection
