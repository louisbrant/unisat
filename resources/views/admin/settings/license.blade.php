@section('site_title', formatTitle([__('License'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('License') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="font-weight-medium py-1">
            {{ __('License') }}
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form method="POST" action="{{ route('admin.settings', 'license') }}">
            @csrf

            <div class="form-group">
                <label for="i-license-key">{{ __('License key') }}</label>
                <input id="i-license-key" type="text" class="form-control{{ $errors->has('license_key') ? ' is-invalid' : '' }}" name="license_key" value="{{ old('license_key') }}" autofocus>
                @if ($errors->has('license_key'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('license_key') }}</strong>
                    </span>
                @endif
            </div>

            <div class="row mt-3">
                <div class="col">
                    <button type="submit" class="btn btn-primary">
                        {{ __('Save') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
