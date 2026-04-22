@extends('admin.layouts.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="mb-3 d-flex justify-content-between align-items-center">
                <h4>{{ __('Leave Details') }}</h4>
                <div>
                    <a href="{{ route('leave.edit', $leave->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> {{ __('Edit') }}
                    </a>
                    <a href="{{ route('leave.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> {{ __('Back') }}
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th>{{ __('Employee') }}:</th>
                                    <td>{{ $leave->employee->full_name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Leave Type') }}:</th>
                                    <td>{{ __($leave->leave_type) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Start Date') }}:</th>
                                    <td>{{ $leave->start_date }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('End Date') }}:</th>
                                    <td>{{ $leave->end_date }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Total Days') }}:</th>
                                    <td>{{ $leave->total_days }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Status') }}:</th>
                                    <td>
                                        <span class="badge bg-{{ $leave->status == 'Approved' ? 'success' : ($leave->status == 'Pending' ? 'warning' : 'danger') }}">
                                            {{ __($leave->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ __('Reason') }}:</th>
                                    <td>{{ $leave->reason }}</td>
                                </tr>
                                <tr>
                                    <th>{{ __('Notes') }}:</th>
                                    <td>{{ $leave->notes ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($leave->status == 'Pending')
                    <hr>
                    <div class="d-flex gap-2">
                        <form action="{{ route('leave.approve', $leave->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('{{ __('Approve this leave?') }}')">
                                <i class="fas fa-check"></i> {{ __('Approve') }}
                            </button>
                        </form>
                        <form action="{{ route('leave.reject', $leave->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger" onclick="return confirm('{{ __('Reject this leave?') }}')">
                                <i class="fas fa-times"></i> {{ __('Reject') }}
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
