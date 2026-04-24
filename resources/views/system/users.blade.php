@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-users-cog"></i> {{ __('User Management') }}</h3>
            <a href="{{ route('system.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Create User') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('system.users') }}" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name, email, username') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="role" class="form-select">
                            <option value="">{{ __('All Roles') }}</option>
                            @foreach($roles as $role)
                                <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $role)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            <i class="fas fa-search"></i> {{ __('Filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Username') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Shop') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td><code>{{ $user->username }}</code></td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'super_admin' ? 'danger' : ($user->role === 'admin' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                        </span>
                                    </td>
                                    <td>{{ $user->shop->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->status === 'Active' ? 'success' : 'danger' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('system.users.edit', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            @if($user->id !== auth()->id() && $user->role !== 'super_admin')
                                                <form action="{{ route('system.users.toggleStatus', $user) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $user->status === 'Active' ? 'warning' : 'success' }}" title="{{ $user->status === 'Active' ? __('Block') : __('Activate') }}">
                                                        <i class="fas fa-{{ $user->status === 'Active' ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                </form>

                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $user->id }}" title="{{ __('Reset Password') }}">
                                                    <i class="fas fa-key"></i>
                                                </button>

                                                <form action="{{ route('system.users.delete', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this user?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Reset Password Modal -->
                                        <div class="modal fade" id="resetPasswordModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('system.users.resetPassword', $user) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ __('Reset Password') }} - {{ $user->name }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('New Password') }}</label>
                                                                <input type="password" name="password" class="form-control" required minlength="8">
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">{{ __('Confirm Password') }}</label>
                                                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-primary">{{ __('Reset Password') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">{{ __('No users found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
