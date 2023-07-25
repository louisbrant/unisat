@section('site_title', formatTitle([__('Authentication'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Authentication') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Authentication') }}</div></div>
    <div class="card-body">
        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-registration-tab" data-toggle="pill" href="#pills-registration" role="tab" aria-controls="pills-registration" aria-selected="true">{{ __('Registration') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-login-tab" data-toggle="pill" href="#pills-login" role="tab" aria-controls="pills-login" aria-selected="false">{{ __('Login') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('admin.settings', 'authentication') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-registration" role="tabpanel" aria-labelledby="pills-registration-tab">
                    <div class="form-group">
                        <label for="i-registration">{{ __('Registration') }}</label>
                        <select name="registration" id="i-registration" class="custom-select{{ $errors->has('registration') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('registration') !== null && old('registration') == $key) || (config('settings.registration') == $key && old('registration') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('registration'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('registration') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-registration-verification">{{ __('Email verification') }}</label>
                        <select name="registration_verification" id="i-registration-verification" class="custom-select{{ $errors->has('registration_verification') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('registration_verification') !== null && old('registration_verification') == $key) || (config('settings.registration_verification') == $key && old('registration_verification') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('registration_verification'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('registration_verification') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-registration-tfa" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Two-factor authentication') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                        <select name="registration_tfa" id="i-registration-tfa" class="custom-select{{ $errors->has('registration_tfa') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('registration_tfa') !== null && old('registration_tfa') == $key) || (config('settings.registration_tfa') == $key && old('registration_tfa') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('registration_tfa'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('registration_tfa') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-login" role="tabpanel" aria-labelledby="pills-login-tab">
                    <div class="form-group">
                        <label for="i-login-tfa">{{ __('Two-factor authentication') }}</label>
                        <select name="login_tfa" id="i-login-tfa" class="custom-select{{ $errors->has('login_tfa') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('login_tfa') !== null && old('login_tfa') == $key) || (config('settings.login_tfa') == $key && old('login_tfa') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('login_tfa'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('login_tfa') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>