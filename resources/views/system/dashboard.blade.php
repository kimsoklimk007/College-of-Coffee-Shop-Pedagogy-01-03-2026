@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Super Admin Welcome Header -->
        <div class="row justify-content-center mb-3">
            <div class="col-md-10">
                <div class="card bg-gradient-primary border-0 shadow-sm" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);">
                    <div class="card-body text-center text-white py-4">
                        <i class="fas fa-crown fa-3x mb-2" style="color: #ffd700;"></i>
                        <h3 class="mb-1 fw-bold">{{ __('System Administration') }}</h3>
                        <p class="mb-0 opacity-75">{{ __('Platform Owner / Super Admin Dashboard') }}</p>
                        <small class="d-block mt-2 opacity-75">
                            <i class="fas fa-user-shield"></i> {{ auth()->user()->name }} |
                            <i class="fas fa-clock"></i> {{ now()->format('Y-m-d H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Row -->
        <div class="row justify-content-center align-items-center">
            <div class="col">
                <div class="row mt-4">
                    <!-- Total Users -->
                    <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
                        <div class="card border-left-primary shadow h-100" style="border-left: 4px solid #4e73df;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">{{ __('Total Users') }}</div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalUsers }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Shops -->
                    <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
                        <div class="card border-left-success shadow h-100" style="border-left: 4px solid #1cc88a;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-success text-uppercase mb-1">{{ __('Total Shops') }}</div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $totalShops }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-store fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Active Shops -->
                    <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
                        <div class="card border-left-info shadow h-100" style="border-left: 4px solid #36b9cc;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-info text-uppercase mb-1">{{ __('Active Shops') }}</div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $activeShops }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Orders -->
                    <div class="col-xl-3 col-md-6 col-sm-12 mb-3">
                        <div class="card border-left-warning shadow h-100" style="border-left: 4px solid #f6c23e;">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">{{ __('Today Orders') }}</div>
                                        <div class="h5 mb-0 fw-bold text-gray-800">{{ $todayOrders }}</div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Management Links -->
                <div class="row mt-4">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('system.users') }}" class="text-decoration-none">
                            <div class="card shadow h-100 py-3" style="border-left: 4px solid #4e73df;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">{{ __('User Management') }}</div>
                                            <div class="h6 mb-0 text-gray-800">{{ __('Manage all users') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-cog fa-2x text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="{{ route('system.shops') }}" class="text-decoration-none">
                            <div class="card shadow h-100 py-3" style="border-left: 4px solid #1cc88a;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs fw-bold text-success text-uppercase mb-1">{{ __('Shop Management') }}</div>
                                            <div class="h6 mb-0 text-gray-800">{{ __('Manage all shops') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-store-alt fa-2x text-success"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="{{ route('system.settings') }}" class="text-decoration-none">
                            <div class="card shadow h-100 py-3" style="border-left: 4px solid #f6c23e;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">{{ __('System Settings') }}</div>
                                            <div class="h6 mb-0 text-gray-800">{{ __('Configure platform') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-cogs fa-2x text-warning"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <a href="{{ route('system.activityLogs') }}" class="text-decoration-none">
                            <div class="card shadow h-100 py-3" style="border-left: 4px solid #e74a3b;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs fw-bold text-danger text-uppercase mb-1">{{ __('Activity Logs') }}</div>
                                            <div class="h6 mb-0 text-gray-800">{{ __('View system activity') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-history fa-2x text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6 mb-3">
                        <a href="{{ route('adminDashboard') }}" class="text-decoration-none">
                            <div class="card shadow h-100 py-3" style="border-left: 4px solid #6f42c1;">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs fw-bold text-secondary text-uppercase mb-1">{{ __('Admin Dashboard') }}</div>
                                            <div class="h6 mb-0 text-gray-800">{{ __('Access admin features') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-secondary"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity & Charts Row -->
                <div class="row mt-4">
                    <!-- Users by Role Chart -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-primary">{{ __('Users by Role') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Role') }}</th>
                                                <th class="text-end">{{ __('Count') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($usersByRole as $roleData)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $roleData->role === 'super_admin' ? 'danger' : ($roleData->role === 'admin' ? 'primary' : 'secondary') }}">
                                                            {{ ucfirst(str_replace('_', ' ', $roleData->role)) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">{{ $roleData->count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shops by Status -->
                    <div class="col-md-6 mb-4">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-success">{{ __('Shops by Status') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Status') }}</th>
                                                <th class="text-end">{{ __('Count') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($shopsByStatus as $statusData)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-{{ $statusData->status === 'Active' ? 'success' : ($statusData->status === 'Suspended' ? 'warning' : 'secondary') }}">
                                                            {{ $statusData->status }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">{{ $statusData->count }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Logs -->
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="card shadow">
                            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                <h6 class="m-0 font-weight-bold text-info">{{ __('Recent Activity') }}</h6>
                                <a href="{{ route('system.activityLogs') }}" class="btn btn-sm btn-primary">{{ __('View All') }}</a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Time') }}</th>
                                                <th>{{ __('User') }}</th>
                                                <th>{{ __('Action') }}</th>
                                                <th>{{ __('Description') }}</th>
                                                <th>{{ __('IP Address') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($recentActivity as $activity)
                                                <tr>
                                                    <td>{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                                                    <td>{{ $activity->user->name ?? 'System' }}</td>
                                                    <td>
                                                        <span class="badge bg-secondary">{{ str_replace('_', ' ', $activity->action) }}</span>
                                                    </td>
                                                    <td>{{ $activity->description }}</td>
                                                    <td><small class="text-muted">{{ $activity->ip_address }}</small></td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center">{{ __('No recent activity') }}</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
