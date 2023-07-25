@section('site_title', formatTitle([__('Appearance'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Appearance') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Appearance') }}</div></div>
    <div class="card-body">

        @include('shared.message')

        <form action="{{ route('admin.settings', 'appearance') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="row mx-n2">
                <div class="col-12 col-md-6 px-2">
                    <div class="form-group">
                        <label for="i-logo" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Logo') }}</span><span class="badge badge-secondary">{{ __('Light') }}</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-1 px-2"><img src="{{ asset('uploads/brand/' . config('settings.logo')) }}" style="max-height: 1.625rem"></span>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="logo" id="i-logo" class="custom-file-input{{ $errors->has('logo') ? ' is-invalid' : '' }}" accept="jpeg,png,bmp,gif,svg,webp">
                                <label class="custom-file-label" for="i-logo" data-browse="{{ __('Browse') }}">{{ __('Choose file') }}</label>
                            </div>
                        </div>
                        @if ($errors->has('logo'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('logo') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col-12 col-md-6 px-2">
                    <div class="form-group">
                        <label for="i-logo-dark" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Logo') }}</span><span class="badge badge-secondary">{{ __('Dark') }}</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text py-1 px-2"><img src="{{ asset('uploads/brand/' . config('settings.logo_dark')) }}" style="max-height: 1.625rem"></span>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="logo_dark" id="i-logo-dark" class="custom-file-input{{ $errors->has('logo_dark') ? ' is-invalid' : '' }}" accept="jpeg,png,bmp,gif,svg,webp">
                                <label class="custom-file-label" for="i-logo-dark" data-browse="{{ __('Browse') }}">{{ __('Choose file') }}</label>
                            </div>
                        </div>
                        @if ($errors->has('logo_dark'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('logo_dark') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="i-favicon">{{ __('Favicon') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text py-1 px-2"><img src="{{ asset('uploads/brand/' . config('settings.favicon')) }}" style="max-height: 1.625rem;"></span>
                    </div>
                    <div class="custom-file">
                        <input type="file" name="favicon" id="i-favicon" class="custom-file-input{{ $errors->has('favicon') ? ' is-invalid' : '' }}" accept="jpeg,png,bmp,gif,svg,webp">
                        <label class="custom-file-label" for="i-favicon" data-browse="{{ __('Browse') }}">{{ __('Choose file') }}</label>
                    </div>
                </div>
                @if ($errors->has('favicon'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('favicon') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-theme" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Theme') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                <select name="theme" id="i-theme" class="custom-select{{ $errors->has('theme') ? ' is-invalid' : '' }}">
                    @foreach([0 => __('Light'), 1 => __('Dark')] as $key => $value)
                        <option value="{{ $key }}" @if ((old('theme') !== null && old('theme') == $key) || (config('settings.theme') == $key && old('theme') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('theme'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('theme') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-custom-css">{{ __('Custom CSS') }}</label>
                <textarea name="custom_css" id="i-custom-css" class="form-control{{ $errors->has('custom_css') ? ' is-invalid' : '' }}">{{ old('custom_css') ?? config('settings.custom_css') }}</textarea>
                @if ($errors->has('custom_css'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('custom_css') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>
