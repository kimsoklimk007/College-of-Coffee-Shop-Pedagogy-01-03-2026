@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h4 class="mb-3">{{ __('Add New Shift') }}</h4>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('shift.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Status') }} <span class="text-danger">*</span></label>
                                    <select name="status" class="form-select" required>
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Start Time') }} <span class="text-danger">*</span></label>
                                    <input type="time" name="start_time" class="form-control" required value="{{ old('start_time') }}">
                                    @error('start_time')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('End Time') }} <span class="text-danger">*</span></label>
                                    <input type="time" name="end_time" class="form-control" required value="{{ old('end_time') }}">
                                    @error('end_time')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Late Threshold (minutes)') }}</label>
                                    <input type="number" name="late_threshold" class="form-control" value="{{ old('late_threshold', 15) }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label>{{ __('Description') }}</label>
                                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                            <a href="{{ route('shift.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
