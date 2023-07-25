@section('site_title', formatTitle([__('New'), __('Chat'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('chats'), 'title' => __('Chats')],
    ['title' => __('New')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('New') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Chat') }}</div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('chats.new') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i-name" value="{{ old('name') }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-behavior">{{ __('Behavior') }}</label>
                <input type="text" name="behavior" class="form-control{{ $errors->has('behavior') ? ' is-invalid' : '' }}" id="i-behavior" value="{{ old('behavior') }}" placeholder="{{ __('You are a helpful assistant.') }}">
                @if ($errors->has('behavior'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('behavior') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The behavior of the assistant.') }}</small>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>
