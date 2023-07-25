@section('site_title', formatTitle([e($image->name), __('Image'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => request()->is('admin/*') ? route('admin.dashboard') : route('dashboard'), 'title' => request()->is('admin/*') ? __('Admin') : __('Home')],
    ['url' => request()->is('admin/*') ? route('admin.images') : route('images'), 'title' => __('Images')],
    ['title' => __('Image')],
]])

<div class="d-flex align-items-end mb-3">
    <h1 class="h2 mb-0 flex-grow-1 text-truncate">{{ __('Image') }}</h1>

    <div class="d-flex align-items-center flex-grow-0">
        <div class="form-row flex-nowrap">
            <div class="col">
                <a href="{{ $image->url }}" class="btn d-flex align-items-center" download="{{ $image->name }}" data-tooltip="true" title="{{ __('Download') }}">
                    @include('icons.export', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                </a>
            </div>
            <div class="col">
                <a href="#" class="btn text-secondary d-flex align-items-center" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">@include('icons.more-horiz', ['class' => 'fill-current width-4 height-4'])&#8203;</a>

                @include('images.partials.menu')
            </div>
        </div>
    </div>
</div>

<div class="row m-n2">
    <div class="col-12 p-2">
        <div class="card border-0 rounded-top shadow-sm overflow-hidden">
            <div class="px-3 border-bottom">
                <div class="row">
                    <!-- Title -->
                    <div class="col-auto d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'border-left' : 'border-right') }}">
                        <div class="px-2 py-4 d-flex">
                            <div class="d-flex position-relative width-10 height-10 align-items-center justify-content-center flex-shrink-0 text-primary">
                                <div class="position-absolute opacity-10 top-0 right-0 bottom-0 left-0 border-radius-xl bg-primary"></div>

                                @include('icons.image', ['class' => 'fill-current width-5 height-5'])
                            </div>
                        </div>
                    </div>

                    <div class="col">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-12 col-md-6 col-xl-2 {{ (__('lang_dir') == 'rtl' ? 'border-left-md' : 'border-right-md')  }}">
                                <div class="px-2 py-4">
                                    <div class="text-muted mb-1">
                                        {{ __('Name') }}
                                    </div>
                                    <div class="h6 font-weight-bold mb-0 text-truncate">
                                        <span class="d-flex align-items-center text-truncate">
                                            <div class="text-truncate" data-tooltip="true" title="{{ $image->name }}">{{ $image->name }}</div>

                                            @if($image->favorite) <div class="d-flex flex-shrink-0 width-4 height-4 text-warning {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Favorite') }}">@include('icons.star', ['class' => 'fill-current width-4 height-4 flex-shrink-0'])</div> @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Style -->
                            <div class="col-12 d-none d-xl-flex col-xl-2 {{ (__('lang_dir') == 'rtl' ? 'border-left-md' : 'border-right-md')  }}">
                                <div class="px-2 py-4">
                                    <div class="text-muted mb-1">
                                        {{ __('Style') }}
                                    </div>

                                    <div class="d-flex align-items-center h6 font-weight-bold mb-0 text-truncate">
                                        <span class="text-truncate" data-tooltip="true" title="{{ __(config('images.styles')[$image->style] ?? 'None') }}">
                                            {{ __(config('images.styles')[$image->style] ?? 'None') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Medium -->
                            <div class="col-12 d-none d-xl-flex col-xl-2 {{ (__('lang_dir') == 'rtl' ? 'border-left-md' : 'border-right-md')  }}">
                                <div class="px-2 py-4">
                                    <div class="text-muted mb-1">
                                        {{ __('Medium') }}
                                    </div>

                                    <div class="d-flex align-items-center h6 font-weight-bold mb-0 text-truncate">
                                        <span class="text-truncate" data-tooltip="true" title="{{ __(config('images.mediums')[$image->medium] ?? 'None') }}">
                                            {{ __(config('images.mediums')[$image->medium] ?? 'None') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter -->
                            <div class="col-12 d-none d-xl-flex col-xl-2 {{ (__('lang_dir') == 'rtl' ? 'border-left-md' : 'border-right-md')  }}">
                                <div class="px-2 py-4">
                                    <div class="text-muted mb-1">
                                        {{ __('Filter') }}
                                    </div>

                                    <div class="d-flex align-items-center h6 font-weight-bold mb-0 text-truncate">
                                        <span class="text-truncate" data-tooltip="true" title="{{ __(config('images.filters')[$image->filter] ?? 'None') }}">
                                            {{ __(config('images.filters')[$image->filter] ?? 'None') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Resolution -->
                            <div class="col-12 d-none d-xl-flex col-xl-2 {{ (__('lang_dir') == 'rtl' ? 'border-left-md' : 'border-right-md')  }}">
                                <div class="px-2 py-4">
                                    <div class="text-muted mb-1">
                                        <div class="d-flex text-truncate">
                                            <div class="text-truncate">{{ __('Resolution') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center h6 font-weight-bold mb-0 text-truncate">
                                        <span class="text-truncate" data-tooltip="true" title="{{ config('images.resolutions')[$image->resolution] }}">
                                            {{ config('images.resolutions')[$image->resolution] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Created at -->
                            <div class="col-12 d-none d-md-flex col-md-6 col-xl-2">
                                <div class="px-2 py-4">
                                    <div class="text-muted mb-1">
                                        {{ __('Created at') }}
                                    </div>
                                    <div class="d-flex align-items-center h6 font-weight-bold mb-0 text-truncate">
                                        <div class="text-truncate" data-tooltip="true" title="{{ $image->created_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d') . ' H:i:s') }}">{{ $image->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <img src="{{ $image->url }}" class="w-100">
            </div>
        </div>
    </div>
</div>
