@section('site_title', formatTitle([__('Edit'), __('Chat'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => request()->is('admin/*') ? route('admin.dashboard') : route('dashboard'), 'title' => request()->is('admin/*') ? __('Admin') : __('Home')],
    ['url' => request()->is('admin/*') ? route('admin.chats') : route('chats'), 'title' => __('Chats')],
    ['title' => __('Edit')],
]])

<div class="d-flex">
    <h1 class="h2 mb-3 text-break">{{ __('Edit') }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Chat') }}</div>
            </div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        @include('chats.partials.menu')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ request()->is('admin/*') ? route('admin.chats.edit', $chat->id) : route('chats.edit', $chat->id) }}" method="post" enctype="multipart/form-data">
            @csrf

            @include('shared.message')

            @if(request()->is('admin/*'))
                <input type="hidden" name="user_id" value="{{ $chat->user->id }}">
            @endif

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" id="i-name" value="{{ old('name') ?? $chat->name }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-behavior">{{ __('Behavior') }}</label>
                <input type="text" name="behavior" class="form-control{{ $errors->has('behavior') ? ' is-invalid' : '' }}" id="i-behavior" value="{{ old('behavior') ?? $chat->behavior }}">
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

@if(request()->is('admin/*'))
    <div class="mb-3">
        @include('admin.users.partials.card', ['user' => $chat->user])
    </div>
@endif
