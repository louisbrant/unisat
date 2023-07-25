@extends('layouts.app')

@section('site_title', formatTitle([__('Payment completed'), config('settings.title')]))

@section('content')
<div class="bg-base-1 d-flex align-items-center flex-fill">
    <div class="container">
        <div class="h-100 d-flex flex-column justify-content-center align-items-center my-5">
            <div class="position-relative width-32 height-32 d-flex align-items-center justify-content-center">
                <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 rounded-circle"></div>

                @include('icons.credit-card', ['class' => 'text-primary fill-current width-16 height-16'])

                <div class="position-absolute right-0 bottom-0 bg-success width-8 height-8 rounded-circle d-flex align-items-center justify-content-center">
                    @include('icons.checkmark', ['class' => 'text-light fill-current width-4 height-4'])
                </div>
            </div>

            <div>
                <h5 class="mt-4 text-center">{{ __('Payment completed') }}</h5>
                <p class="text-center text-muted">{{ __('The payment was successful.') }}</p>

                <div class="text-center mt-5">
                    <a href="{{ route('home') }}" class="btn btn-primary">{{ __('Dashboard') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('shared.sidebars.user')