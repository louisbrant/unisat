@section('site_title', formatTitle([__('Edit'), __('Template'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => request()->is('admin/*') ? route('admin.dashboard') : route('dashboard'), 'title' => request()->is('admin/*') ? __('Admin') : __('Home')],
    ['url' => request()->is('admin/*') ? route('admin.templates') : route('templates'), 'title' => __('Templates')],
    ['title' => __('Edit')],
]])

<div class="d-flex">
    <h1 class="h2 mb-3 text-break">{{ __('Edit') }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Template') }}</div>
            </div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        @include('templates.partials.menu')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ request()->is('admin/*') ? route('admin.templates.edit', $template->id) : route('templates.edit', $template->id) }}" method="post" enctype="multipart/form-data">
            @csrf

            @if(request()->is('admin/*'))
                <input type="hidden" name="user_id" value="{{ $template->user->id ?? 0 }}">
            @endif

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i-name" value="{{ old('name') ?? $template->name }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The name of the template.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-icon" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Icon') }}</span><span class="badge badge-secondary">{{ __('Emoji') }}</span></label>
                <input type="text" name="icon" class="form-control{{ $errors->has('icon') ? ' is-invalid' : '' }}" id="i-icon" value="{{ old('icon') ?? $template->icon }}">
                @if ($errors->has('icon'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('icon') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The icon of the template.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-description">{{ __('Description') }}</label>
                <textarea rows="3" data-auto-resize-textarea="true" name="description" id="i-description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('The description of my custom translator template') }}">{{ old('description') ?? $template->description }}</textarea>
                @if ($errors->has('description'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The description of the template.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-prompt">{{ __('Prompt') }}</label>
                <textarea rows="3" data-auto-resize-textarea="true" name="prompt" id="i-prompt" class="form-control{{ $errors->has('prompt') ? ' is-invalid' : '' }}" placeholder="{{ __('Translate from [language] to [language] the following text:
[text]') }}">{{ old('prompt') ?? $template->prompt }}</textarea>
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

@if(request()->is('admin/*'))
    <div class="mb-3">
        @include('admin.users.partials.card', ['user' => $template->user])
    </div>

    <div class="row m-n2">
        @php
            $menu = [
                ['icon' => 'icons.document', 'route' => 'admin.documents', 'title' => __('Documents'), 'stats' => 'documents']
            ];
        @endphp

        @foreach($menu as $link)
            <div class="col-12 col-md-6 col-lg-4 p-2">
                <a href="{{ route($link['route'], ['template_id' => $template->id]) }}" class="text-decoration-none text-secondary">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body d-flex align-items-center">
                            @include($link['icon'], ['class' => 'fill-current width-4 height-4 ' . (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3 ')])
                            <div class="text-truncate">{{ $link['title'] }}</div>
                            @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current mx-2'])
                            <div class="{{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }} badge badge-primary">{{ number_format($stats[$link['stats']], 0, __('.'), __(',')) }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
@endif
