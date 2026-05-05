@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Shifts') }}</h4>
                <a href="{{ route('shift.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> {{ __('Add Shift') }}
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" class="mb-3">
                        <div class="row">
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name') }}" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">{{ __('Search') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Start Time') }}</th>
                                    <th>{{ __('End Time') }}</th>
                                    <th>{{ __('Late Threshold') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($shifts as $shift)
                                <tr>
                                    <td>{{ $shift->id }}</td>
                                    <td>{{ $shift->display_name }}</td>
                                    <td>{{ $shift->start_time }}</td>
                                    <td>{{ $shift->end_time }}</td>
                                    <td>{{ $shift->late_threshold ?? 15 }} {{ __('minutes') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $shift->status == 'Active' ? 'success' : 'secondary' }}">
                                            {{ __($shift->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('shift.show', $shift->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('shift.edit', $shift->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('shift.delete', $shift->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure?') }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">{{ __('No shifts found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $shifts->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
