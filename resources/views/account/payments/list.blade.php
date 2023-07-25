@section('site_title', formatTitle([__('Payments'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('account'), 'title' => __('Account')],
    ['title' => __('Payments')]
]])

<div class="d-flex">
    <div class="flex-grow-1">
        <h1 class="h2 mb-3 d-inline-block">{{ __('Payments') }}</h1>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col"><div class="font-weight-medium py-1">{{ __('Payments') }}</div></div>
            <div class="col-auto">
                <form method="GET" action="{{ route('account.payments') }}" class="d-md-flex">
                    <div class="input-group input-group-sm">
                        <input class="form-control" name="search" placeholder="{{ __('Search') }}" value="{{ app('request')->input('search') }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-primary d-flex align-items-center dropdown-toggle dropdown-toggle-split reset-after" data-tooltip="true" title="{{ __('Filters') }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.filter', ['class' => 'fill-current width-4 height-4'])&#8203;</button>
                            <div class="dropdown-menu {{ (__('lang_dir') == 'rtl' ? 'dropdown-menu' : 'dropdown-menu-right') }} border-0 shadow width-64 p-0" id="search-filters">
                                <div class="dropdown-header py-3">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium m-0 text-dark">{{ __('Filters') }}</div></div>
                                        <div class="col-auto"><a href="{{ route('account.payments') }}" class="text-secondary">{{ __('Reset') }}</a></div>
                                    </div>
                                </div>

                                <div class="dropdown-divider my-0"></div>

                                <div class="max-height-96 overflow-auto pt-3">
                                    <div class="form-group px-4">
                                        <label for="i-search-by" class="small">{{ __('Search by') }}</label>
                                        <select name="search_by" id="i-search-by" class="custom-select custom-select-sm">
                                            @foreach(['payment_id' => __('Payment ID'), 'invoice_id' => __('Invoice ID')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('search_by') == $key || !request()->input('search_by') && $key == 'name') selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-plan" class="small">{{ __('Plan') }}</label>
                                        <select id="i-plan" name="plan" class="custom-select custom-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach($plans as $plan)
                                                <option value="{{ $plan->id }}" @if(request()->input('plan') == $plan->id) selected @endif>{{ $plan->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-interval" class="small">{{ __('Interval') }}</label>
                                        <select name="interval" id="i-interval" class="custom-select custom-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach(['month' => __('Monthly'), 'year' => __('Yearly')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('interval') == $key && request()->input('interval') !== null) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-processor" class="small">{{ __('Processor') }}</label>
                                        <select name="processor" id="i-processor" class="custom-select custom-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach(config('payment.processors') as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('processor') == $key && request()->input('processor') !== null) selected @endif>{{ __($value['name']) }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-status" class="small">{{ __('Status') }}</label>
                                        <select name="status" id="i-status" class="custom-select custom-select-sm">
                                            <option value="">{{ __('All') }}</option>
                                            @foreach(['completed' => __('Completed'), 'pending' => __('Pending'), 'cancelled' => __('Cancelled')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('status') == $key && request()->input('status') !== null) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-sort-by" class="small">{{ __('Sort by') }}</label>
                                        <select name="sort_by" id="i-sort-by" class="custom-select custom-select-sm">
                                            @foreach(['id' => __('Date created')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('sort_by') == $key) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-sort" class="small">{{ __('Sort') }}</label>
                                        <select name="sort" id="i-sort" class="custom-select custom-select-sm">
                                            @foreach(['desc' => __('Descending'), 'asc' => __('Ascending')] as $key => $value)
                                                <option value="{{ $key }}" @if(request()->input('sort') == $key) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group px-4">
                                        <label for="i-per-page" class="small">{{ __('Results per page') }}</label>
                                        <select name="per_page" id="i-per-page" class="custom-select custom-select-sm">
                                            @foreach([10, 25, 50, 100] as $value)
                                                <option value="{{ $value }}" @if(request()->input('per_page') == $value || request()->input('per_page') == null && $value == config('settings.paginate')) selected @endif>{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="dropdown-divider my-0"></div>

                                <div class="px-4 py-3">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">{{ __('Search') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        @if(count($payments) == 0)
            {{ __('No results found.') }}
        @else
            <div class="list-group list-group-flush my-n3">
                <div class="list-group-item px-0 text-muted">
                    <div class="row align-items-center">
                        <div class="col">
                            <div class="row align-items-center">
                                <div class="col-12 col-lg-8 d-flex">
                                    {{ __('Amount') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2">
                                    {{ __('Status') }}
                                </div>

                                <div class="d-none d-lg-block col-lg-2">
                                    {{ __('Created at') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-row">
                                <div class="col">
                                    <div class="invisible btn d-flex align-items-center btn-sm text-primary">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach($payments as $payment)
                    <div class="list-group-item px-0">
                        <div class="row align-items-center">
                            <div class="col text-truncate">
                                <div class="row align-items-center text-truncate">
                                    <div class="col-12 col-lg-8 d-flex">
                                        <div class="text-truncate">
                                            <div class="d-flex align-items-center">
                                                <img src="{{ asset('img/icons/payments/' . $payment->processor . '.svg') }}" class="width-6 rounded-sm">

                                                <div class="text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-3 ml-2' : 'ml-3 mr-2') }}">
                                                    <a href="{{ route('account.payments.edit', $payment->id) }}">{{ formatMoney($payment->amount, $payment->currency) }}</a> <span class="text-muted">{{ $payment->currency }}</span>
                                                </div>

                                                @if($payment->status == 'completed')
                                                    <a href="{{ route('account.invoices.show', $payment->id) }}" class="badge badge-secondary text-truncate" data-tooltip="true" title="{{ __('Invoice') }}">{{ $payment->invoice_id }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-none d-lg-flex col-lg-2 align-items-center">
                                        @if($payment->status == 'completed')
                                            <span class="badge badge-success text-truncate">{{ __('Completed') }}</span>
                                        @elseif($payment->status == 'pending')
                                            <span class="badge badge-secondary text-truncate">{{ __('Pending') }}</span>
                                        @else
                                            <span class="badge badge-danger text-truncate">{{ __('Cancelled') }}</span>
                                        @endif
                                    </div>

                                    <div class="d-none d-lg-block col-lg-2 text-truncate">
                                        <span class="text-truncate" data-tooltip="true" title="{{ $payment->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d') . ' H:i:s') }}">{{ $payment->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-row">
                                    <div class="col">
                                        @include('account.payments.partials.menu')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-3 align-items-center">
                    <div class="row">
                        <div class="col">
                            <div class="mt-2 mb-3">{{ __('Showing :from-:to of :total', ['from' => $payments->firstItem(), 'to' => $payments->lastItem(), 'total' => $payments->total()]) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            {{ $payments->onEachSide(1)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
