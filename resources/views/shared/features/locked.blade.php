<div class="h-100 d-flex flex-column justify-content-center align-items-center">
    <div class="position-relative width-32 height-32 d-flex align-items-center justify-content-center">
        <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 rounded-circle"></div>

        @include('icons.lock', ['class' => 'text-primary fill-current width-16 height-16'])
    </div>

    <div>
        <h5 class="mt-4 text-center">{{ __('Feature locked') }}</h5>
        <p class="text-center text-muted">{{ __('Upgrade your account to unlock this feature.') }}</p>

        <div class="text-center mt-5">
            <a href="{{ route('pricing') }}" class="btn btn-primary">{{ __('Upgrade') }}</a>
        </div>
    </div>
</div>
