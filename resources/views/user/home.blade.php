@extends('user.layouts.master')
@section('content')
    <section id="hero" class="d-flex align-items-center justify-content-center"
        style="background-image: url('{{ asset('user/images/coffee.png') }}');
       background-size: cover; background-position: center; height: 60vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 text-center">
                    <div>
                        @if (isset($discountPercentage->discount_percentage) && $discountPercentage->discount_percentage > 0)
                            <h2 class="text-white display-4 font-weight-bold">Exclusive Offer</h2>
                            <p class="text-white lead">Get {{ intval($discountPercentage->discount_percentage) }} % off!</p>
                        @else
                            <h2 class="text-white display-4 font-weight-bold">Welcome to our shop</h2>
                            <p class="text-white lead">What would you like to order today?</p>
                        @endif

                    </div>
                    <button class="btn btn-lg btn-light text-primary shadow-lg rounded-pill"
                        onclick="location.href=''">ORDER NOW</button>
                </div>
            </div>
        </div>
    </section>

@endsection


