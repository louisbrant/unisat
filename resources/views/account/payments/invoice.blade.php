@extends('layouts.invoice')

@section('site_title', formatTitle([__('Invoice'), config('settings.title')]))

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="container-md p-3 mx-auto">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10 col-xl-9">
                <div class="d-print-none">
                    <div class="row no-gutters">
                        <div class="col">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('Back') }}</a>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" onclick="window.print();">{{ __('Print') }}</button>
                        </div>
                    </div>
                </div>

                <div class="bg-base-0 border rounded p-5 my-3 position-relative overflow-hidden">
                    @if ($payment->status == 'cancelled')
                        <div class="position-absolute top-0 right-0 bottom-0 left-0 d-flex align-items-center justify-content-center z-1">
                            <h1 class="font-weight-bold text-uppercase opacity-50 text-danger" style="font-size: 6rem; transform: rotate(-45deg);">{{ __('Cancelled') }}</h1>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <!-- Organization Details -->
                            @if($payment->seller->title)
                                <h3>{{ $payment->seller->title }}</h3>
                            @endif
                        </div>
                        <div class="col-12 col-sm-6 {{ (__('lang_dir') == 'rtl' ? 'text-right text-sm-left' : 'text-left text-sm-right') }}"><h3 class="text-uppercase text-muted">{{ __('Invoice') }}</h3></div>
                        <div class="col-12 py-3 {{ (__('lang_dir') == 'rtl' ? 'text-right text-sm-left' : 'text-left text-sm-right') }}">
                            <div><span class="text-muted">{{ __('Date') }}:</span> {{ $payment->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }}</div>
                            <div><span class="text-muted">{{ __('Invoice ID') }}:</span> {{ $payment->invoice_id }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6 py-3">
                            <h5>{{ __('Vendor') }}</h5>
                            @if ($payment->seller->vendor)
                                <div><span class="text-muted">{{ __('Name') }}:</span> {{ $payment->seller->vendor }}</div>
                            @endif

                            @if ($payment->seller->address)
                                <div><span class="text-muted">{{ __('Address') }}:</span> {{ $payment->seller->address }}</div>
                            @endif

                            @if ($payment->seller->city)
                                <div><span class="text-muted">{{ __('City') }}:</span> {{ $payment->seller->city }}</div>
                            @endif

                            @if ($payment->seller->state)
                                <div><span class="text-muted">{{ __('State') }}:</span> {{ $payment->seller->state }}</div>
                            @endif

                            @if ($payment->seller->postal_code)
                                <div><span class="text-muted">{{ __('Postal code') }}:</span> {{ $payment->seller->postal_code }}</div>
                            @endif

                            @if ($payment->seller->country)
                                <div><span class="text-muted">{{ __('Country') }}:</span> {{ $payment->seller->country }}</div>
                            @endif

                            @if ($payment->seller->phone)
                                <div><span class="text-muted">{{ __('Phone') }}:</span> {{ $payment->seller->phone }}</div>
                            @endif

                            @if ($payment->seller->vat_number)
                                <div><span class="text-muted">{{ __('VAT number') }}:</span> {{ $payment->seller->vat_number }}</div>
                            @endif
                        </div>

                        <div class="col-12 col-sm-6 py-3">
                            <h5>{{ __('Client') }}</h5>
                            @if($payment->customer->name)
                                <div><span class="text-muted">{{ __('Name') }}:</span> {{ $payment->customer->name }}</div>
                            @endif

                            @if ($payment->customer->address)
                                <div><span class="text-muted">{{ __('Address') }}:</span> {{ $payment->customer->address }}</div>
                            @endif

                            @if ($payment->customer->city)
                                <div><span class="text-muted">{{ __('City') }}:</span> {{ $payment->customer->city }}</div>
                            @endif

                            @if ($payment->customer->state)
                                <div><span class="text-muted">{{ __('State') }}:</span> {{ $payment->customer->state }}</div>
                            @endif

                            @if ($payment->customer->postal_code)
                                <div><span class="text-muted">{{ __('Postal code') }}:</span> {{ $payment->customer->postal_code }}</div>
                            @endif

                            @if ($payment->customer->country)
                                <div><span class="text-muted">{{ __('Country') }}:</span> {{ $payment->customer->country }}</div>
                            @endif

                            @if ($payment->customer->phone)
                                <div><span class="text-muted">{{ __('Phone') }}:</span> {{ $payment->customer->phone }}</div>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="list-group list-group-flush">
                                <div class="list-group-item px-0">
                                    <div class="row font-weight-medium">
                                        <div class="col-6">{{ __('Description') }}</div>
                                        <div class="col-3">{{ __('Date') }}</div>
                                        <div class="col-3">{{ __('Amount') }}</div>
                                    </div>
                                </div>

                                <!-- Display The Item -->
                                <div class="list-group-item px-0">
                                    <div class="row">
                                        <div class="col-6">{{ $payment->product->name }} ({{ $payment->interval == 'month' ? __('Month') : __('Year') }})</div>
                                        <div class="col-3">
                                            <div>{{ $payment->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }}</div>
                                        </div>
                                        <div class="col-3">{{ formatMoney(($payment->interval == 'month' ? $payment->product->amount_month : $payment->product->amount_year), $payment->currency) }} {{ $payment->currency }}</div>
                                    </div>
                                </div>

                                <!-- Display The Subtotal -->
                                @if ($payment->coupon || $payment->tax_rates)
                                    <div class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-9 {{ (__('lang_dir') == 'rtl' ? 'text-left' : 'text-right') }}">{{ __('Subtotal') }}</div>
                                            <div class="col-3">{{ formatMoney(($payment->interval == 'month' ? $payment->product->amount_month : $payment->product->amount_year), $payment->currency) }} {{ strtoupper($payment->currency) }}</div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Display The Discount -->
                                @if ($payment->coupon)
                                    <div class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-9 {{ (__('lang_dir') == 'rtl' ? 'text-left' : 'text-right') }}">
                                                {{ $payment->coupon->name }} ({{ $payment->coupon->percentage }}% {{ __('Discount') }})
                                            </div>
                                            <div class="col-3">-{{ formatMoney(calculateDiscount(($payment->interval == 'month' ? $payment->product->amount_month : $payment->product->amount_year), $payment->coupon->percentage), $payment->currency) }} {{ $payment->currency }}</div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Display The Taxes -->
                                @foreach(collect($payment->tax_rates) as $taxRate)
                                    <div class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-9 {{ (__('lang_dir') == 'rtl' ? 'text-left' : 'text-right') }}">{{ $taxRate->name }} ({{ $taxRate->percentage }}% {{ $taxRate->type ? __('excl.') : __('incl.') }})</div>

                                            <div class="col-3">
                                                @if($taxRate->type)
                                                    {{ formatMoney(checkoutExclusiveTax(($payment->interval == 'month' ? $payment->product->amount_month : $payment->product->amount_year), $payment->coupon->percentage ?? null, $taxRate->percentage, $inclTaxRatesPercentage), $payment->currency) }}
                                                @else
                                                    {{ formatMoney(calculateInclusiveTax(($payment->interval == 'month' ? $payment->product->amount_month : $payment->product->amount_year), $payment->coupon->percentage ?? null, $taxRate->percentage, $inclTaxRatesPercentage), $payment->currency) }}
                                                @endif
                                                {{ $payment->currency }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Display The Final Total -->
                                <div class="list-group-item px-0">
                                    <div class="row">
                                        <div class="col-9 {{ (__('lang_dir') == 'rtl' ? 'text-left' : 'text-right') }} font-weight-bold">{{ __('Total') }}</div>
                                        <div class="col-3 font-weight-bold">{{ formatMoney($payment->amount, $payment->currency) }} {{ $payment->currency }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection