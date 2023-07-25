@extends('layouts.app')

@section('site_title', formatTitle([Str::ucfirst(mb_strtolower(__('Verify Account'))), config('settings.title')]))

@section('content')
<div class="bg-base-1 d-flex align-items-center flex-fill">
    <div class="container">
        <div class="h-100 d-flex flex-column justify-content-center align-items-center my-5">
            @if (request()->session()->get('resent'))
                <div class="alert alert-success mb-5" role="alert">
                    {{ __('A new verification link has been sent to your email address.') }}
                </div>
            @endif

            <div class="position-relative width-32 height-32 d-flex align-items-center justify-content-center">
                <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 rounded-circle"></div>

                @include('icons.email', ['class' => 'text-primary fill-current width-16 height-16'])

                <div class="position-absolute right-0 bottom-0 bg-secondary width-8 height-8 rounded-circle d-flex align-items-center justify-content-center">
                    @include('icons.more-horiz', ['class' => 'text-light fill-current width-4 height-4'])
                </div>
            </div>

            <div>
                <h5 class="mt-4 text-center">{{ Str::ucfirst(mb_strtolower(__('Verify Account'))) }}</h5>
                <p class="text-center text-muted">{{ __('Verify your account by accessing the link sent through email.') }}</p>

                <div class="text-center mt-5">
                    <div class="text-center text-muted">
                        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}" id="resend-form">
                            @csrf

                            {{ __('Didn\'t receive the email?') }} <a href="{{ route('verification.resend') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">{{ __('Resend') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection