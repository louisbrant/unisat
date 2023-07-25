@extends('layouts.auth')

@section('site_title', formatTitle([Str::ucfirst(mb_strtolower(__('Reset Password'))), config('settings.title')]))

@section('head_content')

@endsection

@section('content')
    <div class="bg-base-1 d-flex align-items-start align-items-lg-center flex-fill">
        <div class="container h-100 py-6">

            <div class="text-center d-block d-lg-none">
                <h1 class="h2 mb-3 d-inline-block">{{ Str::ucfirst(mb_strtolower(__('Reset Password'))) }}</h1>
                <div class="m-auto">
                    <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Get back your account.') }}</p>
                </div>
            </div>

            <div class="row h-100 justify-content-center align-items-center mt-5 mt-lg-0">
                <div class="col-12">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="row no-gutters">
                            <div class="col-12 col-lg-5">
                                <div class="card-body p-lg-5">
                                    @include('shared.message')

                                    @if (request()->session()->get('status'))
                                        <div class="alert alert-success" role="alert">
                                            {{ request()->session()->get('status') }}
                                        </div>
                                    @endif

                                    <form method="POST" action="{{ route('password.email') }}">
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

                                        <button type="submit" class="btn btn-block btn-primary">
                                            {{ __('Send Password Reset Link') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div class="col-12 col-lg-7 bg-dark d-none d-lg-flex flex-fill background-size-cover background-position-center" style="background-image: url({{ asset('img/password.svg') }})">
                                <div class="card-body p-lg-5 d-flex flex-column flex-fill position-absolute top-0 right-0 bottom-0 left-0">
                                    <div class="d-flex align-items-center d-flex flex-fill">
                                        <div class="text-light {{ (__('lang_dir') == 'rtl' ? 'mr-5' : 'ml-5') }}">
                                            <div class="h2 font-weight-bold">
                                                {{ Str::ucfirst(mb_strtolower(__('Reset Password'))) }}
                                            </div>
                                            <div class="font-size-lg font-weight-medium">
                                                {{ __('Get back your account.') }}
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
