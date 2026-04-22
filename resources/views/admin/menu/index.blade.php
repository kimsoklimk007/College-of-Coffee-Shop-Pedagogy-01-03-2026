@extends('admin.layouts.master')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>{{ __('Menu & Pricing') }}</h4>
                    <div>
                        <button class="btn btn-info" onclick="generateMenu()">
                            <i class="fas fa-sync"></i> {{ __('Generate Menu') }}
                        </button>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#telegramModal">
                            <i class="fab fa-telegram"></i> {{ __('Send to Telegram') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="menuContent">
                        @foreach($categories as $category)
                        <div class="mb-4">
                            <h5 class="bg-light p-2">{{ $category->name }}</h5>
                            <div class="row">
                                @foreach($category->products as $product)
                                <div class="col-md-4 mb-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6>{{ $product->name }}</h6>
                                            @if($product->image)
                                                <img src="{{ asset($product->image) }}" class="img-fluid mb-2" style="max-height: 100px;">
                                            @endif
                                            <table class="table table-sm">
                                                @foreach($product->sizes as $size)
                                                <tr>
                                                    <td>{{ $size->size ?? 'Regular' }}</td>
                                                    <td>${{ number_format($size->price_usd, 2) }}</td>
                                                    <td>
                                                        @php
                                                            $discount = $product->discounts->firstWhere('product_size_id', $size->id);
                                                        @endphp
                                                        @if($discount)
                                                            <span class="badge bg-danger">{{ $discount->discount_percent }}% OFF</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                                @if($product->sizes->isEmpty())
                                                <tr>
                                                    <td>Price</td>
                                                    <td>${{ number_format($product->price_usd, 2) }}</td>
                                                    <td></td>
                                                </tr>
                                                @endif
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="telegramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Send Menu to Telegram') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('menu.sendTelegram') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Bot Token') }} *</label>
                        <input type="text" name="bot_token" class="form-control" required placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Chat ID') }} *</label>
                        <input type="text" name="chat_id" class="form-control" required placeholder="-123456789">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Menu Text') }}</label>
                        <textarea name="menu_text" class="form-control" rows="10" id="menuText"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Send') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function generateMenu() {
    fetch('{{ route("menu.generate") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('menuText').value = data.menu;
                alert('Menu generated! Copy the text and send to Telegram.');
            }
        });
}
</script>
@endsection
