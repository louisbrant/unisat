@extends('layouts.auth')

@section('site_title', formatTitle([__('Register'), config('settings.title')]))

@section('head_content')

@endsection

@section('content')
<div class="bg-base-1 d-flex align-items-center flex-fill">
    <div class="container h-100 py-6">

        <div class="text-center d-block d-lg-none">
            <h1 class="h2 mb-3 d-inline-block">{{ __('Register') }}</h1>
            <div class="m-auto">
                <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Join us.') }}</p>
            </div>
        </div>

        <div class="row h-100 justify-content-center align-items-center mt-5 mt-lg-0">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="row no-gutters">
                        <div class="col-12 col-lg-5">
                            <div class="card-body p-lg-5">
                                <form method="POST" action="{{ route('register') }}" id="registration-form">
                                    @csrf

                                    <div class="form-group">
                                        <label for="i-name">{{ __('Name') }}</label>
                                        <input id="i-name" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" autofocus>
                                        @if ($errors->has('name'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="i-email">{{ __('Email address') }}</label>
                                        <input id="i-email" type="text" dir="ltr" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}">
                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="i-password">{{ __('Password') }}</label>
                                        <input id="i-password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <label for="i-password-confirmation">{{ __('Confirm password') }}</label>
                                        <input id="i-password-confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation">
                                    </div>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input{{ $errors->has('agreement') ? ' is-invalid' : '' }}" name="agreement" id="i-agreement">
                                            <label class="custom-control-label" for="i-agreement">{!! __('I agree to the :terms and :privacy.', ['terms' => mb_strtolower('<a href="'.config('settings.legal_terms_url').'" target="_blank">'. __('Terms of service').'</a>'), 'privacy' => mb_strtolower('<a href="'.config('settings.legal_privacy_url').'" target="_blank">'. __('Privacy policy') .'</a>')]) !!}</label>
                                            @if ($errors->has('agreement'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('agreement') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(config('settings.captcha_registration'))
                                        {!! NoCaptcha::displaySubmit('registration-form', __('Register'), ['data-theme' => (config('settings.dark_mode') == 1 ? 'dark' : 'light'), 'data-size' => 'invisible', 'class' => 'btn btn-block btn-primary py-2']) !!}

                                        {!! NoCaptcha::renderJs(__('lang_code')) !!}
                                    @else
                                        <button type="submit" class="btn btn-block btn-primary py-2">
                                            {{ __('Register') }}
                                        </button>
                                    @endif

                                    @if ($errors->has('g-recaptcha-response'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                        </span>
                                    @endif
                                </form>
                            </div>
                            <div class="card-footer bg-base-2 border-0">
                                <div class="text-center text-muted my-2">{{ __('Already have an account?') }} <a href="{{ route('login') }}" role="button">{{ __('Login') }}</a></div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-7 bg-dark d-none d-lg-flex flex-fill background-size-cover background-position-center" style="background-image: url({{ asset('img/register.svg') }})">
                            <div class="card-body p-lg-5 d-flex flex-column flex-fill position-absolute top-0 right-0 bottom-0 left-0">
                                <div class="d-flex align-items-center d-flex flex-fill">
                                    <div class="text-light {{ (__('lang_dir') == 'rtl' ? 'mr-5' : 'ml-5') }}">
                                        <div class="h2 font-weight-bold">
                                            {{ __('Register') }}
                                        </div>
                                        <div class="font-size-lg font-weight-medium">
                                            {{ __('Join us.') }}
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
