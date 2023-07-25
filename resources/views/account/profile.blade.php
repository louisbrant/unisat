@if(request()->is('admin/*'))
    @section('site_title', formatTitle([__('Edit'), __('User'), config('settings.title')]))
@else
    @section('site_title', formatTitle([__('Profile'), config('settings.title')]))
@endif

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => request()->is('admin/*') ? route('admin.dashboard') : route('dashboard'), 'title' => request()->is('admin/*') ? __('Admin') : __('Home')],
    ['url' => request()->is('admin/*') ? route('admin.users') : route('account'), 'title' => request()->is('admin/*') ? __('Users') : __('Account')],
    ['title' => request()->is('admin/*') ? __('Edit') : __('Profile')]
]])

<div class="d-flex">
    <h1 class="h2 mb-3 text-break">{{ (request()->is('admin/*') ? __('Edit') : __('Profile')) }}</h1>
</div>

<div class="card border-0 shadow-sm @if(request()->is('admin/*')) mb-3 @endif">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">
                    @if(request()->is('admin/*'))
                        {{ __('User') }}
                        @if($user->trashed())
                            <span class="badge badge-danger">{{ __('Disabled') }}</span>
                        @elseif(!$user->email_verified_at)
                            <span class="badge badge-secondary">{{ __('Pending') }}</span>
                        @endif
                    @else
                        {{ __('Profile') }}
                    @endif
                </div>
            </div>
            @if(request()->is('admin/*'))
                <div class="col-auto">
                    <div class="form-row">
                        <div class="col">
                            @include('admin.users.partials.menu')
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="card-body">
        @include('shared.message')

        @if($user->getPendingEmail() && request()->is('admin/*') == false)
            <div class="alert alert-info d-flex" role="alert">
                <div>
                    <form class="d-inline" method="POST" action="{{ route('account.profile.resend') }}" id="resend-form">
                        @csrf
                        {{ __(':address email address is pending confirmation', ['address' => $user->getPendingEmail()]) }}. {{ __('Didn\'t receive the email?') }} <a href="#" class="alert-link font-weight-medium" onclick="event.preventDefault(); document.getElementById('resend-form').submit();">{{ __('Resend') }}</a>
                    </form>
                </div>
                <div class="{{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}">
                    <form class="d-inline" method="POST" action="{{ route('account.profile.cancel') }}" id="cancel-form">
                        @csrf
                        <a href="#" class="alert-link font-weight-medium" onclick="event.preventDefault(); document.getElementById('cancel-form').submit();">{{ __('Cancel') }}</a>
                    </form>
                </div>
            </div>
        @endif

        <form action="{{ (request()->is('admin/*') ? route('admin.users.edit', $user->id) : route('account.profile.update')) }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" id="i-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') ?? $user->name }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-email">{{ __('Email') }}</label>
                <input type="text" name="email" id="i-email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') ?? $user->email }}">
                @if ($errors->has('email'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-timezone">{{ __('Timezone') }}</label>
                <select name="timezone" id="i-timezone" class="custom-select{{ $errors->has('timezone') ? ' is-invalid' : '' }}">
                    @foreach(timezone_identifiers_list() as $value)
                        <option value="{{ $value }}" @if ($value == $user->timezone) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('timezone'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('timezone') }}</strong>
                    </span>
                @endif
            </div>

            @if(request()->is('admin/*'))
                <div class="hr-text"><span class="font-weight-medium text-body">{{ __('Status') }}</span></div>

                <div class="form-group">
                    <label for="i-email-verified-at">{{ __('Verified') }}</label>
                    <select name="email_verified_at" id="i-email-verified-at" class="custom-select{{ $errors->has('email_verified_at') ? ' is-invalid' : '' }}">
                        <option value="0" @if (empty($user->email_verified_at)) selected @endif>{{ __('No') }}</option>
                        <option value="1" @if ($user->email_verified_at) selected @endif>{{ __('Yes') }}</option>
                    </select>
                    @if ($errors->has('email_verified_at'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email_verified_at') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="i-role">{{ __('Role') }}</label>
                    <select name="role" id="i-role" class="custom-select{{ $errors->has('role') ? ' is-invalid' : '' }}">
                        @foreach([0 => __('User'), 1 => __('Admin')] as $key => $value)
                            <option value="{{ $key }}" @if ($key == $user->role) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('role'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('role') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="hr-text"><span class="font-weight-medium text-body">{{ __('Password') }}</span></div>

                <div class="form-group">
                    <label for="i-password">{{ __('New password') }} <span class="text-muted">({{ mb_strtolower(__('Leave empty if you don\'t want to change it')) }})</span></label>
                    <input id="i-password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password">
                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="i-password-confirmation">{{ __('Confirm new password') }}</label>
                    <input type="password" name="password_confirmation" id="i-password-confirmation" class="form-control{{ $errors->has('password_confirmation') ? ' is-invalid' : '' }}">
                    @if ($errors->has('password_confirmation'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="hr-text"><span class="font-weight-medium text-body">{{ __('Two-factor authentication') }}</span></div>

                <div class="form-group">
                    <label for="i-tfa">{{ __('Email') }}</label>
                    <select name="tfa" id="i-tfa" class="custom-select{{ $errors->has('tfa') ? ' is-invalid' : '' }}">
                        @foreach([0 => __('Disabled'), 1 => __('Enabled')] as $key => $value)
                            <option value="{{ $key }}" @if ((old('tfa') !== null && old('tfa') == $key) || ($user->tfa == $key && old('tfa') == null)) selected @endif>{{ $value }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('tfa'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('tfa') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="hr-text"><span class="font-weight-medium text-body">{{ __('Plan') }}</span></div>

                <div class="row mx-n2">
                    <div class="col-12 col-lg-4 px-2">
                        <div class="form-group">
                            <label for="i-plan-id">{{ __('Name') }}</label>
                            <select id="i-plan-id" name="plan_id" class="custom-select{{ $errors->has('plan_id') ? ' is-invalid' : '' }}">
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" @if($user->plan_id == $plan->id) selected @endif>{{ $plan->name }}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('plan_id'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('plan_id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 px-2">
                        <div class="form-group">
                            <label for="i-plan-ends-at">{{ __('Ends at') }}</label>
                            <input type="date" name="plan_ends_at" class="form-control{{ $errors->has('plan_ends_at') ? ' is-invalid' : '' }}" id="i-plan-ends-at" placeholder="YYYY-MM-DD" value="{{ old('plan_ends_at') ?? ($user->plan_ends_at ? $user->plan_ends_at->tz($user->timezone ?? config('app.timezone'))->format('Y-m-d') : '') }}">
                            @if ($errors->has('plan_ends_at'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('plan_ends_at') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 px-2">
                        <div class="form-group">
                            <label for="i-plan-payment-processor">{{ __('Processor') }}</label>
                            <input type="text" class="form-control" id="i-plan-payment-processor" value="{{ config('payment.processors.' . $user->plan_payment_processor)['name'] ?? __('None') }}" readonly>
                        </div>
                    </div>
                </div>
            @endif

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>

@if(request()->is('admin/*'))
    <div class="row m-n2">
        @php
            $menu = [
                ['icon' => 'icons.credit-card', 'route' => 'admin.payments', 'title' => __('Payments'), 'stats' => 'payments'],
                ['icon' => 'icons.apps', 'route' => 'admin.templates', 'title' => __('Templates'), 'stats' => 'templates'],
                ['icon' => 'icons.article', 'route' => 'admin.documents', 'title' => __('Documents'), 'stats' => 'documents'],
                ['icon' => 'icons.image', 'route' => 'admin.images', 'title' => __('Images'), 'stats' => 'images'],
                ['icon' => 'icons.chat', 'route' => 'admin.chats', 'title' => __('Chats'), 'stats' => 'chats'],
                ['icon' => 'icons.headphones', 'route' => 'admin.transcriptions', 'title' => __('Transcriptions'), 'stats' => 'transcriptions']
            ];
        @endphp

        @foreach($menu as $link)
            <div class="col-12 col-md-6 col-lg-4 p-2">
                <a href="{{ route($link['route'], ['user_id' => $user->id]) }}" class="text-decoration-none text-secondary">
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
