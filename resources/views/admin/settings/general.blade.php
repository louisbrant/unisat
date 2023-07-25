@section('site_title', formatTitle([__('General'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('General') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('General') }}</div></div>
    <div class="card-body">

        @include('shared.message')

        <form action="{{ route('admin.settings', 'general') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="form-group">
                <label for="i-title">{{ __('Title') }}</label>
                <input type="text" name="title" id="i-title" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" value="{{ old('title') ?? config('settings.title') }}">
                @if ($errors->has('title'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('title') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-tagline">{{ __('Tagline') }}</label>
                <input type="text" name="tagline" id="i-tagline" class="form-control{{ $errors->has('tagline') ? ' is-invalid' : '' }}" value="{{ old('tagline') ?? config('settings.tagline') }}">
                @if ($errors->has('tagline'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('tagline') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-index">{{ __('Custom index') }}</label>
                <input type="text" dir="ltr" name="index" id="i-index" class="form-control{{ $errors->has('index') ? ' is-invalid' : '' }}" value="{{ old('index') ?? config('settings.index') }}">
                @if ($errors->has('index'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('index') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-paginate" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Results per page') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                <select name="paginate" id="i-paginate" class="custom-select{{ $errors->has('paginate') ? ' is-invalid' : '' }}">
                    @foreach([10, 25, 50, 100] as $value)
                        <option value="{{ $value }}" @if ((old('paginate') !== null && old('paginate') == $value) || (config('settings.paginate') == $value && old('paginate') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('paginate'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('paginate') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-locale" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Language') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                <select name="locale" id="i-locale" class="custom-select{{ $errors->has('locale') ? ' is-invalid' : '' }}">
                    @foreach(config('app.locales') as $code => $language)
                        <option value="{{ $code }}" @if ((old('locale') !== null && old('locale') == $code) || (config('settings.locale') == $code && old('locale') == null)) selected @endif>{{ $language['name'] }}</option>
                    @endforeach
                </select>
                @if ($errors->has('locale'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('locale') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-timezone">{{ __('Timezone') }}</label>
                <select name="timezone" id="i-timezone" class="custom-select{{ $errors->has('timezone') ? ' is-invalid' : '' }}">
                    @foreach(timezone_identifiers_list() as $value)
                        <option value="{{ $value }}" @if ((old('timezone') !== null && old('timezone') == $value) || (config('settings.timezone') == $value && old('timezone') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('timezone'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('timezone') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-custom-js">{{ __('Custom JS') }}</label>
                <textarea name="custom_js" id="i-custom-js" class="form-control{{ $errors->has('custom_js') ? ' is-invalid' : '' }}">{{ old('custom_js') ?? config('settings.custom_js') }}</textarea>
                @if ($errors->has('custom_js'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('custom_js') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>