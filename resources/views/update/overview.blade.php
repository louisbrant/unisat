@section('site_title', formatTitle([__('Update'), config('info.software.name')]))

<form action="{{ route('update.overview') }}" method="post">
    @csrf

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="card-body text-center py-5">
            @include('shared.message')

            <div class="my-6">
                <p class="text-muted font-size-lg">{{ __('Updates pending') }}</p>

                <div class="h1">{{ $updates }}</div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-block btn-primary d-inline-flex align-items-center mt-3 py-2">
        <span class="d-inline-flex align-items-center mx-auto">
            {{ __('Next') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])
        </span>
    </button>
</form>