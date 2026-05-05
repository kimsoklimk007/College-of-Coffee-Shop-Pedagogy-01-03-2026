@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-user-edit"></i> {{ __('Edit User') }}: {{ $user->name }}</h3>
            <a href="{{ route('system.users') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <form action="{{ route('system.users.update', $user) }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Full Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Username') }} <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Role') }} <span class="text-danger">*</span></label>
                            <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ old('role', $user->role) == $role ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $role)) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="Active" {{ old('status', $user->status) == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                <option value="Inactive" {{ old('status', $user->status) == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3" id="shop-container" style="{{ $user->role === 'super_admin' ? 'display:none' : '' }}">
                            <label class="form-label">{{ __('Assign to Shop') }}</label>
                            <select name="shop_id" class="form-select @error('shop_id') is-invalid @enderror">
                                <option value="">{{ __('No Shop') }}</option>
                                @foreach($shops as $shop)
                                    <option value="{{ $shop->id }}" {{ old('shop_id', $user->shop_id) == $shop->id ? 'selected' : '' }}>
                                        {{ $shop->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shop_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">{{ __('Super Admin cannot be assigned to a shop') }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ __('Update User') }}
                        </button>
                        <a href="{{ route('system.users') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.getElementById('role').addEventListener('change', function() {
        const shopContainer = document.getElementById('shop-container');
        if (this.value === 'super_admin') {
            shopContainer.style.display = 'none';
        } else {
            shopContainer.style.display = 'block';
        }
    });
</script>
@endpush
