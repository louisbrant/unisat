<div class="card border-0 shadow-sm mt-3">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Notes') }}</div>
            </div>
            <div class="col-auto d-flex align-items-center">
                <div class="badge badge-danger">{{ __('Expert level') }}</div>
            </div>
        </div>
    </div>

    <div class="card-body">
        {{ __('The API key should be sent as a Bearer token in the Authorization header of the request.') }} <a href="{{ route('account.api') }}">{{ __('Get your API key') }}</a>.
    </div>
</div>