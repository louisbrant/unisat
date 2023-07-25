@section('site_title', formatTitle([__('Cron job'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Cron job') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1">{{ __('Cron job') }}</div></div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <div class="form-group">
            <label for="i-cronjob">{{ __('Command') }}</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <code class="input-group-text">* * * * *</code>
                </div>
                <input type="text" dir="ltr" name="cronjob" id="i-cronjob" class="form-control" value="wget -q -O /dev/null {{ route('cronjob', ['key' => config('settings.cronjob_key')]) }}" readonly>
                <div class="input-group-append">
                    <button type="button" class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-cronjob">{{ __('Copy') }}</button>
                </div>
            </div>
            <small class="form-text text-muted">
                {{ __('The cron job command must be set to run every minute.') }} @if(config('settings.cronjob_executed_at')) {{ __('Last executed at: :date.', ['date' => Carbon\Carbon::createFromTimestamp(config('settings.cronjob_executed_at'))->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d') . ' H:i:s')]) }} @endif
            </small>
        </div>

        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modal" data-button-name="cronjob_key" data-action="{{ route('admin.settings', 'cronjob') }}" data-button="btn btn-danger" data-title="{{ __('Regenerate') }}" data-text="{{ __('If you regenerate the cron job key, you will need to update the cron job task with the new command.') }}">{{ __('Regenerate') }}</button>
    </div>
</div>