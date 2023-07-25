@if(isset($user))
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header">
            <div class="row">
                <div class="col">
                    <div class="font-weight-medium py-1">{{ __('User') }}</div>
                </div>
                <div class="col-auto">
                    <div class="form-row">
                        <div class="col">
                            @include('admin.users.partials.menu')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body mb-n3">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted">{{ __('Name') }}</div>
                    <div>{{ $user->name }}</div>
                </div>

                <div class="col-12 col-lg-6 mb-3">
                    <div class="text-muted">{{ __('Email') }}</div>
                    <div>{{ $user->email }}</div>
                </div>
            </div>
        </div>
    </div>
@endif