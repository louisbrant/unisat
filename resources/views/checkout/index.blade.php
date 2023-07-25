@extends('layouts.app')

@section('site_title', formatTitle([__('Checkout'), config('settings.title')]))

@section('content')
    <div class="bg-base-1 flex-fill">
        <div class="container py-3 my-3">

            @include('shared.breadcrumbs', ['breadcrumbs' => [
                ['url' => route('dashboard'), 'title' => __('Home')],
                ['url' => route('pricing'), 'title' => __('Pricing')],
                ['title' => __('Checkout')],
            ]])

            <h1 class="h2 mb-3 d-inline-block">{{ __('Checkout') }}</h1>

            @include('shared.message')
            <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="transfer_inscription">
                Inscription for transfer has not been created.
                <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-12 p-0" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" class="fill-current width-4 height-4" viewBox="0 0 16 16"><rect width="16" height="16" style="fill:none"></rect><path d="M9.41,8l5.3-5.29a1,1,0,1,0-1.42-1.42L8,6.59,2.71,1.29A1,1,0,0,0,1.29,2.71L6.59,8l-5.3,5.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L8,9.41l5.29,5.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z"></path></svg></span>
                </button>
            </div>
            <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="transaction_error">
                <p id="transaction_error_text">
                    
                </p>
                <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-12 p-0" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" class="fill-current width-4 height-4" viewBox="0 0 16 16"><rect width="16" height="16" style="fill:none"></rect><path d="M9.41,8l5.3-5.29a1,1,0,1,0-1.42-1.42L8,6.59,2.71,1.29A1,1,0,0,0,1.29,2.71L6.59,8l-5.3,5.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L8,9.41l5.29,5.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z"></path></svg></span>
                </button>
            </div>
            <div class="alert alert-danger alert-dismissible fade show d-none" role="alert" id="unisat_error">
                Unisat API error!
                <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-12 p-0" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true" class="d-flex align-items-center"><svg xmlns="http://www.w3.org/2000/svg" class="fill-current width-4 height-4" viewBox="0 0 16 16"><rect width="16" height="16" style="fill:none"></rect><path d="M9.41,8l5.3-5.29a1,1,0,1,0-1.42-1.42L8,6.59,2.71,1.29A1,1,0,0,0,1.29,2.71L6.59,8l-5.3,5.29a1,1,0,0,0,0,1.42,1,1,0,0,0,1.42,0L8,9.41l5.29,5.3a1,1,0,0,0,1.42,0,1,1,0,0,0,0-1.42Z"></path></svg></span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" id="form-payment">
                @csrf
                <div class="row m-n2">
                    <div class="col-12 col-lg-8 p-2">
                        <div class="card border-0 shadow-sm mb-3 overflow-hidden @if(($plan->trial_days && !$user->plan_trial_ends_at) || ($coupon && $coupon->type)) d-none @endif">
                            <div class="card-header">
                                <div class="font-weight-medium py-1">{{ __('Payment method') }}</div>
                            </div>
                            <div class="form-group" hidden>
                                <input id="i-invalid-brc20-pay" type="text" dir="ltr" name="invalid-brc20-pay" value="{{ $errors->first('brc20') }}">
                                <input id="i-invalid-brc20-pay-val" type="text" dir="ltr" name="invalid-brc20-pay" @if(request()->input('interval') == 'month') value="{{__(':amount', ['amount' => formatMoney(checkoutTotal($plan->amount_month, $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency), 'currency' => e($plan->currency)])}}" @endif value="{{__(':amount', ['amount' => formatMoney(checkoutTotal($plan->amount_year, $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency), 'currency' => e($plan->currency)])}}">
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach(paymentProcessors() as $key => $value)
                                        <li class="list-group-item">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="payment-method-{{ $key }}" name="payment_processor" class="custom-control-input{{ $errors->has('payment_processor') ? ' is-invalid' : '' }}" value="{{ $key }}" @if(request()->input('payment') == $key && old('payment_processor') == null || old('payment_processor') == $key || $loop->first && old('payment_processor') == null)) checked @endif>
                                                <label class="custom-control-label cursor-pointer d-block" for="payment-method-{{ $key }}">
                                                    <div class="row">
                                                        <div class="col-12 col-lg d-flex align-items-center">
                                                            <img src="{{ asset('img/icons/payments/' . $key . '.svg') }}" class="width-6 rounded-sm"><span class="font-weight-bold {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">{{ $value['name'] == 'Bank' ? __('Bank') : $value['name'] }}</span>
                                                        </div>
                                                        <div class="col-12 col-lg-auto mt-1 mt-lg-0">
                                                            <div class="text-muted">{{ __($value['type']) }}</div>
                                                        </div>
                                                    </div>
                                                </label>

                                                @if($key == 'bank')
                                                    <div class="d-none" id="bank-instructions">
                                                        <div class="alert alert-info my-3">
                                                            {!! __('When issuing the bank payment, please make sure to include the value of the :id field in the reference field of the payment.', ['id' => '<strong>' . __('Payment ID') . '</strong>']) !!}
                                                        </div>

                                                        @if(config('settings.bank_account_owner'))
                                                            <div class="form-group mb-2">
                                                                <label for="i-bank-account-owner">{{ __('Account owner') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="bank_account_owner" id="i-bank-account-owner" class="form-control form-control-sm{{ $errors->has('bank_account_owner') ? ' is-invalid' : '' }}" value="{{ config('settings.bank_account_owner') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-bank-account-owner">{{ __('Copy') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('bank_account_owner'))
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $errors->first('bank_account_owner') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(config('settings.bank_account_number'))
                                                            <div class="form-group mb-2">
                                                                <label for="i-bank-account-number">{{ __('Account number') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="bank_account_number" id="i-bank-account-number" class="form-control form-control-sm{{ $errors->has('bank_account_number') ? ' is-invalid' : '' }}" value="{{ config('settings.bank_account_number') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-bank-account-number">{{ __('Copy') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('bank_account_number'))
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $errors->first('bank_account_number') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(config('settings.bank_name'))
                                                            <div class="form-group mb-2">
                                                                <label for="i-bank-name">{{ __('Bank name') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="bank_name" id="i-bank-name" class="form-control form-control-sm{{ $errors->has('bank_name') ? ' is-invalid' : '' }}" value="{{ config('settings.bank_name') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-bank-name">{{ __('Copy') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('bank_name'))
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $errors->first('bank_name') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(config('settings.bank_routing_number'))
                                                            <div class="form-group mb-2">
                                                                <label for="i-bank-routing-number">{{ __('Routing number') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="bank_routing_number" id="i-bank-routing-number" class="form-control form-control-sm{{ $errors->has('bank_routing_number') ? ' is-invalid' : '' }}" value="{{ config('settings.bank_routing_number') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-bank-routing-number">{{ __('Copy') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('bank_routing_number'))
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $errors->first('bank_routing_number') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(config('settings.bank_iban'))
                                                            <div class="form-group mb-2">
                                                                <label for="i-bank-iban">{{ __('IBAN') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="bank_iban" id="i-bank-iban" class="form-control form-control-sm{{ $errors->has('bank_iban') ? ' is-invalid' : '' }}" value="{{ config('settings.bank_iban') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-bank-iban">{{ __('Copy') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('bank_iban'))
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $errors->first('bank_iban') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if(config('settings.bank_bic_swift'))
                                                            <div class="form-group mb-2">
                                                                <label for="i-bank-bic-swift">{{ __('BIC') }} / {{ __('SWIFT') }}</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="bank_bic_swift" id="i-bank-bic-swift" class="form-control form-control-sm{{ $errors->has('bank_bic_swift') ? ' is-invalid' : '' }}" value="{{ config('settings.bank_bic_swift') }}" readonly>
                                                                    <div class="input-group-append">
                                                                        <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-bank-bic-swift">{{ __('Copy') }}</div>
                                                                    </div>
                                                                </div>
                                                                @if ($errors->has('bank_bic_swift'))
                                                                    <span class="invalid-feedback d-block" role="alert">
                                                                        <strong>{{ $errors->first('bank_bic_swift') }}</strong>
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        @endif

                                                        <div class="form-group mb-2">
                                                            <label for="i-payment-id">{{ __('Payment ID') }}</label>
                                                            <div class="input-group">
                                                                <input type="text" name="payment_id" id="i-payment-id" class="form-control form-control-sm{{ $errors->has('payment_id') ? ' is-invalid' : '' }}" value="{{ old('payment_id') ?? Str::random(16) }}" readonly>
                                                                <div class="input-group-append">
                                                                    <div class="btn btn-sm btn-primary" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#i-payment-id">{{ __('Copy') }}</div>
                                                                </div>
                                                            </div>
                                                            @if ($errors->has('payment_id'))
                                                                <span class="invalid-feedback d-block" role="alert">
                                                                    <strong>{{ $errors->first('payment_id') }}</strong>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if ($errors->has('payment_processor'))
                                <div class="card-footer">
                                    <span class="invalid-feedback d-block mt-0" role="alert">
                                        <strong>{{ $errors->first('payment_processor') }}</strong>
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-header">
                                <div class="font-weight-medium py-1">{{ __('Billing information') }}</div>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="i-name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="i-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') ?? ($user->billing_information->name ?? null) }}">
                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label for="i-address">{{ __('Address') }}</label>
                                    <input type="text" name="address" id="i-address" class="form-control{{ $errors->has('address') ? ' is-invalid' : '' }}" value="{{ old('address') ?? ($user->billing_information->address ?? null) }}">
                                    @if ($errors->has('address'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('address') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="row mx-n2">
                                    <div class="col-12 col-md-6 px-2">
                                        <div class="form-group">
                                            <label for="i-city">{{ __('City') }}</label>
                                            <input type="text" name="city" id="i-city" class="form-control{{ $errors->has('city') ? ' is-invalid' : '' }}" value="{{ old('city') ?? ($user->billing_information->city ?? null) }}">
                                            @if ($errors->has('city'))
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('city') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3 px-2">
                                        <div class="form-group">
                                            <label for="i-state">{{ __('State') }}</label>
                                            <input type="text" name="state" id="i-state" class="form-control{{ $errors->has('state') ? ' is-invalid' : '' }}" value="{{ old('state') ?? ($user->billing_information->state ?? null) }}">
                                            @if ($errors->has('state'))
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('state') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3 px-2">
                                        <div class="form-group">
                                            <label for="i-postal-code">{{ __('Postal code') }}</label>
                                            <input type="text" name="postal_code" id="i-postal-code" class="form-control{{ $errors->has('postal_code') ? ' is-invalid' : '' }}" value="{{ old('postal_code') ?? ($user->billing_information->postal_code ?? null) }}">
                                            @if ($errors->has('postal_code'))
                                                <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('postal_code') }}</strong>
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="i-country">{{ __('Country') }}</label>
                                    <select name="country" id="i-country" class="custom-select{{ $errors->has('country') ? ' is-invalid' : '' }}">
                                        <option value="" hidden disabled selected>{{ __('Country') }}</option>
                                        @foreach(config('countries') as $key => $value)
                                            <option value="{{ $key }}" @if ((old('country') !== null && $key == old('country')) || (isset($user->billing_information->country) && $key == $user->billing_information->country) && old('country') == null) selected @endif>{{ __($value) }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('country'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('country') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group mb-0">
                                    <label for="i-phone">{{ __('Phone') }}</label>
                                    <input type="text" name="phone" id="i-phone" class="form-control{{ $errors->has('phone') ? ' is-invalid' : '' }}" value="{{ old('phone') ?? ($user->billing_information->phone ?? null) }}">
                                    @if ($errors->has('phone'))
                                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('phone') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-4 p-2 d-flex flex-column justify-content-start">
                        @if(($plan->trial_days && !$user->plan_trial_ends_at) || ($coupon && $coupon->type))
                            <div class="card border-0 shadow-sm">
                                <div class="card-header"><div class="font-weight-medium py-1">{{ __('Order summary') }}</div></div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">
                                            <div class="row">
                                                <div class="col">
                                                    <div>{{ __(':name plan', ['name' => $plan->name]) }}</div>

                                                    <div>
                                                        <div class="small text-muted">{{ __('Not billed.') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col">
                                            <div>
                                                @if($plan->trial_days && !$user->plan_trial_ends_at)
                                                    {{ __('Trial days') }}
                                                @else
                                                    {{ __('Days') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            @if($plan->trial_days && !$user->plan_trial_ends_at)
                                                {{ $plan->trial_days }}
                                            @else
                                                {{ $coupon->days < 0 ? __('Unlimited') : $coupon->days }}
                                                <input type="hidden" name="coupon" value="{{ $coupon->code }}">
                                                <input type="hidden" name="coupon_set" value="true">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="interval" value="month">

                            <div class="mt-3">
                                <div class="small text-muted">{!! __('By continuing, you agree with the :terms.', ['terms' => mb_strtolower('<a href="'.config('settings.legal_terms_url').'" target="_blank">'. __('Terms of service') .'</a>')]) !!}</div>
                            </div>

                            <button type="submit" name="submit" class="btn btn-success btn-block mt-3">
                                {{ __('Start') }}
                            </button>

                            @if($plan->trial_days && !$user->plan_trial_ends_at)
                                <button type="submit" name="skip_trial" class="btn btn-secondary btn-block my-3" value="1">
                                    {{ __('Skip trial') }}
                                </button>
                            @endif
                        @else
                            <div class="card border-0 shadow-sm">
                                <div class="card-header"><div class="font-weight-medium py-1">{{ __('Order summary') }}</div></div>
                                <div class="card-body">
                                    <div class="btn-group btn-group-toggle d-flex" data-toggle="buttons">
                                        <label class="btn {{ $errors->has('interval') ? 'btn-outline-danger' : 'btn-outline-dark' }} w-100{{ request()->input('interval') == 'month' ? ' active' : ''}}" id="plan-month">
                                            <input type="radio" name="interval" value="month" @if(request()->input('interval') == 'month') checked="checked" @endif>{{ __('Monthly') }}
                                        </label>
                                        <label class="btn {{ $errors->has('interval') ? 'btn-outline-danger' : 'btn-outline-dark' }} w-100{{ request()->input('interval') == 'year' ? ' active' : ''}}" id="plan-year">
                                            <input type="radio" name="interval" value="year" @if(request()->input('interval') == 'year') checked="checked" @endif>{{ __('Yearly') }}

                                            @if(($plan->amount_month * 12) > $plan->amount_year)
                                                <span class="badge bg-success text-white">-{{ number_format(((($plan->amount_month * 12) - $plan->amount_year)/($plan->amount_month * 12) * 100), 0) }}%</span>
                                            @endif
                                        </label>
                                    </div>
                                    @if ($errors->has('interval'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $errors->first('interval') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item pt-0">
                                            <div class="row">
                                                <div class="col">
                                                    <div>{{ __(':name plan', ['name' => $plan->name]) }}</div>

                                                    <div class="d-none checkout-month">
                                                        <div class="small text-muted">
                                                            <span class="d-none checkout-subscription">{!! ($plan->trial_days && !$user->plan_trial_ends_at) ? __('Billed :interval, after trial ends.', ['interval' => mb_strtolower(__('Monthly'))]) :__('Billed :interval.', ['interval' => mb_strtolower(__('Monthly'))]) !!}</span>
                                                            <span class="d-none checkout-one-time">{!! __('Billed once.') !!}</span>
                                                        </div>
                                                    </div>
                                                    <div class="d-none checkout-year">
                                                        <div class="small text-muted">
                                                            <span class="d-none checkout-subscription">{!! ($plan->trial_days && !$user->plan_trial_ends_at) ? __('Billed :interval, after trial ends.', ['interval' => mb_strtolower(__('Yearly'))]) :__('Billed :interval.', ['interval' => mb_strtolower(__('Yearly'))]) !!}</span>
                                                            <span class="d-none checkout-one-time">{!! __('Billed once.') !!}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="d-none checkout-month">
                                                        {{ formatMoney($plan->amount_month, $plan->currency) }} <span class="text-muted">{{ $plan->currency }}</span>
                                                    </div>
                                                    <div class="d-none checkout-year">
                                                        {{ formatMoney($plan->amount_year, $plan->currency) }} <span class="text-muted">{{ $plan->currency }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>

                                        @if(!$coupon || $coupon->type == 0)
                                            @foreach($taxRates as $taxRate)
                                                <li class="list-group-item">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div>{{ $taxRate->name }} ({{ $taxRate->percentage }}% {{ $taxRate->type ? __('excl.') : __('incl.') }})</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            @if($taxRate->type)
                                                                <span class="d-none checkout-month">
                                                            {{ formatMoney(checkoutExclusiveTax($plan->amount_month, $coupon->percentage ?? null, $taxRate->percentage, $inclTaxRatesPercentage), $plan->currency) }}
                                                        </span>
                                                                <span class="d-none checkout-year">
                                                            {{ formatMoney(checkoutExclusiveTax($plan->amount_year, $coupon->percentage ?? null, $taxRate->percentage, $inclTaxRatesPercentage), $plan->currency) }}
                                                        </span>
                                                            @else
                                                                <span class="d-none checkout-month">
                                                            {{ formatMoney(calculateInclusiveTax($plan->amount_month, $coupon->percentage ?? null, $taxRate->percentage, $inclTaxRatesPercentage), $plan->currency) }}
                                                        </span>
                                                                <span class="d-none checkout-year">
                                                            {{ formatMoney(calculateInclusiveTax($plan->amount_year, $coupon->percentage ?? null, $taxRate->percentage, $inclTaxRatesPercentage), $plan->currency) }}
                                                        </span>
                                                            @endif

                                                            <span class="text-muted">{{ $plan->currency }}</span>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        @endif

                                        @if($coupon)
                                            <li class="list-group-item text-success">
                                                <div class="row">
                                                    <div class="col">
                                                        <div>{{ __('Discount') }} ({{ $coupon->percentage }}%)</div>
                                                    </div>
                                                    <div class="col-auto">
                                                    <span class="d-none checkout-month">
                                                        -{{ formatMoney(calculateDiscount($plan->amount_month, $coupon->percentage), $plan->currency) }}
                                                    </span>
                                                        <span class="d-none checkout-year">
                                                        -{{ formatMoney(calculateDiscount($plan->amount_year, $coupon->percentage), $plan->currency) }}
                                                    </span>
                                                        <span class="text-muted">{{ $plan->currency }}</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" name="coupon" value="{{ $coupon->code }}">
                                                <input type="hidden" name="coupon_set" value="true">
                                            </li>
                                        @endif

                                        @if($plan->coupons && !$coupon)
                                            <li class="list-group-item">
                                                <a href="#" id="coupon" class="{{ $errors->has('coupon') || old('coupon') ? 'd-none' : '' }}">{{ __('Have a coupon code?') }}</a>

                                                <div class="form-row {{ $errors->has('coupon') || old('coupon') ? '' : 'd-none' }}" id="coupon-input">
                                                    <div class="col">
                                                        <div class="form-group mb-0">
                                                            <input type="text" name="coupon" id="i-coupon" class="form-control form-control-sm{{ $errors->has('coupon') || old('coupon') ? ' is-invalid' : '' }}" value="{{ old('coupon') }}" placeholder="{{ __('Coupon code') }}"{{ $errors->has('coupon') || old('coupon') ? '' : ' disabled' }}>
                                                        </div>
                                                    </div>

                                                    <div class="col-auto">
                                                        <a href="#" id="coupon-cancel" class="btn btn-sm btn-secondary">{{ __('Cancel') }}</a>
                                                    </div>

                                                    <div class="col-auto">
                                                        <button type="submit" class="btn btn-primary btn-sm">{{ __('Apply') }}</button>
                                                    </div>

                                                    @if ($errors->has('coupon'))
                                                        <div class="col-12">
                                                            <span class="invalid-feedback d-block" role="alert">
                                                                <strong>{{ $errors->first('coupon') }}</strong>
                                                            </span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="card-footer font-weight-bold">
                                    <div class="row">
                                        <div class="col">
                                            <span>{{ __('Total') }}</span>
                                        </div>
                                        <div class="col-auto">
                                            <span class="d-none checkout-month">
                                                {{ formatMoney(checkoutTotal($plan->amount_month, $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency) }}
                                            </span>
                                            <span class="d-none checkout-year">
                                                {{ formatMoney(checkoutTotal($plan->amount_year, $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency) }}
                                            </span>
                                            <span>{{ $plan->currency }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <span class="small text-muted">
                                    <span class="checkout-subscription">
                                        {!! __('By continuing, you agree with the :terms and authorize :title to charge your payment method on a recurring basis.', ['terms' => mb_strtolower('<a href="'.config('settings.legal_terms_url').'" target="_blank">'. __('Terms of service') .'</a>'), 'title' => '<strong>'.e(config(('settings.title'))).'</strong>']) !!} {{ __('You can cancel your subscription at any time.') }}
                                    </span>
                                    <span class="checkout-one-time">
                                        {!! __('By continuing, you agree with the :terms.', ['terms' => mb_strtolower('<a href="'.config('settings.legal_terms_url').'" target="_blank">'. __('Terms of service') .'</a>')]) !!}
                                    </span>
                                </span>
                            </div>

                            <button type="submit" name="submit" class="btn btn-success btn-block my-3">
                                <span class="d-none checkout-month">
                                    {{ __('Pay :amount :currency', ['amount' => formatMoney(checkoutTotal($plan->amount_month, $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency), 'currency' => e($plan->currency)]) }}
                                </span>
                                <span class="d-none checkout-year">
                                    {{ __('Pay :amount :currency', ['amount' => formatMoney(checkoutTotal($plan->amount_year, $coupon->percentage ?? null, $exclTaxRatesPercentage, $inclTaxRatesPercentage), $plan->currency), 'currency' => e($plan->currency)]) }}
                                </span>
                            </button>
                        @endif

                        <div class="d-block mt-lg-auto">
                            <div class="card border-0 h-100 shadow-sm">
                                <div class="card-body d-flex">
                                    <div class="d-flex position-relative text-primary width-12 height-12 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                        <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                        @include('icons.email', ['class' => 'fill-current width-6 height-6'])
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <a href="{{ route('contact') }}" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Need help?') }}</a>

                                        <div class="text-muted">
                                            {{ __('Get in touch with us.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <form action="{{ route('webhooks.brc20') }}" method="POST" id="brc-20-form" >
                <input hidden id="brc20-user-id" type="text" dir="ltr" name="brc20-user-id" value="{{ $user->id }}">
                <input hidden id="brc20-plan-id" type="text" dir="ltr" name="brc20-plan-id" value="{{ $plan->id }}">
                <input hidden id="brc20-plan-amount" type="text" dir="ltr" name="brc20-plan-amount" @if(request()->input('interval') == 'month') value="{{ $plan->amount_month }}" @endif value="{{ $plan->amount_year }}">
                <input hidden id="brc20-plan-currency" type="text" dir="ltr" name="brc20-plan-currency" value="{{ $plan->currency }}">
                <input hidden id="brc20-plan-coupons" type="text" dir="ltr" name="brc20-plan-coupons" value="{{ $plan->coupons }}">
                <input hidden id="brc20-plan-tax_rates" type="text" dir="ltr" name="brc20-plan-tax_rates" value="{{ $plan->tax_rates }}">
                <input hidden id="brc20-tx-id" type="text" dir="ltr" name="brc20-tx-id" value="">
                <input hidden id="brc20-interval" type="text" dir="ltr" name="brc20-interval" @if(request()->input('interval') == 'month') value="month" @endif value="year">
                <input hidden id="brc20-pay-to-address" type="text" dir="ltr" name="brc20-to-address" value="{{ optional($address[0])->value }}">
            </form>
        </div>
    </div>
    <script>
    async function  main (params) {
        if(document.getElementById('i-invalid-brc20-pay')){
            if(document.getElementById('i-invalid-brc20-pay').value){
                let payval = Number(document.getElementById('i-invalid-brc20-pay-val').value.replace(",",""));
                const to_address = document.getElementById("brc20-pay-to-address").value;
                if(payval >= 0 && to_address){
                     setTimeout(async () => {
                        try {
                            await window.unisat.requestAccounts();
                            try {
                                let address = await window.unisat.getAccounts();
                                if (address.length) {
                                    try {
                                        let insptList = await window.unisat.getInscriptions(0,9999999999);
                                        const promiseVal = insptList.list.map(getInscriptionInfo);
                                        const insptListData = await Promise.all(promiseVal);
                                        let selected_inscription = false;
                                        insptListData.map(async (item,i)=>{
                                            if(item?.op == "transfer" && item?.tick == "biso" && item?.p=="brc-20" && item?.amt == payval){
                                                selected_inscription = true;
                                                try {
                                                    $.ajax({
                                                        url: 'https://unisat.io/api/v1/fee-summary',
                                                        method: 'GET',
                                                        headers: {
                                                            'X-Client': 'UniSat Wallet',
                                                            'Content-Type': 'application/json'
                                                        },
                                                        mode: 'cors',
                                                        cache: 'default',
                                                        success: async function (response) {
                                                            if(response?.status == '1'){
                                                                let feeRate = response?.result?.list[1]?.feeRate ?? 20;
                                                                let txid  = await window.unisat.sendInscription(to_address,item?.id,{feeRate});
                                                                document.getElementById("brc20-tx-id").value = txid;
                                                                setTimeout(() => {
                                                                    document.getElementById('brc-20-form').submit();
                                                                }, 8000);
                                                            }
                                                            else{
                                                                $("#unisat_error").removeClass("d-none");
                                                            }
                                                        },
                                                        error: function (xhr, status, error) {
                                                            if (error?.status != 200) {
                                                                console.log(error);
                                                            }
                                                            $("#unisat_error").removeClass("d-none");
                                                        }
                                                    }); 
                                                } catch (e) {
                                                    console.log(e);
                                                    $("#unisat_error").removeClass("d-none");
                                                }
                                            }
                                        })
                                        if(!selected_inscription){
                                            $("#transfer_inscription").removeClass("d-none");
                                        }
                                    }
                                    catch (e) {
                                        console.log(e);
                                    }
                                }
                            } catch (e) {
                                console.log(e);
                            }
                        } catch (e) {
                        }
                    }, 1000);
                }
            }
        }
    }
    async function getInscriptionInfo(data) {
        try {
            const response = await $.ajax({
            url: 'https://ordinals.com/content/' + data?.inscriptionId,
            method: 'GET',
            dataType: 'json'
            });
            response.id = data?.inscriptionId;
            return response;
        } catch (error) {
            console.error(error);
            return {};
        }
    }
    main();
    </script>
@endsection

@include('shared.sidebars.user')
