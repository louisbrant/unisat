@section('site_title', formatTitle([__('Account'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['title' => __('Account')]
]])

<h1 class="h2 mb-0 d-inline-block">{{ __('Account') }}</h1>

@php
    $settings[] = [
        'icon' => 'portrait',
        'title' => __('Profile'),
        'description' => __('Update your profile information'),
        'route' => route('account.profile')
    ];
    $settings[] = [
        'icon' => 'lock',
        'title' => __('Security'),
        'description' => __('Change your security information'),
        'route' => route('account.security')
    ];
    $settings[] = [
        'icon' => 'tune',
        'title' => __('Preferences'),
        'description' => __('Change your preferences'),
        'route' => route('account.preferences')
    ];
    $settings[] = [
        'icon' => 'package',
        'title' => __('Plan'),
        'description' => __('View your plan details'),
        'route' => route('account.plan')
    ];
    if(paymentProcessors()) {
        $settings[] = [
            'icon' => 'credit-card',
            'title' => __('Payments'),
            'description' => __('View your payments and invoices'),
            'route' => route('account.payments')
        ];
    }
    $settings[] = [
        'icon' => 'code',
        'title' => __('API'),
        'description' => __('View and change your developer key'),
        'route' => route('account.api')
    ];
    $settings[] = [
        'icon' => 'delete',
        'title' => __('Delete'),
        'description' => __('Delete your account and associated data'),
        'route' => route('account.delete')
    ];
@endphp

<div class="row">
    @foreach($settings as $setting)
        <div class="col-12 mt-3">
            <div class="card border-0 h-100 shadow-sm">
                <div class="card-body d-flex align-items-center text-truncate">
                    <div class="d-flex position-relative text-primary width-8 height-8 align-items-center justify-content-center flex-shrink-0">
                        <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-lg"></div>
                        @include('icons.' . $setting['icon'], ['class' => 'fill-current width-4 height-4'])
                    </div>

                    <a href="{{ $setting['route'] }}" class="text-dark font-weight-medium stretched-link text-decoration-none text-truncate mx-3">{{ $setting['title'] }}</a>

                    <div class="text-muted d-flex align-items-center text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}">
                        <span class="text-truncate d-none d-md-inline-block">{{ $setting['description'] }}</span>

                        @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'flex-shrink-0 width-3 height-3 fill-current mx-2'])
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
