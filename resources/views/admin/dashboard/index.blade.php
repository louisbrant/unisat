@extends('layouts.app')

@section('site_title', formatTitle([__('Dashboard'), config('settings.title')]))

@section('content')
    <div class="bg-base-1 flex-fill">
        <div class="bg-base-0">
            <div class="container py-5">
                <div class="d-flex">
                    <div class="row no-gutters w-100">
                        <div class="d-flex col-12 col-md">
                            <div class="flex-grow-1 d-flex align-items-center">
                                <div>
                                    <h1 class="h2 font-weight-medium mb-0">{{ config('settings.title') }}</h1>

                                    <div class="d-flex flex-wrap">
                                        <div class="d-inline-block mt-2 {{ (__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4') }}">
                                            <div class="d-flex">
                                                <div class="d-inline-flex align-items-center">
                                                    @include('icons.info', ['class' => 'text-muted fill-current width-4 height-4'])
                                                </div>

                                                <div class="d-inline-block {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">
                                                    <a href="{{ config('info.software.url') }}/{{ mb_strtolower(config('info.software.name')) }}/changelog" class="text-dark text-decoration-none d-flex align-items-center" target="_blank">{{ __('Version') }} <span class="badge badge-primary {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">{{ config('info.software.version') }}</span></a>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-inline-block mt-2 {{ (__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4') }}">
                                            <div class="d-flex">
                                                <div class="d-inline-flex align-items-center">
                                                    @include('icons.vpn-key', ['class' => 'text-muted fill-current width-4 height-4'])
                                                </div>

                                                <div class="d-inline-block {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">
                                                    <a href="{{ route('admin.settings', 'license') }}" class="text-dark text-decoration-none d-flex align-items-center">{{ __('License') }} <span class="badge badge-primary {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">{{ config('settings.license_type') ? 'Extended' : 'Regular' }}</span></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-auto d-flex flex-row-reverse align-items-center"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-base-1">
            <div class="container py-3 my-3">
                <h4 class="mb-3">{{ __('Overview') }}</h4>

                <div class="row m-n2">
                    @php
                        $cards = [
                            'users' =>
                            [
                                'title' => 'Users',
                                'value' => $stats['users'],
                                'route' => 'admin.users',
                                'icon' => 'people-alt'
                            ],
                            [
                                'title' => 'Plans',
                                'value' => $stats['plans'],
                                'route' => 'admin.plans',
                                'icon' => 'package'
                            ],
                            [
                                'title' => 'Payments',
                                'value' => $stats['payments'],
                                'route' => 'admin.payments',
                                'icon' => 'credit-card'
                            ],
                            [
                                'title' => 'Pages',
                                'value' => $stats['pages'],
                                'route' => 'admin.pages',
                                'icon' => 'menu-book'
                            ],
                            [
                                'title' => 'Documents',
                                'value' => $stats['documents'],
                                'route' => 'admin.documents',
                                'icon' => 'document'
                            ],
                            [
                                'title' => 'Images',
                                'value' => $stats['images'],
                                'route' => 'admin.images',
                                'icon' => 'image'
                            ],
                            [
                                'title' => 'Chats',
                                'value' => $stats['chats'],
                                'route' => 'admin.chats',
                                'icon' => 'chat'
                            ],
                            [
                                'title' => 'Transcriptions',
                                'value' => $stats['transcriptions'],
                                'route' => 'admin.transcriptions',
                                'icon' => 'headphones'
                            ]
                        ];
                    @endphp

                    @foreach($cards as $card)
                        <div class="col-12 col-md-6 col-xl-3 p-2">
                            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                                <div class="card-body d-flex">
                                    <div class="d-flex position-relative text-primary width-10 height-10 align-items-center justify-content-center flex-shrink-0">
                                        <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-xl"></div>
                                        @include('icons.' . $card['icon'], ['class' => 'fill-current width-5 height-5'])
                                    </div>

                                    <div class="flex-grow-1"></div>

                                    <div class="d-flex align-items-center h2 font-weight-bold mb-0 text-truncate">
                                        {{ number_format($card['value'], 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                                <div class="card-footer bg-base-2 border-0">
                                    <a href="{{ route($card['route']) }}" class="text-muted font-weight-medium d-inline-flex align-items-baseline">{{ __($card['title']) }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <h4 class="mb-3 mt-5">{{ __('Activity') }}</h4>

                <div class="row m-n2">
                    <div class="col-12 col-xl-6 p-2">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header align-items-center">
                                <div class="row">
                                    <div class="col"><div class="font-weight-medium py-1">{{ __('Latest users') }}</div></div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if(count($users) == 0)
                                    {{ __('No data') }}.
                                @else
                                    <div class="list-group list-group-flush my-n3">
                                        @foreach($users as $user)
                                            <div class="list-group-item px-0">
                                                <div class="row align-items-center">
                                                    <div class="col text-truncate">
                                                        <div class="text-truncate">
                                                            <div class="d-flex align-items-center">
                                                                <img src="{{ gravatar($user->email, 48) }}" class="rounded-circle width-4 height-4 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">

                                                                <div class="text-truncate">
                                                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="text-truncate">{{ $user->name }}</a>
                                                                </div>
                                                            </div>
                                                            <div class="d-flex align-items-center">
                                                                <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                                <div class="text-muted text-truncate small">
                                                                    {{ $user->email }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-auto">
                                                        <div class="form-row">
                                                            <div class="col">
                                                                @include('admin.users.partials.menu', ['user' => $user])
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            @if(count($users) > 0)
                                <div class="card-footer bg-base-2 border-0">
                                    <a href="{{ route('admin.users') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(paymentProcessors())
                        <div class="col-12 col-xl-6 p-2">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header align-items-center">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium py-1">{{ __('Latest payments') }}</div></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(count($payments) == 0)
                                        {{ __('No data') }}.
                                    @else
                                        <div class="list-group list-group-flush my-n3">
                                            @foreach($payments as $payment)
                                                <div class="list-group-item px-0">
                                                    <div class="row align-items-center">
                                                        <div class="col text-truncate">
                                                            <div class="text-truncate">
                                                                <div class="d-flex align-items-center">
                                                                    <img src="{{ asset('img/icons/payments/' . $payment->processor . '.svg') }}" class="width-4 rounded-sm {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">

                                                                    <div class="text-truncate d-flex align-items-center">
                                                                        <div class="text-truncate {{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">
                                                                            <a href="{{ route('admin.payments.edit', $payment->id) }}">{{ formatMoney($payment->amount, $payment->currency) }}</a> <span class="text-muted">{{ $payment->currency }}</span>
                                                                        </div>

                                                                        @if($payment->status == 'completed')
                                                                            <span class="badge badge-success text-truncate">{{ __('Completed') }}</span>
                                                                        @elseif($payment->status == 'pending')
                                                                            <span class="badge badge-secondary text-truncate">{{ __('Pending') }}</span>
                                                                        @else
                                                                            <span class="badge badge-danger text-truncate">{{ __('Cancelled') }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                                    <div class="text-muted text-truncate small">
                                                                        {{ $payment->plan->name }}
                                                                    </div>
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
                                        </div>
                                    @endif
                                </div>

                                @if(count($payments) > 0)
                                    <div class="card-footer bg-base-2 border-0">
                                        <a href="{{ route('admin.payments') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="col-12 col-xl-6 p-2">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-header align-items-center">
                                    <div class="row">
                                        <div class="col"><div class="font-weight-medium py-1">{{ __('Latest documents') }}</div></div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    @if(count($documents) == 0)
                                        {{ __('No data') }}.
                                    @else
                                        <div class="list-group list-group-flush my-n3">
                                            @foreach($documents as $document)
                                                <div class="list-group-item px-0">
                                                    <div class="row align-items-center">
                                                        <div class="col text-truncate">
                                                            <div class="row align-items-center">
                                                                <div class="col-12 d-flex text-truncate">
                                                                    <div class="text-truncate">
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}" data-tooltip="true" title="{{ __($document->template->name) }}">
                                                                                @if($document->template->isCustom())
                                                                                    <span class="width-4 height-4">
                                                                                        <span class="position-absolute width-4 height-4 d-flex align-items-center justify-content-center user-select-none">{{ $document->template->icon }}</span>
                                                                                    </span>
                                                                                @else
                                                                                    @include('icons.' . $document->template->icon, ['class' => 'fill-current width-4 height-4 text-' . categoryColor($document->template->category_id)])
                                                                                @endif
                                                                            </div>

                                                                            <div class="text-truncate">
                                                                                <a href="{{ route('admin.documents.edit', $document->id) }}">{{ $document->name }}</a>
                                                                            </div>
                                                                        </div>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                                            <div class="text-muted text-truncate small cursor-help" data-tooltip="true" title="{{ $document->url }}">
                                                                                {{ Str::substr(strip_tags($document->result) ?? __('No data') . '.', 0, 160) }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <div class="form-row">
                                                                <div class="col">
                                                                    @include('documents.partials.menu')
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                @if(count($documents) > 0)
                                    <div class="card-footer bg-base-2 border-0">
                                        <a href="{{ route('admin.documents') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <h4 class="mb-3 mt-5">{{ __('More') }}</h4>

                <div class="row m-n2">
                    <div class="col-12 col-xl-4 p-2">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex position-relative text-primary width-12 height-12 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                    <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                    @include('icons.website', ['class' => 'fill-current width-6 height-6'])
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="{{ config('info.software.url') }}/{{ mb_strtolower(config('info.software.name')) }}" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Website') }}</a>

                                    <div class="text-muted">
                                        {{ __('Visit the official website.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4 p-2">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex position-relative text-primary width-12 height-12 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                    <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                    @include('icons.book', ['class' => 'fill-current width-6 height-6'])
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="{{ config('info.software.url') }}/{{ mb_strtolower(config('info.software.name')) }}/documentation" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Documentation') }}</a>

                                    <div class="text-muted">
                                        {{ __('Read the documentation.') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl-4 p-2">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex position-relative text-primary width-12 height-12 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                    <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                    @include('icons.history', ['class' => 'fill-current width-6 height-6'])
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="{{ config('info.software.url') }}/{{ mb_strtolower(config('info.software.name')) }}/changelog" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Changelog') }}</a>

                                    <div class="text-muted">
                                        {{ __('See what\'s new.') }}
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
@include('admin.sidebar')
