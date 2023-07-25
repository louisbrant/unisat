@section('site_title', formatTitle([__('Payment processors'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Payment processors') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Payment processors') }}</div></div>
    <div class="card-body">
        <ul class="nav nav-pills d-flex flex-fill flex-column flex-lg-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-paypal-tab" data-toggle="pill" href="#pills-paypal" role="tab" aria-controls="pills-paypal" aria-selected="true">{{ __('PayPal') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-stripe-tab" data-toggle="pill" href="#pills-stripe" role="tab" aria-controls="pills-stripe" aria-selected="false">{{ __('Stripe') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-razorpay-tab" data-toggle="pill" href="#pills-razorpay" role="tab" aria-controls="pills-razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-paystack-tab" data-toggle="pill" href="#pills-paystack" role="tab" aria-controls="pills-paystack" aria-selected="false">{{ __('Paystack') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-coinbase-tab" data-toggle="pill" href="#pills-coinbase" role="tab" aria-controls="pills-coinbase" aria-selected="false">{{ __('Coinbase') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-cryptocom-tab" data-toggle="pill" href="#pills-cryptocom" role="tab" aria-controls="pills-cryptocom" aria-selected="false">{{ __('Crypto.com') }}</a>
            </li>

            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-bank-tab" data-toggle="pill" href="#pills-bank" role="tab" aria-controls="pills-bank" aria-selected="false">{{ __('Bank') }}</a>
            </li>
            
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="brc20-tab" data-toggle="pill" href="#pills-brc20" role="tab" aria-controls="pills-brc20" aria-selected="false">{{ __('BRC20') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('admin.settings', 'payment-processors') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-paypal" role="tabpanel" aria-labelledby="pills-paypal-tab">
                    <div class="form-group">
                        <label for="i-paypal">{{ __('Enabled') }}</label>
                        <select name="paypal" id="i-paypal" class="custom-select{{ $errors->has('paypal') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('paypal') !== null && old('paypal') == $key) || (config('settings.paypal') == $key && old('paypal') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('paypal'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paypal') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paypal-mode">{{ __('Mode') }}</label>
                        <select name="paypal_mode" id="i-paypal-mode" class="custom-select{{ $errors->has('paypal_mode') ? ' is-invalid' : '' }}">
                            @foreach(['live' => __('Live'), 'sandbox' => __('Sandbox')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('paypal_mode') !== null && old('paypal_mode') == $key) || (config('settings.paypal_mode') == $key && old('paypal_mode') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('paypal_mode'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paypal_mode') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paypal-client-id">{{ __('Client ID') }}</label>
                        <input type="text" name="paypal_client_id" id="i-paypal-client-id" class="form-control{{ $errors->has('paypal_client_id') ? ' is-invalid' : '' }}" value="{{ old('paypal_client_id') ?? config('settings.paypal_client_id') }}">
                        @if ($errors->has('paypal_client_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paypal_client_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paypal-secret">{{ __('Secret') }}</label>
                        <input type="password" name="paypal_secret" id="i-paypal-secret" class="form-control{{ $errors->has('paypal_secret') ? ' is-invalid' : '' }}" value="{{ old('paypal_secret') ?? config('settings.paypal_secret') }}">
                        @if ($errors->has('paypal_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paypal_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paypal-webhook-id">{{ __('Webhook ID') }}</label>
                        <input type="text" name="paypal_webhook_id" id="i-paypal-webhook-id" class="form-control{{ $errors->has('paypal_webhook_id') ? ' is-invalid' : '' }}" value="{{ old('paypal_webhook_id') ?? config('settings.paypal_webhook_id') }}">
                        @if ($errors->has('paypal_webhook_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paypal_webhook_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paypal-wh-url">{{ __('Webhook URL') }}</label>
                        <div class="input-group">
                            <input type="text" dir="ltr" name="paypal_wh_url" id="i-paypal-wh-url" class="form-control" value="{{ route('webhooks.paypal') }}" readonly>
                            <div class="input-group-append">
                                <div class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-paypal-wh-url">{{ __('Copy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-stripe" role="tabpanel" aria-labelledby="pills-stripe-tab">
                    <div class="form-group">
                        <label for="i-stripe">{{ __('Enabled') }}</label>
                        <select name="stripe" id="i-stripe" class="custom-select{{ $errors->has('stripe') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('stripe') !== null && old('stripe') == $key) || (config('settings.stripe') == $key && old('stripe') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('stripe'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('stripe') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-stripe-key">{{ __('Publishable key') }}</label>
                        <input type="text" name="stripe_key" id="i-stripe-key" class="form-control{{ $errors->has('stripe_key') ? ' is-invalid' : '' }}" value="{{ old('stripe_key') ?? config('settings.stripe_key') }}">
                        @if ($errors->has('stripe_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('stripe_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-stripe-secret">{{ __('Secret key') }}</label>
                        <input type="password" name="stripe_secret" id="i-stripe-secret" class="form-control{{ $errors->has('stripe_secret') ? ' is-invalid' : '' }}" value="{{ old('stripe_secret') ?? config('settings.stripe_secret') }}">
                        @if ($errors->has('stripe_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('stripe_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-stripe-wh-secret">{{ __('Signing secret') }}</label>
                        <input type="password" name="stripe_wh_secret" id="i-stripe-wh-secret" class="form-control{{ $errors->has('stripe_wh_secret') ? ' is-invalid' : '' }}" value="{{ old('stripe_wh_secret') ?? config('settings.stripe_wh_secret') }}">
                        @if ($errors->has('stripe_wh_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('stripe_wh_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-stripe-wh-url">{{ __('Webhook URL') }}</label>
                        <div class="input-group">
                            <input type="text" dir="ltr" name="stripe_wh_url" id="i-stripe-wh-url" class="form-control" value="{{ route('webhooks.stripe') }}" readonly>
                            <div class="input-group-append">
                                <div class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-stripe-wh-url">{{ __('Copy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-razorpay" role="tabpanel" aria-labelledby="pills-razorpay-tab">
                    <div class="form-group">
                        <label for="i-razorpay">{{ __('Enabled') }}</label>
                        <select name="razorpay" id="i-razorpay" class="custom-select{{ $errors->has('razorpay') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('razorpay') !== null && old('razorpay') == $key) || (config('settings.razorpay') == $key && old('razorpay') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('razorpay'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('razorpay') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-razorpay-key">{{ __('Key ID') }}</label>
                        <input type="text" name="razorpay_key" id="i-razorpay-key" class="form-control{{ $errors->has('razorpay_key') ? ' is-invalid' : '' }}" value="{{ old('razorpay_key') ?? config('settings.razorpay_key') }}">
                        @if ($errors->has('razorpay_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('razorpay_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-razorpay-secret">{{ __('Key secret') }}</label>
                        <input type="password" name="razorpay_secret" id="i-razorpay-secret" class="form-control{{ $errors->has('razorpay_secret') ? ' is-invalid' : '' }}" value="{{ old('razorpay_secret') ?? config('settings.razorpay_secret') }}">
                        @if ($errors->has('razorpay_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('razorpay_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-razorpay-wh-secret">{{ __('Webhook secret') }}</label>
                        <input type="password" name="razorpay_wh_secret" id="i-razorpay-wh-secret" class="form-control{{ $errors->has('razorpay_wh_secret') ? ' is-invalid' : '' }}" value="{{ old('razorpay_wh_secret') ?? config('settings.razorpay_wh_secret') }}">
                        @if ($errors->has('razorpay_wh_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('razorpay_wh_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-razorpay-wh-url">{{ __('Webhook URL') }}</label>
                        <div class="input-group">
                            <input type="text" dir="ltr" name="razorpay_wh_url" id="i-razorpay-wh-url" class="form-control" value="{{ route('webhooks.razorpay') }}" readonly>
                            <div class="input-group-append">
                                <div class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-razorpay-wh-url">{{ __('Copy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-paystack" role="tabpanel" aria-labelledby="pills-paystack-tab">
                    <div class="form-group">
                        <label for="i-paystack">{{ __('Enabled') }}</label>
                        <select name="paystack" id="i-paystack" class="custom-select{{ $errors->has('paystack') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('paystack') !== null && old('paystack') == $key) || (config('settings.paystack') == $key && old('paystack') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('paystack'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paystack') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paystack-key">{{ __('Publishable key') }}</label>
                        <input type="text" name="paystack_key" id="i-paystack-key" class="form-control{{ $errors->has('paystack_key') ? ' is-invalid' : '' }}" value="{{ old('paystack_key') ?? config('settings.paystack_key') }}">
                        @if ($errors->has('paystack_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paystack_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paystack-secret">{{ __('Secret key') }}</label>
                        <input type="password" name="paystack_secret" id="i-paystack-secret" class="form-control{{ $errors->has('paystack_secret') ? ' is-invalid' : '' }}" value="{{ old('paystack_secret') ?? config('settings.paystack_secret') }}">
                        @if ($errors->has('paystack_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('paystack_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-paystack-wh-url">{{ __('Webhook URL') }}</label>
                        <div class="input-group">
                            <input type="text" dir="ltr" name="paystack_wh_url" id="i-paystack-wh-url" class="form-control" value="{{ route('webhooks.paystack') }}" readonly>
                            <div class="input-group-append">
                                <div class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-paystack-wh-url">{{ __('Copy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-coinbase" role="tabpanel" aria-labelledby="pills-coinbase-tab">
                    <div class="form-group">
                        <label for="i-coinbase">{{ __('Enabled') }}</label>
                        <select name="coinbase" id="i-coinbase" class="custom-select{{ $errors->has('coinbase') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('coinbase') !== null && old('coinbase') == $key) || (config('settings.coinbase') == $key && old('coinbase') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('coinbase'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('coinbase') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-coinbase-key">{{ __('Client ID') }}</label>
                        <input type="text" name="coinbase_key" id="i-coinbase-key" class="form-control{{ $errors->has('coinbase_key') ? ' is-invalid' : '' }}" value="{{ old('coinbase_key') ?? config('settings.coinbase_key') }}">
                        @if ($errors->has('coinbase_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('coinbase_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-coinbase-wh-secret">{{ __('Webhook shared secret') }}</label>
                        <input type="password" name="coinbase_wh_secret" id="i-coinbase-wh-secret" class="form-control{{ $errors->has('coinbase_wh_secret') ? ' is-invalid' : '' }}" value="{{ old('coinbase_wh_secret') ?? config('settings.coinbase_wh_secret') }}">
                        @if ($errors->has('coinbase_wh_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('coinbase_wh_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-coinbase-wh-url">{{ __('Webhook URL') }}</label>
                        <div class="input-group">
                            <input type="text" dir="ltr" name="coinbase_wh_url" id="i-coinbase-wh-url" class="form-control" value="{{ route('webhooks.coinbase') }}" readonly>
                            <div class="input-group-append">
                                <div class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-coinbase-wh-url">{{ __('Copy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-cryptocom" role="tabpanel" aria-labelledby="pills-cryptocom-tab">
                    <div class="form-group">
                        <label for="i-cryptocom">{{ __('Enabled') }}</label>
                        <select name="cryptocom" id="i-cryptocom" class="custom-select{{ $errors->has('cryptocom') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('cryptocom') !== null && old('cryptocom') == $key) || (config('settings.cryptocom') == $key && old('cryptocom') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('cryptocom'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cryptocom') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-cryptocom-key">{{ __('Publishable key') }}</label>
                        <input type="text" name="cryptocom_key" id="i-cryptocom-key" class="form-control{{ $errors->has('cryptocom_key') ? ' is-invalid' : '' }}" value="{{ old('cryptocom_key') ?? config('settings.cryptocom_key') }}">
                        @if ($errors->has('cryptocom_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cryptocom_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-cryptocom-secret">{{ __('Secret key') }}</label>
                        <input type="password" name="cryptocom_secret" id="i-cryptocom-secret" class="form-control{{ $errors->has('cryptocom_secret') ? ' is-invalid' : '' }}" value="{{ old('cryptocom_secret') ?? config('settings.cryptocom_secret') }}">
                        @if ($errors->has('cryptocom_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cryptocom_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-cryptocom-wh-secret">{{ __('Signing secret') }}</label>
                        <input type="password" name="cryptocom_wh_secret" id="i-cryptocom-wh-secret" class="form-control{{ $errors->has('cryptocom_wh_secret') ? ' is-invalid' : '' }}" value="{{ old('cryptocom_wh_secret') ?? config('settings.cryptocom_wh_secret') }}">
                        @if ($errors->has('cryptocom_wh_secret'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('cryptocom_wh_secret') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-cryptocom-wh-url">{{ __('Webhook URL') }}</label>
                        <div class="input-group">
                            <input type="text" dir="ltr" name="cryptocom_wh_url" id="i-cryptocom-wh-url" class="form-control" value="{{ route('webhooks.cryptocom') }}" readonly>
                            <div class="input-group-append">
                                <div class="btn btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-cryptocom-wh-url">{{ __('Copy') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-bank" role="tabpanel" aria-labelledby="pills-bank-tab">
                    <div class="form-group">
                        <label for="i-bank">{{ __('Enabled') }}</label>
                        <select name="bank" id="i-bank" class="custom-select{{ $errors->has('bank') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('bank') !== null && old('bank') == $key) || (config('settings.bank') == $key && old('bank') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('bank'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bank-account-owner">{{ __('Account owner') }}</label>
                        <input type="text" name="bank_account_owner" id="i-bank-account-owner" class="form-control{{ $errors->has('bank_account_owner') ? ' is-invalid' : '' }}" value="{{ old('bank_account_owner') ?? config('settings.bank_account_owner') }}">
                        @if ($errors->has('bank_account_owner'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank_account_owner') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bank-account-number">{{ __('Account number') }}</label>
                        <input type="text" name="bank_account_number" id="i-bank-account-number" class="form-control{{ $errors->has('bank_account_number') ? ' is-invalid' : '' }}" value="{{ old('bank_account_number') ?? config('settings.bank_account_number') }}">
                        @if ($errors->has('bank_account_number'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank_account_number') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bank-name">{{ __('Bank name') }}</label>
                        <input type="text" name="bank_name" id="i-bank-name" class="form-control{{ $errors->has('bank_name') ? ' is-invalid' : '' }}" value="{{ old('bank_name') ?? config('settings.bank_name') }}">
                        @if ($errors->has('bank_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bank-routing-number">{{ __('Routing number') }}</label>
                        <input type="text" name="bank_routing_number" id="i-bank-routing-number" class="form-control{{ $errors->has('bank_routing_number') ? ' is-invalid' : '' }}" value="{{ old('bank_routing_number') ?? config('settings.bank_routing_number') }}">
                        @if ($errors->has('bank_routing_number'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank_routing_number') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bank-iban">{{ __('IBAN') }}</label>
                        <input type="text" name="bank_iban" id="i-bank-iban" class="form-control{{ $errors->has('bank_iban') ? ' is-invalid' : '' }}" value="{{ old('bank_iban') ?? config('settings.bank_iban') }}">
                        @if ($errors->has('bank_iban'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank_iban') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bank-bic-swift">{{ __('BIC') }} / {{ __('SWIFT') }}</label>
                        <input type="text" name="bank_bic_swift" id="i-bank-bic-swift" class="form-control{{ $errors->has('bank_bic_swift') ? ' is-invalid' : '' }}" value="{{ old('bank_bic_swift') ?? config('settings.bank_bic_swift') }}">
                        @if ($errors->has('bank_bic_swift'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bank_bic_swift') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-brc20" role="tabpanel" aria-labelledby="pills-brc20-tab">
                    <div class="form-group">
                        <label for="i-brc20">{{ __('Enabled') }}</label>
                        <select name="brc20" id="i-brc20" class="custom-select{{ $errors->has('brc20') ? ' is-invalid' : '' }}">
                            @foreach([1 => __('Yes'), 0 => __('No')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('brc20') !== null && old('brc20') == $key) || (config('settings.brc20') == $key && old('brc20') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('brc20'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('brc20') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-brc20-account-owner">{{ __('Address') }}</label>
                        <input type="text" name="brc20_address" id="i-brc20-account-owner" class="form-control{{ $errors->has('brc20_address') ? ' is-invalid' : '' }}" value="{{ old('brc20_address') ?? config('settings.brc20_address') }}">
                        @if ($errors->has('brc20_address'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('brc20_address') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>