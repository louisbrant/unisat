@section('site_title', formatTitle([config('settings.title'), __(config('settings.tagline'))]))

@extends('layouts.app')

@section('head_content')

@endsection

@section('content')
<div class="flex-fill">
    <div class="bg-base-0 position-relative pt-5 pt-sm-6">
        <div class="container position-relative z-1">
            <div class="row py-sm-5">
                <div class="col-12 text-center text-break">
                    <h1 class="display-4 mb-0 font-weight-bold">
                        {{ __('AI powered content generator') }}
                    </h1>

                    <p class="text-muted font-weight-normal my-4 font-size-xl">
                        {{ __('Create unique and engaging content that will increase conversions and drive sales.') }}
                    </p>

                    <div class="pt-2 d-flex flex-column flex-sm-row justify-content-center">
                        <a href="{{ config('settings.registration') ? route('register') : route('login') }}" class="btn btn-primary btn-lg font-size-lg align-items-center mt-3">{{ __('Get started') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="position-relative z-0 mt-5 mt-sm-0">
            <div class="container">
                <div class="position-absolute top-0 right-0 bottom-0 left-0 z-1 more-gradient"></div>
                <div class="row position-relative d-flex justify-content-center">
                    <div class="position-relative col-12">
                        <img src="{{ (config('settings.dark_mode') == 1 ? asset('img/hero_dark.png') : asset('img/hero.png')) }}" class="img-fluid shadow-lg border-top-left-radius-2xl border-top-right-radius-2xl image-rendering-optimize-contrast" data-theme-dark="{{ asset('img/hero_dark.png') }}" data-theme-light="{{ asset('img/hero.png') }}" width="1512" height="700" data-theme-target="src" alt="{{ config('settings.title') }}">
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-base-0 container position-relative pb-5 pb-md-7 z-1">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="row">
                        <div class="col-12 col-lg-4 mt-5 d-flex">
                            <div class="d-flex position-relative text-primary width-8 height-8 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-lg"></div>
                                @include('icons.apps', ['class' => 'fill-current width-4 height-4'])
                            </div>
                            <div>
                                <div class="d-block w-100"><div class="mt-1 mb-1 d-inline-block font-weight-bold font-size-lg">{{ __('Templates') }}</div></div>
                                <div class="d-block w-100 text-muted">{{ __('Streamline content creation through our ready to use templates.') }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4 mt-5 d-flex">
                            <div class="d-flex position-relative text-primary width-8 height-8 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-lg"></div>
                                @include('icons.document', ['class' => 'fill-current width-4 height-4'])
                            </div>
                            <div>
                                <div class="d-block w-100"><div class="mt-1 mb-1 d-inline-block font-weight-bold font-size-lg">{{ __('Documents') }}</div></div>
                                <div class="d-block w-100 text-muted">{{ __('Leverage the power of AI to create quality content in seconds.') }}</div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4 mt-5 d-flex">
                            <div class="d-flex position-relative text-primary width-8 height-8 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-lg"></div>
                                @include('icons.image', ['class' => 'fill-current width-4 height-4'])
                            </div>
                            <div>
                                <div class="d-block w-100"><div class="mt-1 mb-1 d-inline-block font-weight-bold font-size-lg">{{ __('Images') }}</div></div>
                                <div class="d-block w-100 text-muted">{{ __('Generate stunning images to drive more content engagement.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-1 overflow-hidden">
        <div class="container py-5 py-md-7 position-relative z-1">
            <div class="text-center">
                <h2 class="h2 mb-3 font-weight-bold text-center">{{ __('How it works') }}</h2>
                <div class="m-auto">
                    <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Your content ready, in three easy steps.') }}</p>
                </div>
            </div>

            <div class="row mx-n5">
                <div class="col-12 col-lg-4 px-5 mt-5">
                    <div class="position-relative width-16 height-16 d-flex align-items-center justify-content-center mb-3 mx-auto">
                        <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 rounded-circle"></div>
                        <div class="h5 mb-0 font-weight-bold text-primary">1</div>
                    </div>
                    <div class="h5 my-1 font-weight-bold text-center">{{ __('Select a template') }}</div>
                    <div class="d-block w-100 text-muted mb-5 text-center">{{ __('Start by choosing a content creation template.') }}</div>

                    <div class="position-relative">
                        <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 border-radius-2xl" style="transform: translate(1rem, 1rem);"></div>

                        <div class="card border-0 shadow-lg border-radius-2xl overflow-hidden cursor-default min-height-80">
                            <div class="card-body d-flex flex-column p-5 align-items-center justify-content-center">
                                <div class="d-flex width-12 height-12 position-relative align-items-center justify-content-center flex-shrink-0 mb-3">
                                    <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                    @include('icons.apps', ['class' => 'fill-current width-6 height-6 text-primary'])
                                </div>

                                <div class="d-block w-100 text-center"><div class="h5 mt-1 mb-1 d-inline-block font-weight-bold">
                                    {{ __('Template') }}</div></div>
                                <div class="d-block w-100 text-center text-muted">{{ __('One of the many templates we have available.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 px-5 mt-5">
                    <div class="position-relative width-16 height-16 d-flex align-items-center justify-content-center mb-3 mx-auto">
                        <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 rounded-circle"></div>
                        <div class="h5 mb-0 font-weight-bold text-primary">2</div>
                    </div>
                    <div class="h5 my-1 font-weight-bold text-center">{{ __('Fill the form') }}</div>
                    <div class="d-block w-100 text-muted mb-5 text-center">{{ __('Add a detailed description of your request for the AI.') }}</div>

                    <div class="position-relative">
                        <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 border-radius-2xl" style="transform: translate(1rem, 1rem);"></div>

                        <div class="card border-0 shadow-lg border-radius-2xl overflow-hidden cursor-default min-height-80">
                            <div class="card-body p-5 d-flex flex-column justify-content-center">
                                <div class="form-group">
                                    <label for="i-name">{{ __('Name') }}</label>
                                    <div class="form-control text-truncate">{{ __('Love poem') }}</div>
                                </div>

                                <div class="form-group">
                                    <label for="i-content">{{ __('Prompt') }}</label>
                                    <div class="form-control height-auto">{{ __('Write a short poem about love.') }}<br><br></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4 px-5 mt-5">
                    <div class="position-relative width-16 height-16 d-flex align-items-center justify-content-center mb-3 mx-auto">
                        <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 rounded-circle"></div>
                        <div class="h5 mb-0 font-weight-bold text-primary">3</div>
                    </div>
                    <div class="h5 my-1 font-weight-bold text-center">{{ __('Get the result') }}</div>
                    <div class="d-block w-100 text-muted mb-5 text-center">{{ __('Receive a high quality result ready to be published.') }}</div>

                    <div class="position-relative">
                        <div class="position-absolute top-0 right-0 bottom-0 left-0 bg-primary opacity-10 border-radius-2xl" style="transform: translate(1rem, 1rem);"></div>

                        <div class="card border-0 shadow-lg border-radius-2xl overflow-hidden cursor-default min-height-80">
                            <div class="card-body d-flex flex-column justify-content-center p-5 p-xl-4">
                                <div class="h5 mb-3 font-weight-bold text-center">{{ __('Love poem') }}</div>

                                <div class="text-truncate text-center">
                                    {{ __('Love is a mystery') }},
                                </div>
                                <div class="text-truncate text-center">
                                    {{ __('A feeling so strong') }},
                                </div>
                                <div class="text-truncate text-center">
                                    {{ __('It can bring us together') }},
                                </div>
                                <div class="text-truncate text-center">
                                    {{ __('Or it can be wrong') }}.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-base-0">
        <div class="container position-relative py-5 py-md-7 d-flex flex-column z-1">
            <h2 class="h2 mb-3 font-weight-bold text-center">{{ __('Templates') }}</h2>
            <div class="m-auto text-center">
                <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Over :number templates to automate content creation.', ['number' => floor(count($templates) / 10) * 10]) }}</p>
            </div>

            <div class="row position-relative">
                <div class="position-absolute top-0 right-0 bottom-0 left-0 z-1 more-gradient"></div>
                @foreach($templates->take(28) as $template)
                    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mt-5">
                        <div class="d-flex align-items-center">
                            <div class="d-flex position-relative text-primary width-8 height-8 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-lg"></div>
                                @include('icons.' . $template->icon, ['class' => 'fill-current width-4 height-4'])
                            </div>
                            <div>
                                <div class="d-block w-100"><div class="d-inline-block font-weight-bold">{{ __($template->name) }}</div></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @if(paymentProcessors())
        <div class="bg-base-1">
            <div class="container py-5 py-md-7 position-relative z-1">
                <div class="text-center">
                    <h2 class="h2 mb-3 font-weight-bold text-center">{{ __('Pricing') }}</h2>
                    <div class="m-auto">
                        <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Simple pricing plans for everyone and every budget.') }}</p>
                    </div>
                </div>

                @include('shared.pricing')

                <div class="d-flex justify-content-center">
                    <a href="{{ route('pricing') }}" class="btn btn-outline-primary py-2 mt-5">{{ __('Learn more') }}<span class="sr-only"> {{ mb_strtolower(__('Pricing')) }}</span></a>
                </div>
            </div>
        </div>
    @else
        <div class="bg-base-1">
            <div class="container position-relative text-center py-5 py-md-7 d-flex flex-column z-1">
                <div class="flex-grow-1">
                    <div class="badge badge-pill badge-success mb-3 px-3 py-2">{{ __('Join us') }}</div>
                    <div class="text-center">
                        <h4 class="mb-3 font-weight-bold">{{ __('Ready to get started?') }}</h4>
                        <div class="m-auto">
                            <p class="font-weight-normal text-muted font-size-lg mb-0">{{ __('Create an account in seconds.') }}</p>
                        </div>
                    </div>
                </div>

                <div><a href="{{ config('settings.registration') ? route('register') : route('login') }}" class="btn btn-primary btn-lg font-size-lg mt-5">{{ __('Get started') }}</a></div>
            </div>
        </div>
    @endif
</div>
@endsection
