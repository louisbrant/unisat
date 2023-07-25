@section('site_title', formatTitle([__('Update'), config('info.software.name')]))

<div class="card border-0 shadow-sm overflow-hidden">
    <div class="card-body p-5">
        <div class="h-100 d-flex flex-column justify-content-center align-items-center my-6">
            <div class="position-relative width-32 height-32 d-flex align-items-center justify-content-center">
                <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 rounded-circle"></div>

                @include('icons.update', ['class' => 'text-primary fill-current width-16 height-16'])

                <div class="position-absolute right-0 bottom-0 bg-secondary width-8 height-8 rounded-circle d-flex align-items-center justify-content-center">
                    @include('icons.more-horiz', ['class' => 'text-light fill-current width-4 height-4'])
                </div>
            </div>

            <div>
                <h5 class="mt-4 text-center">{{ __('Update') }}</h5>
                <p class="text-center text-muted mb-0">{!! __(':name update wizard.', ['name' => '<span class="font-weight-medium">'.(config('settings.title') ?? config('info.software.name')).'</span>']) !!}</p>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('update.overview') }}" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
    <span class="d-inline-flex align-items-center mx-auto">
        {{ __('Start') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
    </span>
</a>