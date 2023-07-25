@section('site_title', formatTitle([__('Announcements'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Announcements') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Announcements') }}</div></div>
    <div class="card-body">

        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-guest-tab" data-toggle="pill" href="#pills-guest" role="tab" aria-controls="pills-guest" aria-selected="true">{{ __('Guest') }}</a>
            </li>
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-user-tab" data-toggle="pill" href="#pills-user" role="tab" aria-controls="pills-user" aria-selected="false">{{ __('User') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('admin.settings', 'announcements') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-guest" role="tabpanel" aria-labelledby="pills-guest-tab">
                    <div class="form-group">
                        <label for="i-announcement-guest">{{ __('Content') }}</label>
                        <textarea name="announcement_guest" id="i-announcement-guest" class="form-control{{ $errors->has('announcement_guest') ? ' is-invalid' : '' }}">{{ old('announcement_guest') ?? config('settings.announcement_guest') }}</textarea>
                        @if ($errors->has('announcement_guest'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('announcement_guest') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-announcement-guest-type">{{ __('Type') }}</label>
                        <select name="announcement_guest_type" id="i-announcement-guest-type" class="custom-select{{ $errors->has('announcement_guest_type') ? ' is-invalid' : '' }}">
                            @foreach(['primary' => __('Primary'), 'secondary' => __('Secondary'), 'success' => __('Success'), 'danger' => __('Danger'), 'warning' => __('Warning'), 'info' => __('Info'), 'secondary' => __('Secondary'), 'light' => __('Light'), 'dark' => __('Dark')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('announcement_guest_type') !== null && old('announcement_guest_type') == $key) || (config('settings.announcement_guest_type') == $key && old('announcement_guest_type') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('announcement_guest_type'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('announcement_guest_type') }}</strong>
                            </span>
                        @endif
                    </div>

                    <input type="hidden" name="announcement_guest_id" id="i-announcement-guest-id" class="form-control form-control-sm{{ $errors->has('announcement_guest_id') ? ' is-invalid' : '' }}" value="{{ old('announcement_guest_id') ?? Str::random(16) }}">
                </div>

                <div class="tab-pane fade" id="pills-user" role="tabpanel" aria-labelledby="pills-user-tab">
                    <div class="form-group">
                        <label for="i-announcement-user">{{ __('Content') }}</label>
                        <textarea name="announcement_user" id="i-announcement-user" class="form-control{{ $errors->has('announcement_user') ? ' is-invalid' : '' }}">{{ old('announcement_user') ?? config('settings.announcement_user') }}</textarea>
                        @if ($errors->has('announcement_user'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('announcement_user') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-announcement-user-type">{{ __('Type') }}</label>
                        <select name="announcement_user_type" id="i-announcement-user-type" class="custom-select{{ $errors->has('announcement_user_type') ? ' is-invalid' : '' }}">
                            @foreach(['primary' => __('Primary'), 'secondary' => __('Secondary'), 'success' => __('Success'), 'danger' => __('Danger'), 'warning' => __('Warning'), 'info' => __('Info'), 'secondary' => __('Secondary'), 'light' => __('Light'), 'dark' => __('Dark')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('announcement_user_type') !== null && old('announcement_user_type') == $key) || (config('settings.announcement_user_type') == $key && old('announcement_user_type') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('announcement_user_type'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('announcement_user_type') }}</strong>
                            </span>
                        @endif
                    </div>

                    <input type="hidden" name="announcement_user_id" id="i-announcement-user-id" class="form-control form-control-sm{{ $errors->has('announcement_user_id') ? ' is-invalid' : '' }}" value="{{ old('announcement_user_id') ?? Str::random(16) }}">
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>