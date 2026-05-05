@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Edit Staff Member') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('staff.update', $staff->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Name') }} *</label>
                            <input type="text" name="name" class="form-control" value="{{ $staff->name }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Email') }} *</label>
                            <input type="email" name="email" class="form-control" value="{{ $staff->email }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Phone') }}</label>
                            <input type="text" name="phone" class="form-control" value="{{ $staff->phone }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Address') }}</label>
                            <input type="text" name="address" class="form-control" value="{{ $staff->address }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Role') }} *</label>
                            <select name="role" class="form-select" required>
                                <option value="admin" {{ $staff->role === 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                <option value="cashier" {{ $staff->role === 'cashier' ? 'selected' : '' }}>{{ __('Cashier') }}</option>
                                <option value="staff" {{ $staff->role === 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('New Password') }} (leave blank to keep current)</label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>
                        {{-- Only Admin can change status --}}
                        @if(auth()->user()->role === 'admin')
                            {{-- System Owner (ID: 1) can change any status --}}
                            {{-- Other Admin can only change non-admin status or their own status --}}
                            @if(auth()->id() === 1 || $staff->role !== 'admin' || $staff->id === auth()->id())
                                <div class="mb-3">
                                    <label class="form-label">{{ __('Status') }} *</label>
                                    <select name="status" class="form-select" required>
                                        <option value="active" {{ $staff->status === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ $staff->status === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            @else
                                <input type="hidden" name="status" value="{{ $staff->status }}">
                            @endif
                        @else
                            <input type="hidden" name="status" value="{{ $staff->status }}">
                        @endif
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                            <a href="{{ route('staff.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
