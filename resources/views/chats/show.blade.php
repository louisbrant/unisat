@section('site_title', formatTitle([e($chat->name), __('Chat'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => request()->is('admin/*') ? route('admin.dashboard') : route('dashboard'), 'title' => request()->is('admin/*') ? __('Admin') : __('Home')],
    ['url' => request()->is('admin/*') ? route('admin.chats') : route('chats'), 'title' => __('Chats')],
    ['title' => __('Chat')],
]])

<div class="d-flex align-items-end mb-3">
    <h1 class="h2 mb-0 flex-grow-1 text-truncate">{{ $chat->name }}</h1>

    <div class="d-flex align-items-center flex-grow-0">
        <div class="form-row flex-nowrap">
            <div class="col">
                <a href="#" class="btn text-secondary d-flex align-items-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</a>

                @include('chats.partials.menu')
            </div>
        </div>
    </div>
</div>

<div class="card border-0 rounded-top shadow-sm overflow-hidden d-flex flex-fill h-100 max-height-152">
    <div class="card-header">
        <div class="row flex-fill">
            <div class="col d-flex align-items-center">
                <div class="d-flex align-items-center font-weight-medium py-1">
                    <div class="d-flex align-items-center text-truncate">
                        {{ __('Chat') }}

                        @if($chat->favorite) <div class="d-flex flex-shrink-0 width-4 height-4 text-warning {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Favorite') }}">@include('icons.star', ['class' => 'fill-current width-4 height-4 flex-shrink-0'])</div> @endif
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <form method="GET" action="{{ route('chats.show', ['id' => $chat->id]) }}">
                    <div class="input-group input-group-sm">
                        <input class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ app('request')->input('search') }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-tooltip="true" title="{{ __('Filters') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.filter', ['class' => 'fill-current width-4 height-4'])
                                &#8203;
                            </button>
                            <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow width-64 p-0" id="search-filters">
                                <div class="dropdown-header py-3">
                                    <div class="row">
                                        <div class="col">
                                            <div class="font-weight-medium m-0 text-body">{{ __('Filters') }}</div>
                                        </div>
                                        <div class="col-auto">
                                            <a href="{{ route('chats.show', ['id' => $chat->id]) }}" class="text-secondary">{{ __('Reset') }}</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="dropdown-divider my-0"></div>

                                <div class="max-height-96 overflow-auto pt-3">
                                    <div class="form-group px-4">
                                        <label for="i-search-by" class="small">{{ __('Search by') }}</label>
                                        <select name="search_by" id="i-search-by" class="custom-select custom-select-sm">
                                            @foreach(['result' => __('Result')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('search_by') == $key) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-role" class="small">{{ __('Role') }}</label>
                                        <select name="role" id="i-role" class="custom-select custom-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach(['user' => __('User'), 'assistant' => __('Assistant')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('user') == $key && request()->input('user') !== null) selected @endif>{{ __($value) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-sort-by" class="small">{{ __('Sort by') }}</label>
                                        <select name="sort_by" id="i-sort-by" class="custom-select custom-select-sm">
                                            @foreach(['id' => __('Date created')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('sort_by') == $key) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-sort" class="small">{{ __('Sort') }}</label>
                                        <select name="sort" id="i-sort" class="custom-select custom-select-sm">
                                            @foreach(['desc' => __('Descending'), 'asc' => __('Ascending')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('sort') == $key) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-per-page" class="small">{{ __('Results per page') }}</label>
                                        <select name="per_page" id="i-per-page" class="custom-select custom-select-sm">
                                            @foreach([10, 25, 50, 100] as $value)
                                                <option value="{{ $value }}" @if(request()->input('per_page') == $value || request()->input('per_page') == null && $value == config('settings.paginate')) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="dropdown-divider my-0"></div>

                                <div class="px-4 py-3">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('messages.new') }}" method="post" class="d-flex flex-fill flex-column position-relative flex-grow-1 overflow-auto" id="form-chat">

        @csrf

        <input name="chat_id" type="hidden" value="{{ $chat->id }}">

        <div class="card-body flex-grow-1 overflow-auto" id="chat-container">
            {{ $messages->links('chats.partials.pagination', ['paginator' => $messages, 'previous' => true]) }}

            <div id="chat-messages">
                @foreach($messages->reverse() as $message)
                    @include('chats.partials.message')
                @endforeach
            </div>

            {{ $messages->links('chats.partials.pagination', ['paginator' => $messages, 'next' => true]) }}
        </div>

        @if ($chat->user_id == Auth::user()->id)
            <div class="card-footer bg-base-0">
                <div class="form-row">
                    <div class="col">
                        <input name="message" id="i-message" type="text" class="form-control" autocomplete="off">
                        <span class="invalid-feedback" role="alert">
                            <strong></strong>
                        </span>
                    </div>

                    <div class="col-auto">
                        <button type="submit" name="submit" class="btn btn-primary position-relative"
                                data-button-loader>
                            <div class="position-absolute top-0 right-0 bottom-0 left-0 d-flex align-items-center justify-content-center">
                                <span class="d-none spinner-border spinner-border-sm width-4 height-4" role="status"></span>
                            </div>
                            <span class="spinner-text">{{ __('Send') }}</span>&#8203;
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </form>
</div>
