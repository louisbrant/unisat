@extends('layouts.app')

@section('site_title', formatTitle([__('Contact'), config('settings.title')]))

@section('head_content')

@endsection

@section('content')
    <div class="bg-base-1 d-flex align-items-center flex-fill">
        <div class="container h-100 py-6">

            <div class="text-center d-block d-lg-none">
                <h1 class="h2 mb-3 d-inline-block">{{ __('Contact') }}</h1>
                <div class="m-auto">
                    <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Get in touch with us.') }}</p>
                </div>
            </div>

            <div class="row h-100 justify-content-center align-items-center mt-5 mt-lg-0">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="row no-gutters">
                            <div class="col-12 col-lg-5">
                                <div class="card-body p-lg-5">
                                    @include('shared.message')

                                    <form method="POST" action="{{ route('contact') }}" id="contact-form">
                                        @csrf

                                        <div class="form-group">
                                            <label for="i-email">{{ __('Email address') }}</label>
                                            <input id="i-email" type="text" dir="ltr" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" autofocus>
                                            @if ($errors->has('email'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('email') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="i-subject">{{ __('Subject') }}</label>
                                            <input id="i-subject" type="text" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="subject" value="{{ old('subject') }}" autofocus>
                                            @if ($errors->has('subject'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('subject') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <label for="i-message">{{ __('Message') }}</label>
                                            <textarea name="message" id="i-message" class="form-control{{ $errors->has('message') ? ' is-invalid' : '' }}">{{ old('message') }}</textarea>
                                            @if ($errors->has('message'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('message') }}</strong>
                                                </span>
                                            @endif
                                        </div>

                                        @if(config('settings.captcha_contact'))
                                            {!! NoCaptcha::displaySubmit('contact-form', __('Send'), ['data-theme' => (config('settings.dark_mode') == 1 ? 'dark' : 'light'), 'data-size' => 'invisible', 'class' => 'btn btn-block btn-primary']) !!}

                                            {!! NoCaptcha::renderJs(__('lang_code')) !!}
                                        @else
                                            <button type="submit" class="btn btn-block btn-primary">
                                                {{ __('Send') }}
                                            </button>
                                        @endif

                                        @if ($errors->has('g-recaptcha-response'))
                                            <span class="invalid-feedback d-block" role="alert">
                                                <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                                            </span>
                                        @endif
                                    </form>
                                </div>
                            </div>
                            <div class="col-12 col-lg-7 bg-dark d-none d-lg-flex flex-fill background-size-cover background-position-center" style="background-image: url({{ asset('img/contact.svg') }})">
                                <div class="card-body p-lg-5 d-flex flex-column flex-fill position-absolute top-0 right-0 bottom-0 left-0">
                                    <div class="d-flex align-items-center d-flex flex-fill">
                                        <div class="text-light {{ (__('lang_dir') == 'rtl' ? 'mr-5' : 'ml-5') }}">
                                            <div class="h2 font-weight-bold">
                                                {{ __('Contact') }}
                                            </div>
                                            <div class="font-size-lg font-weight-medium">
                                                {{ __('Get in touch with us.') }}
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

@include('shared.sidebars.user')
