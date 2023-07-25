<div class="text-center mb-3 mt-5 pb-3">
    <div class="btn-group btn-group-toggle" data-toggle="buttons">
        <label class="btn btn-outline-dark active" id="plan-month">
            <input type="radio" name="options" autocomplete="off" checked>{{ __('Monthly') }}
        </label>
        <label class="btn btn-outline-dark" id="plan-year">
            <input type="radio" name="options" autocomplete="off">{{ __('Yearly') }}
        </label>
    </div>
</div>

<div class="row flex-column-reverse flex-md-row justify-content-center m-n2 m-md-n3">
    @foreach($plans as $plan)
        <div class="col-12 col-md-6 col-xl-4 p-2 p-md-3">
            <div class="card border-0 shadow-sm rounded h-100 overflow-hidden plan">
                <div class="card-body p-4 d-flex flex-column">
                    <div class="mb-3">
                        <div class="badge badge-pill badge-primary text-uppercase px-2 py-1">{{ $plan->name }}</div>
                    </div>

                    <div class="mb-4">
                        @if(!$plan->isDefault())
                            <div class="plan-preload plan-month d-none d-block">
                                <div>
                                    <span class="h1 mb-0 font-weight-bold">
                                        {{ formatMoney($plan->amount_month, $plan->currency) }}
                                    </span>
                                    <span class="h5 font-weight-bold text-muted">
                                        {{ $plan->currency }}
                                    </span>
                                </div>
                                <span class="text-muted text-lowercase">{{ __('Month') }}</span>
                            </div>

                            <div class="plan-year d-none">
                                <div>
                                    <span class="h1 mb-0 font-weight-bold">
                                        {{ formatMoney($plan->amount_year, $plan->currency) }}
                                    </span>
                                    <span class="h5 font-weight-bold text-muted">
                                        {{ $plan->currency }}
                                    </span>
                                </div>

                                <span class="text-muted text-lowercase">{{ __('Year') }}</span>

                                @if(($plan->amount_month * 12) > $plan->amount_year)
                                    <span class="badge badge-success">
                                        {{ __(':value% off', ['value' => number_format(((($plan->amount_month*12) - $plan->amount_year)/($plan->amount_month * 12) * 100), 0)]) }}
                                    </span>
                                @endif
                            </div>
                        @else
                            <div class="plan-preload plan-month d-none d-block">
                                <div class="h1 mb-0">
                                    <span class="font-weight-bold text-uppercase">
                                        {{ __('Free') }}
                                    </span>
                                </div>
                            </div>

                            <div class="plan-year d-none">
                                <div class="h1 mb-0">
                                    <span class="font-weight-bold text-uppercase">
                                        {{ __('Free') }}
                                    </span>
                                </div>
                            </div>

                            <div class="plan-month d-none d-block">
                                <span class="text-muted text-lowercase">{{ __('Month') }}</span>
                            </div>

                            <div class="plan-year d-none">
                                <span class="text-muted text-lowercase">{{ __('Year') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="row m-n2">
                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->words != 0)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->words == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                @if($plan->features->words < 0)
                                    {{ __('Unlimited words') }}
                                @else($plan->features->words)
                                    {{ __(($plan->features->words == 1 ? ':number word' : ':number words'), ['number' => number_format($plan->features->words, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                                @endif
                            </div>

                            <div class="d-flex align-content-center {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Some language systems will use the following symbol to word ratios: :ratios.', ['ratios' => implode(', ', array_map(function($ratio) { return __(':ratio :scripts', ['ratio' => $ratio['value'], 'scripts' => '(' . implode(', ', array_map(function ($script) { return __($script); }, $ratio['scripts'])) . ')']); }, config('completions.ratios')))]) }}">@include('icons.info', ['class' => 'text-muted width-4 height-4 fill-current'])</div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->documents != 0)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->documents == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                @if($plan->features->documents < 0)
                                    {{ __('Unlimited documents') }}
                                @else($plan->features->documents)
                                    {{ __(($plan->features->documents == 1 ? ':number document' : ':number documents'), ['number' => number_format($plan->features->documents, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->images != 0)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->images == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                @if($plan->features->images < 0)
                                    {{ __('Unlimited images') }}
                                @else($plan->features->images)
                                    {{ __(($plan->features->images == 1 ? ':number image' : ':number images'), ['number' => number_format($plan->features->images, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->chats != 0)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->chats == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                @if($plan->features->chats < 0)
                                    {{ __('Unlimited chats') }}
                                @else($plan->features->chats)
                                    {{ __(($plan->features->chats == 1 ? ':number chat' : ':number chats'), ['number' => number_format($plan->features->chats, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->transcriptions != 0)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->transcriptions == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                @if($plan->features->transcriptions < 0)
                                    {{ __('Unlimited transcriptions') }}
                                @else($plan->features->transcriptions)
                                    {{ __(($plan->features->transcriptions == 1 ? ':number transcription' : ':number transcriptions'), ['number' => number_format($plan->features->transcriptions, 0, __('.'), __(','))]) }} <span class="text-muted">/ {{ mb_strtolower(__('Month')) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->templates)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->templates == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                {{ __('Templates') }}
                            </div>

                            <div class="d-flex align-content-center {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Access to :list templates.', ['list' => implode(', ', array_map('__', $templates->pluck('name')->toArray()))]) }}">@include('icons.info', ['class' => 'text-muted width-4 height-4 fill-current'])</div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->custom_templates)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->custom_templates == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                {{ __('Custom templates') }}
                            </div>

                            <div class="d-flex align-content-center {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Access to :list custom templates.', ['list' => implode(', ', array_map('__', $customTemplates->pluck('name')->toArray()))]) }}">@include('icons.info', ['class' => 'text-muted width-4 height-4 fill-current'])</div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->data_export)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->data_export == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                {{ __('Data export') }}
                            </div>
                        </div>

                        <div class="col-12 p-2 d-flex align-items-center">
                            @if($plan->features->api)
                                @include('icons.checkmark', ['class' => 'flex-shrink-0 text-success fill-current width-4 height-4'])
                            @else
                                @include('icons.close', ['class' => 'flex-shrink-0 text-muted fill-current width-4 height-4'])
                            @endif

                            <div class="{{ ($plan->features->api == 0 ? 'text-muted' : '') }} {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-3') }}">
                                {{ __('API') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer border-0 pt-0 pr-4 pb-4 pl-4 d-flex">
                    <div class="z-1 w-100">
                        @auth
                            @if(!$plan->isDefault())
                                @if(Auth::user()->plan->id == $plan->id)
                                    <div class="btn btn-primary btn-block text-uppercase py-2 disabled">{{ __('Active') }}</div>
                                @else
                                    <div class="plan-no-animation plan-month d-none d-block">
                                        <a href="{{ route('checkout.index', ['id' => $plan->id, 'interval' => 'month']) }}" class="btn btn-primary btn-block text-uppercase py-2">
                                            @if($plan->trial_days > 0 && ! Auth::user()->plan_trial_ends_at)
                                                {{ __('Free trial') }}
                                            @else
                                                {{ __('Subscribe') }}
                                            @endif
                                        </a>
                                    </div>
                                    <div class="plan-no-animation plan-year d-none">
                                        <a href="{{ route('checkout.index', ['id' => $plan->id, 'interval' => 'year']) }}" class="btn btn-primary btn-block text-uppercase py-2">
                                            @if($plan->trial_days > 0 && ! Auth::user()->plan_trial_ends_at)
                                                {{ __('Free trial') }}
                                            @else
                                                {{ __('Subscribe') }}
                                            @endif
                                        </a>
                                    </div>
                                @endif
                            @else
                                <div class="btn btn-primary btn-block text-uppercase py-2 disabled">{{ __('Free') }}</div>
                            @endif
                        @else
                            @if(config('settings.registration'))
                                <div class="plan-no-animation plan-month d-none d-block">
                                    <a href="{{ route('register', ['plan' => $plan->id, 'interval' => 'month']) }}" class="btn btn-primary btn-block text-uppercase py-2">{{ __('Register') }}</a>
                                </div>
                                <div class="plan-no-animation plan-year d-none">
                                    <a href="{{ route('register', ['plan' => $plan->id, 'interval' => 'year']) }}" class="btn btn-primary btn-block text-uppercase py-2">{{ __('Register') }}</a>
                                </div>
                            @else
                                <div class="plan-no-animation plan-month d-none d-block">
                                    <a href="{{ route('login', ['plan' => $plan->id, 'interval' => 'month']) }}" class="btn btn-primary btn-block text-uppercase py-2">{{ __('Login') }}</a>
                                </div>
                                <div class="plan-no-animation plan-year d-none">
                                    <a href="{{ route('login', ['plan' => $plan->id, 'interval' => 'year']) }}" class="btn btn-primary btn-block text-uppercase py-2">{{ __('Login') }}</a>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
