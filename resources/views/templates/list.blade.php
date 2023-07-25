@extends('layouts.app')

@section('site_title', formatTitle([__('Templates'), config('settings.title')]))

@section('head_content')

@endsection

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="container py-3 my-3">
        <div class="row">
            <div class="col-12">
                @include('shared.breadcrumbs', ['breadcrumbs' => [
                    ['url' => route('dashboard'), 'title' => __('Home')],
                    ['title' => __('Templates')],
                ]])

                <div class="d-flex">
                    <div class="flex-grow-1">
                        <h1 class="h2 mb-3 d-inline-block">{{ __('Templates') }}</h1>
                    </div>
                    <div>
                        <a href="{{ route('templates.new') }}" class="btn btn-primary mb-3">{{ __('New') }}</a>
                    </div>
                </div>

                <div class="row no-gutters bg-base-0 rounded shadow-sm mb-3 overflow-hidden" id="template-filters">
                    <div class="col-12">
                        <div class="border-bottom px-3 py-3">
                            @include('shared.message')

                            <form enctype="multipart/form-data" autocomplete="off" id="form-templates-search" onsubmit="event.preventDefault();" class="{{ (__('lang_dir') == 'rtl' ? 'ml-1' : 'mr-1') }}">
                                @csrf

                                <div class="input-group input-group-lg">
                                    <input type="text" name="search" class="form-control font-size-lg" autocapitalize="none" spellcheck="false" id="i-search" placeholder="{{ __('Search') }}" autofocus>
                                </div>

                                <div class="input-group-append border-left-0 d-none" id="clear-button-container">
                                    <button type="button" class="btn text-secondary bg-transparent input-group-text d-flex align-items-center" id="b-clear">
                                        @include('icons.close', ['class' => 'fill-current width-4 height-4'])
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="col">
                        <div class="d-flex flex-column flex-lg-row justify-content-around w-100">
                            <a href="#" class="text-truncate text-decoration-none text-primary d-flex align-items-center justify-content-lg-center font-weight-medium py-3 px-3 cursor-pointer w-100" data-filter-category="" data-text-color-active="text-primary" data-text-color-inactive="text-secondary" data-filter-category-active>
                                <span class="text-truncate">{{ __('All') }}</span>

                                <span class="flex-shrink-0 badge badge-primary {{ (__('lang_dir' == 'rtl') ? 'mr-2' : 'ml-2') }}">{{ $templates->count() }}</span>
                            </a>

                            @foreach($templates as $template)
                                @if(!isset($menuCategoryName) || isset($menuCategoryName) && $menuCategoryName != $template->category->name)
                                    <div class="border-left-0 border-left-lg border-bottom border-bottom-lg-0"></div>
                                    <a href="#" class="text-truncate text-decoration-none text-secondary d-flex align-items-center justify-content-lg-center font-weight-medium py-3 px-3 cursor-pointer w-100" data-filter-category="{{ __($template->category->id) }}" data-text-color-active="text-{{ categoryColor($template->category->id) }}" data-text-color-inactive="text-secondary">
                                        <span class="text-truncate">{{ __($template->category->name) }}</span>

                                        <span class="flex-shrink-0 badge badge-{{ categoryColor($template->category->id) }} {{ (__('lang_dir' == 'rtl') ? 'mr-2' : 'ml-2') }}">{{ $templates->countBy('category_id')->toArray()[$template->category_id] }}</span>
                                    </a>
                                @endif

                                @php $menuCategoryName = $template->category->name; @endphp
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="row m-n2" id="templates">
                    @foreach($templates as $template)
                        @if(!isset($categoryName) || isset($categoryName) && $categoryName != $template->category->name)
                            <div class="col-12 p-2 mt-3" data-category-label="{{ __($template->category->id) }}">
                                <div class="badge badge-{{ categoryColor($template->category->id) }}">{{ __($template->category->name) }}</div>
                            </div>
                        @endif

                        <div class="col-12 col-md-6 col-lg-4 p-2" data-template="{{ __($template->name) }}" data-template-title="{{ __($template->name) }}" data-template-parent="{{ __($template->category->name) }}" data-template-category="{{ __($template->category->id) }}">
                            <div class="card border-0 h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                    <div class="row">
                                        <div class="col">
                                            <div class="d-flex position-relative text-{{ categoryColor($template->category_id) }} width-10 height-10 align-items-center justify-content-center flex-shrink-0">
                                                <div class="position-absolute bg-{{ categoryColor($template->category_id) }} opacity-10 top-0 right-0 bottom-0 left-0 border-radius-xl"></div>
                                                @if($template->isCustom())
                                                    <span class="width-5 height-5 d-flex align-items-center justify-content-center font-size-xl">{{ $template->icon }}</span>
                                                @else
                                                    @include('icons.' . $template->icon, ['class' => 'fill-current width-5 height-5'])
                                                @endif
                                            </div>
                                        </div>

                                        @if($template->isCustom())
                                            <div class="col-auto d-flex align-items-center justify-content-center">
                                                <div class="width-8 height-8 position-absolute z-100">
                                                    @include('templates.partials.menu')
                                                </div>
                                                <div class="width-8 height-8"></div>
                                            </div>
                                        @endif
                                    </div>

                                    <a href="{{ $template->url }}" class="text-dark font-weight-bold stretched-link text-decoration-none text-truncate mt-3 mb-1">{{ __($template->name) }}</a>

                                    <div class="text-muted">{{ __($template->description) }}</div>
                                </div>
                            </div>
                        </div>

                        @php $categoryName = $template->category->name; @endphp
                    @endforeach
                </div>

                @if ($templates->hasPages())
                    <div class="mt-3 align-items-center">
                        <div class="row">
                            <div class="col-auto">
                                {{ $templates->onEachSide(1)->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@include('shared.sidebars.user')
