@extends('admin.layouts.master')

@section('content')
    <section class="container">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold"><i class="fas fa-store-alt"></i> {{ __('Shop Management') }}</h3>
            <a href="{{ route('system.shops.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('Create Shop') }}
            </a>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('system.shops') }}" class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="search" class="form-control" placeholder="{{ __('Search by name or code') }}" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">{{ __('All Status') }}</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                            <option value="Suspended" {{ request('status') == 'Suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
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

        <!-- Shops Table -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Code') }}</th>
                                <th>{{ __('Owner') }}</th>
                                <th>{{ __('Contact') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shops as $shop)
                                <tr>
                                    <td>{{ $shop->id }}</td>
                                    <td>{{ $shop->name }}</td>
                                    <td><code>{{ $shop->code }}</code></td>
                                    <td>
                                        @if($shop->owner)
                                            <span class="badge bg-primary">{{ $shop->owner->name }}</span>
                                        @else
                                            <span class="badge bg-secondary">{{ __('No Owner') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($shop->phone)
                                            <small><i class="fas fa-phone"></i> {{ $shop->phone }}</small><br>
                                        @endif
                                        @if($shop->email)
                                            <small><i class="fas fa-envelope"></i> {{ $shop->email }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $shop->status === 'Active' ? 'success' : ($shop->status === 'Suspended' ? 'warning' : 'secondary') }}">
                                            {{ $shop->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('system.shops.edit', $shop) }}" class="btn btn-sm btn-outline-primary" title="{{ __('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="{{ route('system.shops.assignAdmin', $shop) }}" class="btn btn-sm btn-outline-info" title="{{ __('Assign Admin') }}">
                                                <i class="fas fa-user-shield"></i>
                                            </a>

                                            <form action="{{ route('system.shops.toggleStatus', $shop) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $shop->status === 'Active' ? 'warning' : 'success' }}" title="{{ $shop->status === 'Active' ? __('Suspend') : __('Activate') }}">
                                                    <i class="fas fa-{{ $shop->status === 'Active' ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('system.shops.delete', $shop) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this shop?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">{{ __('No shops found') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $shops->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
