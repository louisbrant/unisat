@extends('layouts.app')

@section('site_title', formatTitle([__('Dashboard'), config('settings.title')]))

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="bg-base-0">
        <div class="container py-5">
            <div class="d-flex">
                <div class="row no-gutters w-100">
                    <div class="d-flex col-12 col-md">
                        <div class="flex-shrink-1">
                            <a href="{{ route('account') }}" class="d-block"><img src="{{asset('img/icons/bigcoin.png')}}" class="rounded-circle width-16 height-16"></a>
                        </div>
                        <div class="flex-grow-1 d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'mr-3' : 'ml-3') }}">
                            <div>
                                <h4 class="font-weight-medium mb-0">{{ Auth::user()->name }}</h4>

                                <div class="d-flex flex-wrap">
                                    @if(paymentProcessors())
                                        <div class="d-inline-block mt-2 {{ (__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4') }}">
                                            <div class="d-flex">
                                                <div class="d-inline-flex align-items-center">
                                                    @include('icons.package', ['class' => 'text-muted fill-current width-4 height-4'])
                                                </div>

                                                <div class="d-inline-block {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">
                                                    <a href="{{ route('account.plan') }}" class="text-dark text-decoration-none">{{ Auth::user()->plan->name }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-inline-block mt-2 {{ (__('lang_dir') == 'rtl' ? 'ml-4' : 'mr-4') }}">
                                            <div class="d-flex">
                                                <div class="d-inline-flex align-items-center">
                                                    @include('icons.package', ['class' => 'text-muted fill-current width-4 height-4'])
                                                </div>

                                                <div class="d-inline-block {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}">
                                                    {{ 'Pro Mode' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(paymentProcessors())
                        @if(Auth::user()->planIsDefault())
                            <div class="col-12 col-md-auto d-flex flex-row-reverse align-items-center">
                                <a href="{{ route('pricing') }}" class="btn btn-outline-primary btn-block d-flex justify-content-center align-items-center mt-3 mt-md-0 {{ (__('lang_dir') == 'rtl' ? 'ml-md-3' : 'mr-md-3') }}">@include('icons.unarchive', ['class' => 'width-4 height-4 fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')]){{ __('Upgrade') }}</a>
                            </div>
                        @else
                            <div class="col-12 col-md-auto d-flex flex-row-reverse align-items-center">
                                <a href="{{ route('pricing') }}" class="btn btn-outline-primary btn-block d-flex justify-content-center align-items-center mt-3 mt-md-0 {{ (__('lang_dir') == 'rtl' ? 'ml-md-3' : 'mr-md-3') }}">@include('icons.package', ['class' => 'width-4 height-4 fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')]){{ __('Plans') }}</a>
                            </div>
                        @endif
                    @endif

                    <div class="col-12 col-md-auto d-flex flex-row-reverse align-items-center">
                        <a href="{{ route('documents.new') }}" class="btn btn-primary btn-block d-flex justify-content-center align-items-center mt-3 mt-md-0">@include('icons.add', ['class' => 'width-4 height-4 fill-current '.(__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')]){{ __('New document') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-1">
        <div class="container py-3 my-3">
            <div class="row mb-3">
                <div class="col-12 col-lg">
                    <h4 class="mb-0">{{ __('Overview') }}</h4>
                </div>
                <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                    <ul class="nav nav-pills small">
                        <li class="nav-item">
                            <a href="{{ route('dashboard', ['overview' => 'month']) }}" class="nav-link py-1 px-2 {{ request()->input('overview') == 'month' ? 'active' : '' }}" href="#">{{ __('This month') }}</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('dashboard', ['overview' => 'total']) }}" class="nav-link py-1 px-2 {{ request()->input('overview') != 'month' ? 'active' : '' }}" href="#">{{ __('All time') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="row m-n2">
                <div class="col-12 col-md p-2">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="d-flex flex-column justify-content-center flex-grow-1">
                                <div class="text-muted mb-1">
                                    {{ __('Documents') }}
                                </div>

                                <div class="font-weight-bold h4 mb-0">
                                    {{ number_format((request()->input('overview') == 'month' ? Auth::user()->documents_month_count : Auth::user()->documents_total_count), 0, __('.'), __(',')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md p-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex flex-column justify-content-center flex-grow-1">
                                    <div class="text-muted mb-1">
                                        {{ __('Images') }}
                                    </div>

                                    <div class="font-weight-bold h4 mb-0">
                                        {{ number_format((request()->input('overview') == 'month' ? Auth::user()->images_month_count : Auth::user()->images_total_count), 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md p-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex flex-column justify-content-center flex-grow-1">
                                    <div class="text-muted mb-1">
                                        {{ __('Chats') }}
                                    </div>

                                    <div class="font-weight-bold h4 mb-0">
                                        {{ number_format((request()->input('overview') == 'month' ? Auth::user()->chats_month_count : Auth::user()->chats_total_count), 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md p-2">
                    <div class="card border-0 shadow-sm">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex flex-column justify-content-center flex-grow-1">
                                    <div class="text-muted mb-1">
                                        {{ __('Transcriptions') }}
                                    </div>

                                    <div class="font-weight-bold h4 mb-0">
                                        {{ number_format((request()->input('overview') == 'month' ? Auth::user()->transcriptions_month_count : Auth::user()->transcriptions_total_count), 0, __('.'), __(',')) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex mb-3 mt-5">
                <div class="flex-grow-1">
                    <h4 class="mb-0 d-inline-block">{{ __('Templates') }}</h4>
                </div>
                <div>
                    <a href="{{ route('templates') }}" class="btn btn-sm btn-outline-primary d-flex justify-content-center align-items-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'fill-current width-3 height-3 ml-1'])</a>
                </div>
            </div>

            <div class="row m-n2" id="templates">
                @foreach($templates as $template)
                    <div class="col-12 col-md-6 col-lg-4 p-2" data-template-title="{{ __($template->name) }}">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex align-items-center text-truncate">
                                <div class="d-flex position-relative text-{{ categoryColor($template->category_id) }} width-8 height-8 align-items-center justify-content-center flex-shrink-0">
                                    <div class="position-absolute bg-{{ categoryColor($template->category_id) }} opacity-10 top-0 right-0 bottom-0 left-0 border-radius-lg"></div>
                                    @if($template->isCustom())
                                        <span class="user-select-none">{{ $template->icon }}</span>
                                    @else
                                        @include('icons.' . $template->icon, ['class' => 'fill-current width-4 height-4'])
                                    @endif
                                </div>

                                <a href="{{ $template->url }}" class="text-dark font-weight-medium stretched-link text-decoration-none text-truncate mx-3">{{ __($template->name) }}</a>

                                <div class="text-muted d-flex align-items-center text-truncate {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}">
                                    @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'flex-shrink-0 width-3 height-3 fill-current mx-2'])
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <h4 class="mb-3 mt-5">{{ __('Activity') }}</h4>

            <div class="row m-n2">
                <div class="col-12 col-lg-6 p-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header align-items-center">
                            <div class="row">
                                <div class="col"><div class="font-weight-medium py-1">{{ __('Recent documents') }}</div></div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if(count($recentDocuments) == 0)
                                {{ __('No data') }}.
                            @else
                                <div class="list-group list-group-flush my-n3">
                                    @foreach($recentDocuments as $document)
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

                                                                    <div class="d-flex align-items-center text-truncate">
                                                                        <a href="{{ route('documents.show', $document->id) }}" class="text-truncate">{{ $document->name }}</a>

                                                                        @if($document->favorite) <div class="d-flex flex-shrink-0 width-4 height-4 text-warning {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Favorite') }}">@include('icons.star', ['class' => 'fill-current width-4 height-4 flex-shrink-0'])</div> @endif
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

                        @if(count($recentDocuments) > 0)
                            <div class="card-footer bg-base-2 border-0">
                                <a href="{{ route('documents') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-12 col-lg-6 p-2">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header align-items-center">
                            <div class="row">
                                <div class="col"><div class="font-weight-medium py-1">{{ __('Recent images') }}</div></div>
                            </div>
                        </div>

                        <div class="card-body">
                            @if(count($recentImages) == 0)
                                {{ __('No data') }}.
                            @else
                                <div class="list-group list-group-flush my-n3">
                                    @foreach($recentImages as $image)
                                        <div class="list-group-item px-0">
                                            <div class="row align-items-center">
                                                <div class="col text-truncate">
                                                    <div class="row align-items-center">
                                                        <div class="col-12 d-flex text-truncate">
                                                            <div class="text-truncate">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                                                        <img src="{{ $image->url }}" class="width-4 height-4 rounded">
                                                                    </div>

                                                                    <div class="d-flex align-items-center text-truncate">
                                                                        <a href="{{ route('images.show', $image->id) }}" class="text-truncate">{{ $image->name }}</a>

                                                                        @if($image->favorite) <div class="d-flex flex-shrink-0 width-4 height-4 text-warning {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Favorite') }}">@include('icons.star', ['class' => 'fill-current width-4 height-4 flex-shrink-0'])</div> @endif
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="width-4 flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}"></div>
                                                                    <div class="text-muted text-truncate small cursor-help" data-tooltip="true" title="{{ __('Resolution') }}">
                                                                        {{ config('images.resolutions')[$image->resolution] }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="form-row">
                                                        <div class="col">
                                                            @include('images.partials.menu')
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        @if(count($recentImages) > 0)
                            <div class="card-footer bg-base-2 border-0">
                                <a href="{{ route('images') }}" class="text-muted font-weight-medium d-flex align-items-center justify-content-center">{{ __('View all') }} @include((__('lang_dir') == 'rtl' ? 'icons.chevron-left' : 'icons.chevron-right'), ['class' => 'width-3 height-3 fill-current '.(__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2')])</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <h4 class="mb-3 mt-5">{{ __('More') }}</h4>

            <div class="row m-n2">
                <div class="col-12 col-xl-4 p-2">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-body d-flex">
                            <div class="d-flex position-relative text-primary width-12 height-12 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                @include('icons.image', ['class' => 'fill-current width-6 height-6'])
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <a href="{{ route('images.new') }}" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Image') }}</a>

                                <div class="text-muted">
                                    {{ __('Generate a new image.') }}
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
                                @include('icons.chat', ['class' => 'fill-current width-6 height-6'])
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <a href="{{ route('chats.new') }}" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Chat') }}</a>

                                <div class="text-muted">
                                    {{ __('Start a new chat.') }}
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
                                @include('icons.headphones', ['class' => 'fill-current width-6 height-6'])
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                                <a href="{{ route('transcriptions.new') }}" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ __('Transcription') }}</a>

                                <div class="text-muted">
                                    {{ __('Create a new transcription.') }}
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

@include('shared.sidebars.user')
