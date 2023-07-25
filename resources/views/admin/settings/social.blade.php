@section('site_title', formatTitle([__('Social'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Social') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Social') }}</div></div>
    <div class="card-body">

        @include('shared.message')

        <form action="{{ route('admin.settings', 'social') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label for="i-social-facebook">{{ __('Facebook') }}</label>
                <input type="text" dir="ltr" name="social_facebook" id="i-social-facebook" class="form-control{{ $errors->has('social_facebook') ? ' is-invalid' : '' }}" value="{{ old('social_facebook') ?? config('settings.social_facebook') }}">
                @if ($errors->has('social_facebook'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('social_facebook') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-social-twitter">{{ __('Twitter') }}</label>
                <input type="text" dir="ltr" name="social_twitter" id="i-social-twitter" class="form-control{{ $errors->has('social_twitter') ? ' is-invalid' : '' }}" value="{{ old('social_twitter') ?? config('settings.social_twitter') }}">
                @if ($errors->has('social_twitter'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('social_twitter') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-social-instagram">{{ __('Instagram') }}</label>
                <input type="text" dir="ltr" name="social_instagram" id="i-social-instagram" class="form-control{{ $errors->has('social_instagram') ? ' is-invalid' : '' }}" value="{{ old('social_instagram') ?? config('settings.social_instagram') }}">
                @if ($errors->has('social_instagram'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('social_instagram') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-social-youtube">{{ __('YouTube') }}</label>
                <input type="text" dir="ltr" name="social_youtube" id="i-social-youtube" class="form-control{{ $errors->has('social_youtube') ? ' is-invalid' : '' }}" value="{{ old('social_youtube') ?? config('settings.social_youtube') }}">
                @if ($errors->has('social_youtube'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('social_youtube') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>