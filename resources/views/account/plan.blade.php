@section('site_title', formatTitle([__('Plan'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('account'), 'title' => __('Account')],
    ['title' => __('Plan')]
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Plan') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1">{{ __('Plan') }}</div></div>
            @if(paymentProcessors())
                <div class="col-auto">
                    @if(Auth::user()->planIsDefault())
                        <a href="{{ route('pricing') }}" class="btn btn-sm btn-outline-primary btn-block d-flex justify-content-center align-items-center">@include('icons.unarchive', ['class' => 'width-4 height-4 fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')]){{ __('Upgrade') }}</a>
                    @else
                        <a href="{{ route('pricing') }}" class="btn btn-sm btn-outline-primary btn-block d-flex justify-content-center align-items-center">@include('icons.package', ['class' => 'width-4 height-4 fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')]){{ __('Plans') }}</a>
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('account.plan') }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted">{{ __('Plan') }}</div>
                    <div>{{ $user->plan->name }}</div>
                </div>

                @if(!$user->planIsDefault())
                    @if($user->plan_payment_processor)
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="text-muted">{{ __('Processor') }}</div>
                            <div>{{ config('payment.processors.' . $user->plan_payment_processor)['name'] }}</div>
                        </div>
                    @endif

                    @if($user->plan_amount && $user->plan_currency && $user->plan_interval)
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="text-muted">{{ __('Amount') }}</div>
                            <div>{{ formatMoney($user->plan_amount, $user->plan_currency) }} {{ $user->plan_currency }} / <span class="text-lowercase">{{ $user->plan_interval == 'month' ? __('Month') : __('Year') }}</span></div>
                        </div>
                    @endif

                    @if($user->plan_recurring_at)
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="text-muted">{{ __('Recurring at') }}</div>
                            <div>{{ $user->plan_recurring_at->tz($user->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }}</div>
                        </div>
                    @endif

                    @if($user->plan_trial_ends_at && $user->plan_trial_ends_at->gt(Carbon\Carbon::now()))
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="text-muted">{{ __('Trial ends at') }}</div>
                            <div>{{ $user->plan_trial_ends_at->tz($user->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }}</div>
                        </div>
                    @endif

                    @if($user->plan_ends_at)
                        <div class="col-12 col-lg-6 mb-3">
                            <div class="text-muted">{{ __('Ends at') }}</div>
                            <div>{{ $user->plan_ends_at->tz($user->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }}</div>
                        </div>
                    @endif
                @endif
            </div>

            <div class="hr-text mt-0"><span class="font-weight-medium text-body">{{ __('Features') }}</span></div>

            <div class="row m-n2">
                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->words != 0)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->words == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        @if($user->plan->features->words < 0)
                            {{ __('Unlimited words') }}
                        @elseif($user->plan->features->words)
                            <span class="text-muted">{{ Auth::user()->words_month_count }} /</span> {{ __(($user->plan->features->words == 1 ? ':number word' : ':number words'), ['number' => number_format($user->plan->features->words, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                        @endif
                    </div>

                    <div class="d-flex align-content-center {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Some language systems will use the following symbol to word ratios: :ratios.', ['ratios' => implode(', ', array_map(function($ratio) { return __(':ratio :scripts', ['ratio' => $ratio['value'], 'scripts' => '(' . implode(', ', array_map(function ($script) { return __($script); }, $ratio['scripts'])) . ')']); }, config('completions.ratios')))]) }}">@include('icons.info', ['class' => 'text-muted width-4 height-4 fill-current'])</div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->documents != 0)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->documents == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        @if($user->plan->features->documents < 0)
                            {{ __('Unlimited documents') }}
                        @elseif($user->plan->features->documents)
                            <span class="text-muted">{{ Auth::user()->documents_month_count }} /</span> {{ __(($user->plan->features->documents == 1 ? ':number document' : ':number documents'), ['number' => number_format($user->plan->features->documents, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->images != 0)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->images == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        @if($user->plan->features->images < 0)
                            {{ __('Unlimited images') }}
                        @elseif($user->plan->features->images)
                            <span class="text-muted">{{ Auth::user()->images_month_count }} /</span> {{ __(($user->plan->features->images == 1 ? ':number image' : ':number images'), ['number' => number_format($user->plan->features->images, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->chats != 0)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->chats == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        @if($user->plan->features->chats < 0)
                            {{ __('Unlimited chats') }}
                        @elseif($user->plan->features->chats)
                            <span class="text-muted">{{ Auth::user()->chats_month_count }} /</span> {{ __(($user->plan->features->chats == 1 ? ':number chat' : ':number chats'), ['number' => number_format($user->plan->features->chats, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->transcriptions != 0)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->transcriptions == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        @if($user->plan->features->transcriptions < 0)
                            {{ __('Unlimited transcriptions') }}
                        @elseif($user->plan->features->transcriptions)
                            <span class="text-muted">{{ Auth::user()->transcriptions_month_count }} /</span> {{ __(($user->plan->features->transcriptions == 1 ? ':number transcription' : ':number transcriptions'), ['number' => number_format($user->plan->features->transcriptions, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->templates)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->templates == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        {{ __('Templates') }}
                    </div>

                    <div class="d-flex align-content-center {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Access to :list templates.', ['list' => implode(', ', array_map('__', $templates->pluck('name')->toArray()))]) }}">@include('icons.info', ['class' => 'text-muted width-4 height-4 fill-current'])</div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->custom_templates)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->custom_templates == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        {{ __('Custom templates') }}
                    </div>

                    <div class="d-flex align-content-center {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Access to :list custom templates.', ['list' => implode(', ', array_map('__', $customTemplates->pluck('name')->toArray()))]) }}">@include('icons.info', ['class' => 'text-muted width-4 height-4 fill-current'])</div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->data_export)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->data_export == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        {{ __('Data export') }}
                    </div>
                </div>

                <div class="col-12 col-md-6 p-2 d-flex align-items-center">
                    @if($user->plan->features->api)
                        @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                    @else
                        @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                    @endif

                    <div class="{{ ($user->plan->features->api == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                        {{ __('API') }}
                    </div>
                </div>
            </div>

            @if(paymentProcessors())
                @if($user->plan_recurring_at)
                    <button type="button" class="btn btn-outline-danger mt-3" data-toggle="modal" data-target="#modal" data-action="{{ route('account.plan') }}" data-button="btn btn-danger" data-title="{{ __('Cancel') }}" data-text="{{ __('You\'ll continue to have access to the features you\'ve paid for until the end of your billing cycle.') }}" data-sub-text="{{ __('Are you sure you want to cancel :name?', ['name' => $user->plan->name]) }}">{{ __('Cancel') }}</button>
                @endif
            @endif
        </form>
    </div>
</div>
