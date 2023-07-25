@section('site_title', formatTitle([__('Edit'), __('Transcription'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => request()->is('admin/*') ? route('admin.dashboard') : route('dashboard'), 'title' => request()->is('admin/*') ? __('Admin') : __('Home')],
    ['url' => request()->is('admin/*') ? route('admin.transcriptions') : route('transcriptions'), 'title' => __('Transcriptions')],
    ['title' => __('Edit')],
]])

<div class="d-flex">
    <h1 class="h2 mb-3 text-break">{{ __('Edit') }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Transcription') }}</div>
            </div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        @include('transcriptions.partials.menu')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ request()->is('admin/*') ? route('admin.transcriptions.edit', $transcription->id) : route('transcriptions.edit', $transcription->id) }}" method="post" enctype="multipart/form-data">
            @csrf

            @include('shared.message')

            @if(request()->is('admin/*'))
                <input type="hidden" name="user_id" value="{{ $transcription->user->id }}">
            @endif

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i-name" value="{{ old('name') ?? $transcription->name }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group d-none">
                <label for="i-result-{{ $transcription->id }}" class="d-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Result') }}</span></label>
                <div class="form-group">
                    <textarea name="result" id="i-result-{{ $transcription->id }}" class="form-control{{ $errors->has('result') ? ' is-invalid' : '' }}" data-auto-resize-textarea="true">{{ old('result') ?? $transcription->result }}</textarea>
                    @if ($errors->has('result'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('result') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="i-result-{{ $transcription->id }}" class="d-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Result') }}</span></label>
                <div class="form-control{{ $errors->has('result') ? ' is-invalid' : '' }} height-auto p-3 text-body">
                    <div class="border-bottom pb-3 mb-3 mx-n3 px-3">
                        @include('shared.editor.toolbar', ['id' => $transcription->id])
                    </div>

                    @include('shared.editor.content', ['id' => $transcription->id, 'text' => $transcription->result])
                </div>
                @if ($errors->has('result'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('result') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>

@if(request()->is('admin/*'))
    <div class="mb-3">
        @include('admin.users.partials.card', ['user' => $transcription->user])
    </div>
@endif
