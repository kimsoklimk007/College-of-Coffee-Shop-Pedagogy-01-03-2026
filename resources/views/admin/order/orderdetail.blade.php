@extends('admin.layouts.master')
@section('content')
    <section class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-lg-12 mt-4">
                <div class="card shadow-sm border-2">
                    <div class="card-body p-4">
                        <!-- Cart Section -->
                        <h3 class="mb-4 fw-bold text-center">{{ __('Order Details') }}</h3>

                        <div class="table-responsive">
                            <!-- Cart Table -->
                            <table class="table table-hover align-middle text-center">
                                <thead class="table-secondary">
                                    <tr>
                                        <th class="">{{ __('IMAGES') }}</th>
                                        <th class="text-center whitespace-nowrap">{{ __('PRODUCT NAME') }}</th>
                                        <th class="text-center whitespace-nowrap">{{ __('PRICE') }}</th>
                                        <th class="text-center whitespace-nowrap">{{ __('COUNT') }}</th>
                                        <th class="text-center whitespace-nowrap">{{ __('Size') }}</th>
                                        <th class="text-center whitespace-nowrap">{{ __('ORDER TYPE') }}</th>
                                        <th class="text-center whitespace-nowrap">{{ __('Notes') }}</th>
                                        @if (auth()->user()->role === 'chef')
                                            <th class="text-center whitespace-nowrap">{{ __('STATUS') }}</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($details as $item)
                                        <tr>
                                            <td>
                                                <img class="rounded-circle"
                                                    src="{{ asset('productImages/' . $item->image) }}" alt="Product Image"
                                                    style="width: 64px; height: 64px;">
                                            </td>
                                            <td class="align-middle">{{ $item->name }}</td>
                                            <td class="align-middle">{{ $item->price }}</td>
                                            <td class="align-middle">{{ $item->quantity }}</td>
                                            <td class="align-middle">{{ strtoupper(substr($item->size, 0, 1)) }}</td>

                                            <td class="align-middle text-danger fw-bolder">
                                                @switch($item->order_type)
                                                    @case(1)
                                                        {{ __('Take Away') }}
                                                        @break
                                                    @case(2)
                                                        {{ __('Eat In') }}
                                                        @break
                                                    @case(3)
                                                        {{ __('Delivery') }}
                                                        @break
                                                    @default
                                                        {{ __('Unknown') }}
                                                @endswitch
                                            </td>
                                            <td class="align-middle fw-bolder">
                                                @if ($item->notes)
                                                    <span class="badge bg-warning text-dark">{{ $item->notes }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            @if (auth()->user()->role === 'chef')
                                                <td class="align-middle">
                                                   @if ($item->status === '1')
                                                    <!-- <span class="badge bg-info">{{ __('Pending') }}</span> -->

                                                    <form action="{{ route('order.updateOrder') }}" method="POST" style="display:inline;">
                                                        @csrf
                                                        <input type="hidden" name="order_code" value="{{ $item->order_code }}">
                                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="action" value="accept">
                                                        <button type="submit" class="btn btn-info shadow-sm me-2">{{ __('Cooking') }}</button>
                                                    </form>


                                                @elseif ($item->status === '2')
                                                    <span class="badge bg-success">{{ __('Done') }}</span>
                                                @elseif ($item->status === '3')
                                                    <!-- <span class="badge bg-danger">{{ __('Rejected') }}</span> -->
                                                @endif

                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('order.orderlist') }}" class="btn text-start mb-2"
                        style="background-color: #4e2318; color: white;"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-return-left me-2" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M14.5 1.5a.5.5 0 0 1 .5.5v4.8a2.5 2.5 0 0 1-2.5 2.5H2.707l3.347 3.346a.5.5 0 0 1-.708.708l-4.2-4.2a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 8.3H12.5A1.5 1.5 0 0 0 14 6.8V2a.5.5 0 0 1 .5-.5"/>
                                </svg>{{ __('Back') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
