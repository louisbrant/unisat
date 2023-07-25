@extends('layouts.app')

@section('site_title', formatTitle([__('Security check'), config('settings.title')]))

@section('content')
<div class="bg-base-1 d-flex align-items-start align-items-lg-center flex-fill">
    <div class="container h-100 py-6">

        <div class="text-center d-block d-lg-none">
            <h1 class="h2 mb-3 d-inline-block">{{ __('Login') }}</h1>
            <div class="m-auto">
                <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Welcome back.') }}</p>
            </div>
        </div>

        <div class="row h-100 justify-content-center align-items-center mt-5 mt-lg-0">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="row no-gutters">
                        <div class="col-12 col-lg-5">
                            <div class="card-body p-lg-5">
                                @include('shared.message')

                                @if (!request()->session()->get('success'))
                                    <div class="alert alert-secondary">
                                        {{ __('A security code has been sent to your email address.') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login.tfa.validate') }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="i-code">{{ __('Code') }}</label>
                                        <input id="i-code" type="text" dir="ltr" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ old('code') }}" autofocus>
                                        @if ($errors->has('code'))
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('code') }}</strong>
                                        </span>
                                        @endif
                                    </div>

                                    <button type="submit" class="btn btn-block btn-primary py-2">
                                        {{ __('Validate') }}
                                    </button>
                                </form>
                            </div>
                            <div class="card-footer bg-base-2 border-0">
                                <div class="text-center text-muted my-2">
                                    <form class="d-inline" method="POST" action="{{ route('login.tfa.resend') }}" id="resend-form">
                                        @csrf

                                        {{ __('Didn\'t receive the email?') }} <a href="{{ route('login.tfa.resend') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">{{ __('Resend') }}</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7 bg-dark d-none d-lg-flex flex-fill background-size-cover background-position-center" style="background-image: url({{ asset('img/login.svg') }})">
                            <div class="card-body p-lg-5 d-flex flex-column flex-fill position-absolute top-0 right-0 bottom-0 left-0">
                                <div class="d-flex align-items-center d-flex flex-fill">
                                    <div class="text-light {{ (__('lang_dir') == 'rtl' ? 'mr-5' : 'ml-5') }}">
                                        <div class="h2 font-weight-bold">
                                            {{ __('Security check') }}
                                        </div>
                                        <div class="font-size-lg font-weight-medium">
                                            {{ __('Confirm your identity.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
