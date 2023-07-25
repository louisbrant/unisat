@section('site_title', formatTitle([__('Webhooks'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Webhooks') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Webhooks') }}</div></div>
    <div class="card-body">
        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-user-tab" data-toggle="pill" href="#pills-user" role="tab" aria-controls="pills-user" aria-selected="true">{{ __('User') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('admin.settings', 'webhooks') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-user" role="tabpanel" aria-labelledby="pills-user-tab">
                    <div class="form-group">
                        <label for="i-webhook-user-created" class="d-inline-flex align-items-center"><span class="badge badge-warning">{{ __('Store') }}</span></label>
                        <input type="text" dir="ltr" name="webhook_user_created" id="i-webhook-user-created" class="form-control{{ $errors->has('webhook_user_created') ? ' is-invalid' : '' }}" value="{{ old('webhook_user_created') ?? config('settings.webhook_user_created') }}" placeholder="https://example.com">
                        @if ($errors->has('webhook_user_created'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('webhook_user_created') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{!! __(':fields fields are being sent when a user is created.', ['fields' => '<code class="badge badge-secondary">' . implode('</code>, <code class="badge badge-secondary">', ['id', 'name', 'email', 'action']) . '</code>']) !!}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-webhook-user-updated" class="d-inline-flex align-items-center"><span class="badge badge-info">{{ __('Update') }}</span></label>
                        <input type="text" dir="ltr" name="webhook_user_updated" id="i-webhook-user-updated" class="form-control{{ $errors->has('webhook_user_updated') ? ' is-invalid' : '' }}" value="{{ old('webhook_user_updated') ?? config('settings.webhook_user_updated') }}" placeholder="https://example.com">
                        @if ($errors->has('webhook_user_updated'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('webhook_user_updated') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{!! __(':fields fields are being sent when a user is updated.', ['fields' => '<code class="badge badge-secondary">' . implode('</code>, <code class="badge badge-secondary">', ['id', 'name', 'email', 'action']) . '</code>']) !!}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-webhook-user-deleted" class="d-inline-flex align-items-center"><span class="badge badge-danger">{{ __('Delete') }}</span></label>
                        <input type="text" dir="ltr" name="webhook_user_deleted" id="i-webhook-user-deleted" class="form-control{{ $errors->has('webhook_user_deleted') ? ' is-invalid' : '' }}" value="{{ old('webhook_user_deleted') ?? config('settings.webhook_user_deleted') }}" placeholder="https://example.com">
                        @if ($errors->has('webhook_user_deleted'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('webhook_user_deleted') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{!! __(':fields fields are being sent when a user is deleted.', ['fields' => '<code class="badge badge-secondary">' . implode('</code>, <code class="badge badge-secondary">', ['id', 'name', 'email', 'action']) . '</code>']) !!}</small>
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>