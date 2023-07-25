@section('site_title', formatTitle([__('Security'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('account'), 'title' => __('Account')],
    ['title' => __('Security')]
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Security') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1">{{ __('Security') }}</div></div>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-registration-tab" data-toggle="pill" href="#pills-registration" role="tab" aria-controls="pills-registration" aria-selected="true">{{ __('Password') }}</a>
            </li>

            @if(config('settings.login_tfa'))
                <li class="nav-item flex-grow-1 text-center">
                    <a class="nav-link" id="pills-tfa-tab" data-toggle="pill" href="#pills-tfa" role="tab" aria-controls="pills-tfa" aria-selected="false">{{ __('Two-factor authentication') }}</a>
                </li>
            @endif
        </ul>
        
        @include('shared.message')

        <form action="{{ route('account.security') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-registration" role="tabpanel" aria-labelledby="pills-registration-tab">
                    <div class="form-group">
                        <label for="i-current-password">{{ __('Current password') }}</label>
                        <input type="password" name="current_password" id="i-current-password" class="form-control{{ $errors->has('current_password') ? ' is-invalid' : '' }}">
                        @if ($errors->has('current_password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('current_password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-password">{{ __('New password') }}</label>
                        <input type="password" name="password" id="i-password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}">
                        @if ($errors->has('password'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-password-confirmation">{{ __('Confirm new password') }}</label>
                        <input type="password" name="password_confirmation" id="i-password-confirmation" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}">
                        @if ($errors->has('password_confirmation'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                @if(config('settings.login_tfa'))
                    <div class="tab-pane fade" id="pills-tfa" role="tabpanel" aria-labelledby="pills-tfa-tab">
                        <div class="form-group">
                            <label for="i-tfa">{{ __('Email') }}</label>
                            <select name="tfa" id="i-tfa" class="custom-select{{ $errors->has('tfa') ? ' is-invalid' : '' }}">
                                @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                                    <option value="{{ $key }}" @if ((old('tfa') !== null && old('tfa') == $key) || ($user->tfa == $key && old('tfa') == null)) selected @endif>{{ $value }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tfa'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('tfa') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>