@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-history"></i> {{ __('Activity Logs') }}</h3>
            <a href="{{ route('system.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>

        <!-- Logs Table -->
        <div class="card shadow">
            <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('System Activity Logs') }}</h5>
                <span class="badge bg-light text-dark">{{ $logs->total() }} {{ __('records') }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Timestamp') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Action') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('IP Address') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <small>{{ $log->created_at->format('Y-m-d') }}</small><br>
                                        <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                                    </td>
                                    <td>
                                        @if($log->user)
                                            <span class="fw-bold">{{ $log->user->name }}</span><br>
                                            <small class="text-muted">{{ $log->user->role }}</small>
                                        @else
                                            <span class="text-muted">{{ __('System') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $actionColors = [
                                                'create_user' => 'success',
                                                'update_user' => 'primary',
                                                'delete_user' => 'danger',
                                                'toggle_user_status' => 'warning',
                                                'reset_password' => 'info',
                                                'create_shop' => 'success',
                                                'update_shop' => 'primary',
                                                'delete_shop' => 'danger',
                                                'toggle_shop_status' => 'warning',
                                                'assign_admin' => 'info',
                                                'login' => 'secondary',
                                                'logout' => 'secondary',
                                            ];
                                            $badgeColor = $actionColors[$log->action] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $badgeColor }}">
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $log->description }}</td>
                                    <td>
                                        <code class="small">{{ $log->ip_address }}</code>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">{{ __('No activity logs found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
