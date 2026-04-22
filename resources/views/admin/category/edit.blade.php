@extends('admin.layouts.master')

@section('content')
<section class="container-fluid">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h2 class="text-lg fw-bold text-center mb-4">{{ __('Update Category') }}</h2>
                    <form action="{{ route('category.update', $data->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="category" class="form-label">{{ __('English Name') }}</label>
                            <input type="hidden" name="categoryID" value="{{ $data->id }}">
                            <input type="text" name="category" value="{{ old('category', $data->name) }}"
                                   class="form-control @error('category') is-invalid @enderror"
                                   id="category" placeholder="Cake, Juice, Coffee...">
                            @error('category')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="khmer_name" class="form-label">{{ __('Khmer Name') }}</label>
                            <input type="text" name="khmer_name" value="{{ old('khmer_name', $data->khmer_name) }}"
                                   class="form-control @error('khmer_name') is-invalid @enderror"
                                   id="khmer_name" placeholder="នំខេក, ទឹកផ្លែឈើ, កាហ្វេ...">
                            @error('khmer_name')
                                <small class="invalid-feedback">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <input type="submit" value="{{ __('Update') }}" class="btn btn-primary rounded-pill w-100">
                            </div>
                            <div class="col-6">
                                <a href="{{ route('category.list') }}" class="btn btn-secondary rounded-pill w-100 text-center">{{ __('Back') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>


@endsection

