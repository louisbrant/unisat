<div class="d-flex align-items-center justify-content-center text-muted small my-4">
    <div class="width-1 height-1 bg-secondary rounded-circle opacity-25" data-tooltip="true" title="{{ $message->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }} {{ $message->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('H:i:s')) }}"></div>
</div>

<div class="d-flex my-4">
    <div class="flex-shrink-0">
        <img src="{{ gravatar($message->role == 'user' ? $message->user->email : config('settings.ai_assistant_email'), 64) }}" class="rounded-circle width-8 height-8 my-1 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}" data-tooltip="true" title="{{ $message->role == 'user' ? $message->user->name : config('settings.ai_assistant_name') }}">
    </div>

    <div class="flex-grow-1">
        <div class="{{ $message->role == 'user' ? 'bg-base-1' : 'bg-base-2' }} px-3 rounded-lg d-flex">
            <div class="flex-grow-1 py-2 mb-n3">
                {!! str_replace('<pre>', '<pre class="bg-dark text-light p-3 rounded white-space-pre-wrap">', \GrahamCampbell\Markdown\Facades\Markdown::convert($message->result)->getContent()) !!}
            </div>
        </div>
    </div>
</div>
