@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Staff Management') }}</h4>
                    <a href="{{ route('staff.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ __('Add Staff') }}
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name or email') }}" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <select name="role" class="form-select">
                                    <option value="">{{ __('All Roles') }}</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
                                    <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>{{ __('Cashier') }}</option>
                                    <option value="staff" {{ request('role') == 'staff' ? 'selected' : '' }}>{{ __('Staff') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">{{ __('All Status') }}</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary w-100">{{ __('Filter') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('ID') }}</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Email') }}</th>
                                    <th>{{ __('Phone') }}</th>
                                    <th>{{ __('Role') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Created At') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($staff as $member)
                                <tr>
                                    <td>{{ $member->id }}</td>
                                    <td>{{ $member->name }}</td>
                                    <td>{{ $member->email }}</td>
                                    <td>{{ $member->phone ?? '-' }}</td>
                                    <td>
                                        @if($member->role === 'admin')
                                            <span class="badge bg-primary">{{ __('Admin') }}</span>
                                        @elseif($member->role === 'cashier')
                                            <span class="badge bg-info">{{ __('Cashier') }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('Staff') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->status === 'active')
                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('Inactive') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $member->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <a href="{{ route('staff.edit', $member->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($member->id !== auth()->id())
                                            {{-- Only Admin can toggle status --}}
                                            @if(auth()->user()->role === 'admin')
                                                {{-- System Owner (ID: 1) can toggle any status --}}
                                                {{-- Other Admin can only toggle non-admin status or their own status --}}
                                                @if(auth()->id() === 1 || $member->role !== 'admin' || $member->id === auth()->id())
                                                    <form action="{{ route('staff.toggleStatus', $member->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-{{ $member->status === 'active' ? 'danger' : 'success' }}"
                                                                title="{{ $member->status === 'active' ? __('Deactivate') : __('Activate') }}">
                                                            <i class="fas fa-{{ $member->status === 'active' ? 'ban' : 'check' }}"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                            <form action="{{ route('staff.destroy', $member->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">{{ __('No staff members found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $staff->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
