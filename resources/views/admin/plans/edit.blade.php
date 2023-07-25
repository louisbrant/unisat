@section('site_title', formatTitle([__('Edit'), __('Plan'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['url' => route('admin.plans'), 'title' => __('Plans')],
    ['title' => __('Edit')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Edit') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">
                    {{ __('Plan') }}
                    @if($plan->trashed())
                        <span class="badge badge-danger">{{ __('Disabled') }}</span>
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        @include('admin.plans.partials.menu')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('admin.plans.edit', $plan->id) }}" method="post" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="i-name">{{ __('Name') }}</label>
                <input type="text" name="name" id="i-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ old('name') ?? $plan->name }}">
                @if ($errors->has('name'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('name') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-description">{{ __('Description') }}</label>
                <input type="text" name="description" id="i-description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" value="{{ old('description') ?? $plan->description }}">
                @if ($errors->has('description'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('description') }}</strong>
                    </span>
                @endif
            </div>

            @if(!$plan->isDefault())
                <div class="form-group">
                    <label for="i-trial-days">{{ __('Trial days') }}</label>
                    <input type="number" name="trial_days" id="i-trial-days" class="form-control{{ $errors->has('trial_days') ? ' is-invalid' : '' }}" value="{{ old('trial_days') ?? $plan->trial_days }}">
                    @if ($errors->has('trial_days'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('trial_days') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="i-currency">{{ __('Currency') }}</label>
                    <select name="currency" id="i-currency" class="custom-select{{ $errors->has('currency') ? ' is-invalid' : '' }}">
                        @foreach(config('currencies.all') as $key => $value)
                            <option value="{{ $key }}" @if((old('currency') !== null && old('currency') == $key) || ($plan->currency == $key && old('currency') == null)) selected @endif>{{ $key }} - {{ $value }}</option>
                        @endforeach
                        <option value="XBAI"  @if ($plan->currency == "XBAI") selected  @endif>{{ "XBAI"}} - {{ "XBAI Token" }}</option>
                    </select>
                    @if ($errors->has('currency'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('currency') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="row mx-n2">
                    <div class="col-12 col-lg-6 px-2">
                        <div class="form-group">
                            <label for="i-amount-month">{{ __('Monthly amount') }}</label>
                            <input type="text" name="amount_month" id="i-amount-month" class="form-control{{ $errors->has('amount_month') ? ' is-invalid' : '' }}" value="{{ old('amount_month') ?? $plan->amount_month }}">
                            @if ($errors->has('amount_month'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('amount_month') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 px-2">
                        <div class="form-group">
                            <label for="i-amount-year">{{ __('Yearly amount') }}</label>
                            <input type="text" name="amount_year" id="i-amount-year" class="form-control{{ $errors->has('amount_year') ? ' is-invalid' : '' }}" value="{{ old('amount_year') ?? $plan->amount_year }}">
                            @if ($errors->has('amount_year'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('amount_year') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mx-n2">
                    <div class="col-12 col-lg-6 px-2">
                        <div class="form-group">
                            <label for="i-tax-rates">{{ __('Tax rates') }}</label>
                            <select name="tax_rates[]" id="i-tax-rates" class="custom-select{{ $errors->has('tax_rates') ? ' is-invalid' : '' }}" size="3" multiple>
                                @foreach($taxRates as $taxRate)
                                    <option value="{{ $taxRate->id }}" @if(old('taxRates') !== null && in_array($taxRate->id, old('taxRates')) || old('taxRates') == null && is_array($plan->tax_rates) && in_array($taxRate->id, $plan->tax_rates)) selected @endif>{{ $taxRate->name }} ({{ number_format($taxRate->percentage, 2, __('.'), __(',')) }}% {{ ($taxRate->type ? __('Exclusive') : __('Inclusive')) }})</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tax_rates'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('tax_rates') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-lg-6 px-2">
                        <div class="form-group">
                            <label for="i-coupons">{{ __('Coupons') }}</label>
                            <select name="coupons[]" id="i-coupons" class="custom-select{{ $errors->has('coupons') ? ' is-invalid' : '' }}" size="3" multiple>
                                @foreach($coupons as $coupon)
                                    <option value="{{ $coupon->id }}" @if(old('coupons') !== null && in_array($coupon->id, old('coupons')) || old('coupons') == null && is_array($plan->coupons) && in_array($coupon->id, $plan->coupons)) selected @endif>{{ $coupon->code }} ({{ number_format($coupon->percentage, 2, __('.'), __(',')) }}% {{ ($coupon->type ? __('Redeemable') : __('Discount')) }})</option>
                                @endforeach
                            </select>
                            @if ($errors->has('coupons'))
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $errors->first('coupons') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="form-group">
                <label for="i-visibility">{{ __('Visibility') }}</label>
                <select name="visibility" id="i-visibility" class="custom-select{{ $errors->has('visibility') ? ' is-invalid' : '' }}">
                    @foreach([1 => __('Public'), 0 => __('Unlisted')] as $key => $value)
                        <option value="{{ $key }}" @if((old('visibility') !== null && old('visibility') == $key) || ($plan->visibility == $key && old('visibility') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('visibility'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('visibility') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-position">{{ __('Position') }}</label>
                <input type="number" name="position" id="i-position" class="form-control{{ $errors->has('position') ? ' is-invalid' : '' }}" value="{{ old('position') ?? $plan->position }}">
                @if ($errors->has('position'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('position') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-balance">{{ __('Token Balance') }}</label>
                <input type="number" name="balance" id="i-balance" class="form-control{{ $errors->has('balance') ? ' is-invalid' : '' }}" value="{{ old('balance') ?? $plan->token_balance }}">
                @if ($errors->has('balance'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('balance') }}</strong>
                    </span>
                @endif
            </div>

            <div class="hr-text"><span class="font-weight-medium text-body">{{ __('Features') }}</span></div>

            <div class="form-group">
                <label for="i-features-words">{{ __('Words') }}</label>
                <input type="text" name="features[words]" id="i-features-words" class="form-control{{ $errors->has('features.words') ? ' is-invalid' : '' }}" value="{{ old('features.words') ?? $plan->features->words }}">
                @if ($errors->has('features.words'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.words') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{!! __(':value for unlimited.', ['value' => '<code class="badge badge-secondary">-1</code>']) !!} {!! __(':value for none.', ['value' => '<code class="badge badge-secondary">0</code>']) !!} {!! __(':value for number.', ['value' => '<code class="badge badge-secondary">N</code>']) !!}</small>
            </div>

            <div class="form-group">
                <label for="i-features-documents">{{ __('Documents') }}</label>
                <input type="text" name="features[documents]" id="i-features-documents" class="form-control{{ $errors->has('features.documents') ? ' is-invalid' : '' }}" value="{{ old('features.documents') ?? $plan->features->documents }}">
                @if ($errors->has('features.documents'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.documents') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{!! __(':value for unlimited.', ['value' => '<code class="badge badge-secondary">-1</code>']) !!} {!! __(':value for none.', ['value' => '<code class="badge badge-secondary">0</code>']) !!} {!! __(':value for number.', ['value' => '<code class="badge badge-secondary">N</code>']) !!}</small>
            </div>

            <div class="form-group">
                <label for="i-features-images">{{ __('Images') }}</label>
                <input type="text" name="features[images]" id="i-features-images" class="form-control{{ $errors->has('features.images') ? ' is-invalid' : '' }}" value="{{ old('features.images') ?? $plan->features->images }}">
                @if ($errors->has('features.images'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.images') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{!! __(':value for unlimited.', ['value' => '<code class="badge badge-secondary">-1</code>']) !!} {!! __(':value for none.', ['value' => '<code class="badge badge-secondary">0</code>']) !!} {!! __(':value for number.', ['value' => '<code class="badge badge-secondary">N</code>']) !!}</small>
            </div>

            <div class="form-group">
                <label for="i-features-chats">{{ __('Chats') }}</label>
                <input type="text" name="features[chats]" id="i-features-chats" class="form-control{{ $errors->has('features.chats') ? ' is-invalid' : '' }}" value="{{ old('features.chats') ?? $plan->features->chats }}">
                @if ($errors->has('features.chats'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.chats') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{!! __(':value for unlimited.', ['value' => '<code class="badge badge-secondary">-1</code>']) !!} {!! __(':value for none.', ['value' => '<code class="badge badge-secondary">0</code>']) !!} {!! __(':value for number.', ['value' => '<code class="badge badge-secondary">N</code>']) !!}</small>
            </div>

            <div class="form-group">
                <label for="i-features-transcriptions">{{ __('Transcriptions') }}</label>
                <input type="text" name="features[transcriptions]" id="i-features-transcriptions" class="form-control{{ $errors->has('features.transcriptions') ? ' is-invalid' : '' }}" value="{{ old('features.transcriptions') ?? $plan->features->transcriptions }}">
                @if ($errors->has('features.transcriptions'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.transcriptions') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{!! __(':value for unlimited.', ['value' => '<code class="badge badge-secondary">-1</code>']) !!} {!! __(':value for none.', ['value' => '<code class="badge badge-secondary">0</code>']) !!} {!! __(':value for number.', ['value' => '<code class="badge badge-secondary">N</code>']) !!}</small>
            </div>

            <div class="form-group">
                <label for="i-features-templates">{{ __('Templates') }}</label>
                <select name="features[templates]" id="i-features-templates" class="custom-select{{ $errors->has('features.templates') ? ' is-invalid' : '' }}">
                    @foreach([1 => __('On'), 0 => __('Off')] as $key => $value)
                        <option value="{{ $key }}" @if((old('features.templates') !== null && old('features.templates') == $key) || ($plan->features->templates == $key && old('features.templates') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('features.templates'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.templates') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-features-custom-templates">{{ __('Custom templates') }}</label>
                <select name="features[custom_templates]" id="i-features-custom-templates" class="custom-select{{ $errors->has('features.custom_templates') ? ' is-invalid' : '' }}">
                    @foreach([1 => __('On'), 0 => __('Off')] as $key => $value)
                        <option value="{{ $key }}" @if((old('features.custom_templates') !== null && old('features.custom_templates') == $key) || ($plan->features->custom_templates == $key && old('features.custom_templates') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('features.custom_templates'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.custom_templates') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-features-data-export">{{ __('Data export') }}</label>
                <select name="features[data_export]" id="i-features-data-export" class="custom-select{{ $errors->has('features.data_export') ? ' is-invalid' : '' }}">
                    @foreach([1 => __('On'), 0 => __('Off')] as $key => $value)
                        <option value="{{ $key }}" @if((old('features.data_export') !== null && old('features.data_export') == $key) || ($plan->features->data_export == $key && old('features.data_export') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('features.data_export'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.data_export') }}</strong>
                    </span>
                @endif
            </div>

            <div class="form-group">
                <label for="i-features-api">{{ __('API') }}</label>
                <select name="features[api]" id="i-features-api" class="custom-select{{ $errors->has('features.api') ? ' is-invalid' : '' }}">
                    @foreach([1 => __('On'), 0 => __('Off')] as $key => $value)
                        <option value="{{ $key }}" @if((old('features.api') !== null && old('features.api') == $key) || ($plan->features->api == $key && old('features.api') == null)) selected @endif>{{ $value }}</option>
                    @endforeach
                </select>
                @if ($errors->has('features.api'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('features.api') }}</strong>
                    </span>
                @endif
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>
    </div>
</div>
