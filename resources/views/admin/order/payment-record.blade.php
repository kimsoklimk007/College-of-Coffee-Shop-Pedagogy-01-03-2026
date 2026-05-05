@extends('admin.layouts.master')
@section('content')
    <section class="container mt-2">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6">
                <div class="card-body mt-2">
                    <!-- Search by ORDER Code -->
                    <div class="payment-card p-4 shadow-lg rounded-3" style="background-color: #50301a;">
                        <!-- Search Form -->
                        <div class="row align-items-center mb-4">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <form action="{{ route('searchRecord') }}" method="GET">
                                    <label for="orderCode" class="form-label text-white">{{ __('Search...') }}</label>
                                    <div class="input-group">
                                        <input type="text" name="searchKey" value="{{ request('searchKey') }}"
                                               class="form-control" placeholder="{{ __('Type Order Code...') }}">
                                        <button type="submit" class="btn btn-outline-secondary">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            @if ($records->isNotEmpty())
                                <div class="col-lg-6 text-lg-end text-center mt-4">
                                    <button class="btn btn-outline-light w-100" id="printButton"
                                            data-order-code="{{ $records->first()->order_code }}">
                                        <i class="fa fa-print me-2"></i>{{ __('Print') }}
                                    </button>
                                </div>
                            @endif
                        </div>

                        <!-- Show if no records are found -->
                        @if ($records->isEmpty())
                            <small class="text-light">{{ __('No records found. Please search by Order Code.') }}</small>
                        @else
                            <!-- Payment Slip Table -->
                            <div class="slip-container bg-white text-black rounded-3" >
                                <!-- Business Header -->
                                <div style="text-align: center; margin-bottom: 10px; padding: 10px;">
                                    @if(business_logo())
                                        <img src="{{ business_logo() }}" alt="Logo" style="max-height: 40px; margin-bottom: 5px;">
                                    @endif
                                    <h5 style="margin: 0; font-weight: bold;">{{ business('business_name_kh', 'កាហ្វេ គរុកោសល្យ និងមីនីម៉ាត') }}</h5>
                                    <p style="margin: 0; font-size: 12px;">{{ business('business_name_en', 'BTEC Cafe & Mini Mart') }}</p>
                                    @if(business_address())
                                        <p style="margin: 0; font-size: 10px; color: #666;">{{ business_address() }}</p>
                                    @endif
                                    @if(business_phone())
                                        <p style="margin: 0; font-size: 10px; color: #666;">{{ __('Tel:') }} {{ business_phone() }}</p>
                                    @endif
                                </div>
                                <hr style="border: none; border-top: 1px dashed #000; margin: 5px 10px;">

                                <div class="text-center mb-3">
                                    <h5 style="margin: 0; font-weight: bold;">{{ __('Payment Slip') }}</h5>
                                </div>
                                <div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <div>{{ __('Cashier ID:') }} {{ $records->first()->CashierID ?? 'N/A' }}</div>
                                        <div>{{ __('Date:') }} {{ $records->first()->Date ?? 'N/A' }}</div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between;">
                                        <div>{{ $records->first()->order_code ?? 'N/A' }}</div>
                                        <div>{{ __('Paid by:') }} {{ $records->first()->payment_method ?? 'N/A' }}</div>
                                    </div>
                                </div>

                                <!-- Table -->
                                <table style="width: 100%; margin-top: 10px; border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left; border-bottom: 1px solid #000;">{{ __('Name') }}</th>
                                            <th style="text-align: center; border-bottom: 1px solid #000;">{{ __('Qty') }}</th>
                                            <th style="text-align: right; border-bottom: 1px solid #000;">{{ __('Price') }}</th>
                                            <th style="text-align: right; border-bottom: 1px solid #000;">{{ __('Size') }}</th>
                                            <th style="text-align: right; border-bottom: 1px solid #000;">{{ __('Discount') }}</th>
                                            <th style="text-align: right; border-bottom: 1px solid #000;">{{ __('Amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($records as $item)
                                            <tr>
                                                <td style="padding: 5px 0;">{{ $item->ProductName }}</td>
                                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                                <td style="text-align: right;">{{ $item->price }}</td>
                                                <td style="text-align: right;">{{ strtoupper(substr($item->size, 0, 1)) }}</td>
                                                <td style="text-align: right;">{{ $item->discount_percentage ?? 0 }}%</td>
                                                <td style="text-align: right;">{{ number_format($item->total_price, 2, '.', ',') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div style="border-top: 1px solid #000; margin-top: 10px; padding-top: 5px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="text-align: left;">{{ __('Sub Total') }}</td>
                                            <td style="text-align: right;">{{ number_format($subTotalAmt, 2, '.', ',') }}</td>
                                        </tr>
                                        {{-- <tr>
                                            <td style="text-align: left;">Discount</td>
                                            <td style="text-align: right;">{{ $records->first()->discount }}</td>
                                        </tr> --}}
                                        <tr>
                                            <td style="text-align: left;">{{ __('Tax') }}</td>
                                            <td style="text-align: right;">{{ $taxAmount }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">{{ __('Deli Fees:') }}</td>
                                            <td style="text-align: right;">{{ number_format($delivery_fee, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td style="text-align: left;">{{ __('Net Amount') }}</td>
                                            <td style="text-align: right;">{{ $records->first()->paid_amount - $records->first()->change_amount }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div style="border-top: 1px solid #000; margin-top: 10px; padding-top: 5px;">
                                    <table style="width: 100%; border-collapse: collapse;">
                                        <tr>
                                            <td style="text-align: left;">{{ __('Paid Amount') }}</td>
                                            <td style="text-align: right;">{{ $records->first()->paid_amount }}</td>
                                        </tr>
                                        <tr>
                                            <td style="text-align: left;">{{ __('Change') }}</td>
                                            <td style="text-align: right;">{{ $records->first()->change_amount }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="text-center" style="font-weight: bold;">{{ __('Thank You') }}</div>

                                <!-- Receipt Footer -->
                                @if(receipt_footer())
                                    <div style="text-align: center; margin-top: 5px; font-size: 11px; color: #666;">
                                        {{ receipt_footer() }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
<script>

    document.getElementById('printButton').addEventListener('click', function () {
        const orderCode = this.getAttribute('data-order-code');

        const printWindow = window.open(`/admin/order/printPaymentSlip/${orderCode}`, "_blank", "width=600,height=400");

    printWindow.onload = function () {
            setTimeout(() => {
                printWindow.print();
            }, 10000);

        };
    });


</script>

@endsection

