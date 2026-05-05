@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-chart-bar"></i> {{ __('System Reports') }}</h3>
            <a href="{{ route('system.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
            </a>
        </div>

        <!-- Revenue Overview -->
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <div class="card border-left-primary shadow h-100" style="border-left: 4px solid #4e73df;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-primary text-uppercase mb-1">{{ __('Total Revenue') }}</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">${{ number_format($totalRevenue, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="card border-left-success shadow h-100" style="border-left: 4px solid #1cc88a;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs fw-bold text-success text-uppercase mb-1">{{ __('Monthly Revenue') }}</div>
                                <div class="h5 mb-0 fw-bold text-gray-800">${{ number_format($monthlyRevenue, 2) }}</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders by Month -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-chart-line"></i> {{ __('Orders by Month') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Month') }}</th>
                                <th class="text-end">{{ __('Order Count') }}</th>
                                <th class="text-end">{{ __('Revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ordersByMonth as $data)
                                <tr>
                                    <td>{{ date('F', mktime(0, 0, 0, $data->month, 1)) }} {{ $data->year }}</td>
                                    <td class="text-end">{{ number_format($data->count) }}</td>
                                    <td class="text-end">${{ number_format($data->revenue, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4">{{ __('No data available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top Shops -->
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-store"></i> {{ __('Top Performing Shops') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('Rank') }}</th>
                                <th>{{ __('Shop Name') }}</th>
                                <th class="text-end">{{ __('Orders') }}</th>
                                <th class="text-end">{{ __('Revenue') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topShops as $index => $shop)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $index < 3 ? 'warning' : 'secondary' }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td>{{ $shop->name }}</td>
                                    <td class="text-end">{{ number_format($shop->orders_count) }}</td>
                                    <td class="text-end">${{ number_format($shop->orders_sum_totalprice ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">{{ __('No shop data available') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
