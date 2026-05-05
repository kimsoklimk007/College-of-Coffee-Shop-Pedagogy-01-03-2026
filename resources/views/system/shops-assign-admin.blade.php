@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-user-shield"></i> {{ __('Assign Admin to Shop') }}</h3>
            <a href="{{ route('system.shops') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back') }}
            </a>
        </div>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">{{ $shop->name }}</h5>
                <small>{{ $shop->code }}</small>
            </div>
            <div class="card-body">
                <form action="{{ route('system.shops.assignAdmin', $shop) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">{{ __('Select Admin') }} <span class="text-danger">*</span></label>
                        <select name="owner_id" class="form-select form-select-lg @error('owner_id') is-invalid @enderror" required>
                            <option value="">{{ __('Choose an admin...') }}</option>
                            @forelse($availableAdmins as $admin)
                                <option value="{{ $admin->id }}" {{ old('owner_id', $shop->owner_id) == $admin->id ? 'selected' : '' }}>
                                    {{ $admin->name }} ({{ $admin->email }})
                                    @if($admin->shop_id && $admin->shop_id != $shop->id)
                                        - {{ __('Currently owns another shop') }}
                                    @elseif($admin->shop_id == $shop->id)
                                        - {{ __('Current owner') }}
                                    @else
                                        - {{ __('Available') }}
                                    @endif
                                </option>
                            @empty
                                <option value="" disabled>{{ __('No available admins found') }}</option>
                            @endforelse
                        </select>
                        @error('owner_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">{{ __('Admins without a shop or current owner of this shop are shown') }}</div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>{{ __('Note:') }}</strong>
                        <ul class="mb-0 mt-2">
                            <li>{{ __('Assigning a new admin will remove the current owner from this shop') }}</li>
                            <li>{{ __('The new admin will gain full control of this shop') }}</li>
                            <li>{{ __('This action will be logged for security purposes') }}</li>
                        </ul>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> {{ __('Assign Admin') }}
                        </button>
                        <a href="{{ route('system.shops') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Current Owner Info -->
        @if($shop->owner)
            <div class="card shadow mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">{{ __('Current Owner Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">{{ __('Name:') }}</td>
                                    <td>{{ $shop->owner->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('Email:') }}</td>
                                    <td>{{ $shop->owner->email }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('Phone:') }}</td>
                                    <td>{{ $shop->owner->phone ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td class="fw-bold">{{ __('Username:') }}</td>
                                    <td>{{ $shop->owner->username ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('Role:') }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ ucfirst($shop->owner->role) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">{{ __('Status:') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $shop->owner->status === 'Active' ? 'success' : 'danger' }}">{{ $shop->owner->status }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i>
                {{ __('This shop currently has no owner assigned.') }}
            </div>
        @endif
    </section>
@endsection
