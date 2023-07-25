@section('site_title', formatTitle([__('New'), __('Template'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('templates'), 'title' => __('Templates')],
    ['title' => __('New')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('New') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Template') }}</div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ request()->is('admin/*') ? route('admin.templates.new') : route('templates.new') }}" method="post" enctype="multipart/form-data">
            @csrf

            @if(request()->is('admin/*'))
                <div class="alert alert-warning">{{ __('This template will be available as a plan feature.') }}</div>
            @endif

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i-name" value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The name of the template.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-icon" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Icon') }}</span><span class="badge badge-secondary">{{ __('Emoji') }}</span></label>
                <input type="text" name="icon" class="form-control{{ $errors->has('icon') ? ' is-invalid' : '' }}" id="i-icon" value="{{ old('icon') ?? '😊' }}">
                @if ($errors->has('icon'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('icon') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The icon of the template.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-description">{{ __('Description') }}</label>
                <textarea rows="3" data-auto-resize-textarea="true" name="description" id="i-description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('The description of my custom translator template') }}">{{ old('description') }}</textarea>
                @if ($errors->has('description'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The description of the template.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-prompt">{{ __('Prompt') }}</label>
                <textarea rows="3" data-auto-resize-textarea="true" name="prompt" id="i-prompt" class="form-control{{ $errors->has('prompt') ? ' is-invalid' : '' }}" placeholder="{{ __('Translate from [language] to [language] the following text:') }}
{{ __('[text]') }}">{{ old('prompt') }}</textarea>
                @if ($errors->has('prompt'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('prompt') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The prompt of the template.') }}</small>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>
