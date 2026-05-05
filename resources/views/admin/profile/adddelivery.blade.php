@extends('admin.layouts.master')

@section('content')

<section class="container-fluid py-4 modern-tax-section">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-6 ">
            <div class="card p-3 shadow-sm">
                <h3 class="text-dark fw-bold mb-4 text-center">{{ __('Add Delivery Info') }}</h3>

                <!-- Existing Locations Dropdown -->
                <div class="mb-2">

                    <label for="location_name" class="form-label fw-semibold">{{ __('Check Existing Location') }}</label>
                    <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" id="location_id">
                    <!-- <select name="location_name" class="form-select @error('location_name') is-invalid @enderror" id="location_name"> -->
                        <option value="">{{ __('Choose existing location...') }}</option>
                        @foreach ($locations as $item)
                            @php
                                $displayCity = app()->getLocale() == 'km' && $item->city_kh ? $item->city_kh : $item->city;
                                $displayTownship = app()->getLocale() == 'km' && $item->township_kh ? $item->township_kh : $item->township;
                            @endphp
                            <option value="{{ $item->id  }}"
                                    data-city="{{ $item->city }}"
                                    data-city-kh="{{ $item->city_kh }}"
                                    data-township="{{ $item->township }}"
                                    data-township-kh="{{ $item->township_kh }}"
                                    data-fee="{{ $item->fees }}"
                                @if (old('location_id') == $item->id) selected @endif>
                                {{ $displayCity }} - {{ $displayTownship }} - {{ $item->fees }} Ks
                            </option>
                        @endforeach
                    </select>

                </div>

                <hr>

                <!-- Delivery Info Form -->
                <small class="fw-semibold mb-2">{{ __('Add New or Update') }}</small>
                <form action="{{ route('addDeliFees')}}" method="POST">
                    @csrf

                    <div class="form-floating mb-4">
                        <input type="text" name="city" id="city"
                            class="form-control @error('city') is-invalid @enderror"
                            placeholder="{{ __('Enter new city...') }}">
                        <label for="city">{{ __('City') }} (English)</label>
                        @error('city')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" name="city_kh" id="city_kh"
                            class="form-control @error('city_kh') is-invalid @enderror"
                            placeholder="{{ __('Enter city in Khmer...') }}">
                        <label for="city_kh">{{ __('City') }} (Khmer)</label>
                        @error('city_kh')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" name="township" id="township"
                            class="form-control @error('township') is-invalid @enderror"
                            placeholder="{{ __('Enter new township...') }}">
                        <label for="township">{{ __('Township') }} (English)</label>
                        @error('township')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="form-floating mb-4">
                        <input type="text" name="township_kh" id="township_kh"
                            class="form-control @error('township_kh') is-invalid @enderror"
                            placeholder="{{ __('Enter township in Khmer...') }}">
                        <label for="township_kh">{{ __('Township') }} (Khmer)</label>
                        @error('township_kh')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-floating mb-4">
                        <input type="number" name="deli_fees" id="deli_fees"
                            class="form-control @error('deli_fees') is-invalid @enderror"
                            placeholder="{{ __('Enter delivery fees') }}">
                        <label for="deli_fees">{{ __('Delivery Fees') }}</label>
                        @error('deli_fees')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <button type="submit" value="add" name="action"
                                class="btn btn-primary w-100 shadow-sm rounded-pill">
                                <i class="fas fa-plus-circle me-1"></i> {{ __('Save') }}
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" name="action" value="update"
                                class="btn btn-dark w-100 shadow-sm rounded-pill">
                                <i class="fas fa-sync-alt me-1"></i> {{ __('Update') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@endsection
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const select = document.getElementById('location_id');
        const cityInput = document.getElementById('city');
        const cityKhInput = document.getElementById('city_kh');
        const townshipInput = document.getElementById('township');
        const townshipKhInput = document.getElementById('township_kh');
        const feeInput = document.getElementById('deli_fees');

        select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const city = selectedOption.getAttribute('data-city');
            const cityKh = selectedOption.getAttribute('data-city-kh');
            const township = selectedOption.getAttribute('data-township');
            const townshipKh = selectedOption.getAttribute('data-township-kh');
            const fee = selectedOption.getAttribute('data-fee');

            if (city && township && fee !== null) {
                cityInput.value = city;
                cityKhInput.value = cityKh || '';
                townshipInput.value = township;
                townshipKhInput.value = townshipKh || '';
                feeInput.value = fee;
                } else {
                    cityInput.value = '';
                    cityKhInput.value = '';
                    townshipInput.value = '';
                    townshipKhInput.value = '';
                    feeInput.value = '';
                }
        });
    });
</script>
