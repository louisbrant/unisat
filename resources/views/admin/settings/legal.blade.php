@section('site_title', formatTitle([__('Legal'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Legal') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Legal') }}</div></div>
    <div class="card-body">

        @include('shared.message')

        <form action="{{ route('admin.settings', 'legal') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label for="i-legal-terms-url">{{ __(':name URL', ['name' => __('Terms of service')]) }}</label>
                <input type="text" dir="ltr" name="legal_terms_url" id="i-legal-terms-url" class="form-control{{ $errors->has('legal_terms_url') ? ' is-invalid' : '' }}" value="{{ old('legal_terms_url') ?? config('settings.legal_terms_url') }}">
                @if ($errors->has('legal_terms_url'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('legal_terms_url') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-privacy-url">{{ __(':name URL', ['name' => __('Privacy policy')]) }}</label>
                <input type="text" dir="ltr" name="legal_privacy_url" id="i-privacy-url" class="form-control{{ $errors->has('legal_privacy_url') ? ' is-invalid' : '' }}" value="{{ old('legal_privacy_url') ?? config('settings.legal_privacy_url') }}">
                @if ($errors->has('legal_privacy_url'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('legal_privacy_url') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-cookie-url">{{ __(':name URL', ['name' => __('Cookie policy')]) }}</label>
                <input type="text" dir="ltr" name="legal_cookie_url" id="i-cookie-url" class="form-control{{ $errors->has('legal_cookie_url') ? ' is-invalid' : '' }}" value="{{ old('legal_cookie_url') ?? config('settings.legal_cookie_url') }}">
                @if ($errors->has('legal_cookie_url'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('legal_cookie_url') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>