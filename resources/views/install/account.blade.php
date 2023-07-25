@section('site_title', formatTitle([__('Installation'), config('info.software.name')]))

<form action="{{ route('install.account') }}" method="post">
    @csrf

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-header">
            <div class="font-weight-medium py-1">{{ __('Admin credentials') }}</div>
        </div>

        <div class="card-body">
            @include('shared.message')

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
                <input id="i-password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" value="{{ old('password') }}">
                @if ($errors->has('password'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-password-confirmation">{{ __('Confirm password') }}</label>
                <input id="i-password-confirmation" type="password" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}" name="password_confirmation" value="{{ old('password_confirmation') }}">
            </div>

            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input{{ $errors->has('newsletter') ? ' is-invalid' : '' }}" name="newsletter" id="i-newsletter">
                <label class="custom-control-label" for="i-newsletter">
                    {{ __('Newsletter') }}
                    <small id="passwordHelpInline" class="d-block text-muted">
                        {{ __('Get notified when we launch a new product or run a sale.') }}
                    </small>
                </label>
                @if ($errors->has('newsletter'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('newsletter') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
        <span class="d-inline-flex align-items-center mx-auto">
            {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
        </span>
    </button>
</form>