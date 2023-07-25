@section('site_title', formatTitle([__('New'), __('Image'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('images'), 'title' => __('Images')],
    ['title' => __('New')],
]])

<h1 class="h2 mb-0 d-inline-block">{{ __('New') }}</h1>

<div class="row mx-n2">
    <div class="col-12 col-lg-5 px-2">
        <div class="card border-0 shadow-sm mt-3 @if(isset($images)) d-none d-lg-flex @endif" id="ai-form">
            <div class="card-header align-items-center">
                <div class="row">
                    <div class="col">
                        <div class="font-weight-medium py-1">{{ __('Image') }}</div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @include('shared.message')

                <form action="{{ route('images.new') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label for="i-name">{{ __('Name') }}</label>
                        <input type="text" name="name" id="i-name" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" value="{{ $name ?? (old('name') ?? '') }}">
                        @if ($errors->has('name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('name') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The name of the image.') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-description">{{ __('Description') }}</label>
                        <textarea dir="ltr" rows="5" name="description" id="i-description" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('Blue butterfly') }}">{{ $description ?? (old('description') ?? '') }}</textarea>
                        @if ($errors->has('description'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('description') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The description of the image.') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-style">{{ __('Style') }}</label>
                        <select name="style" id="i-style" class="custom-select{{ $errors->has('style') ? ' is-invalid' : '' }}">
                            <option value="">{{ __('None') }}</option>
                            @foreach(config('images.styles') as $key => $value)
                                <option value="{{ $key }}" @if ((old('style') !== null && old('style') == $key) || (isset($style) && $style == $key && old('style') == null)) selected @endif>{{ __($value) }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('style'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('style') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The style of the image.') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-medium">{{ __('Medium') }}</label>
                        <select name="medium" id="i-medium" class="custom-select{{ $errors->has('medium') ? ' is-invalid' : '' }}">
                            <option value="">{{ __('None') }}</option>
                            @foreach(config('images.mediums') as $key => $value)
                                <option value="{{ $key }}" @if ((old('medium') !== null && old('medium') == $key) || (isset($medium) && $medium == $key && old('medium') == null)) selected @endif>{{ __($value) }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('medium'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('medium') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The medium of the image.') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-filter">{{ __('Filter') }}</label>
                        <select name="filter" id="i-filter" class="custom-select{{ $errors->has('filter') ? ' is-invalid' : '' }}">
                            <option value="">{{ __('None') }}</option>
                            @foreach(config('images.filters') as $key => $value)
                                <option value="{{ $key }}" @if ((old('filter') !== null && old('filter') == $key) || (isset($filter) && $filter == $key && old('filter') == null)) selected @endif>{{ __($value) }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('filter'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('filter') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The filter of the image.') }}</small>
                    </div>

                    <div class="form-group">
                        <label for="i-resolution">{{ __('Resolution') }}</label>
                        <select name="resolution" id="i-resolution" class="custom-select{{ $errors->has('resolution') ? ' is-invalid' : '' }}">
                            @foreach(config('images.resolutions') as $key => $value)
                                <option value="{{ $key }}" @if ((old('resolution') !== null && old('resolution') == $key) || (isset($resolution) && $resolution == $key && old('resolution') == null) || (old('resolution') == null && !isset($resolution) && $key == '512x512')) selected @endif>{{ __($value) }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('resolution'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('resolution') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The resolution of the image.') }}</small>
                    </div>

                    @include('templates.partials.common-inputs')

                    <div class="row mx-n2">
                        <div class="col px-2">
                            <button type="submit" name="submit" class="btn btn-primary position-relative" data-button-loader>
                                <div class="position-absolute top-0 right-0 bottom-0 left-0 d-flex align-items-center justify-content-center">
                                    <span class="d-none spinner-border spinner-border-sm width-4 height-4" role="status"></span>
                                </div>
                                <span class="spinner-text">{{ __('Generate') }}</span>&#8203;
                            </button>
                        </div>
                        <div class="col-auto px-2">
                            <a href="{{ route('templates.freestyle') }}" class="btn btn-outline-secondary d-none {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}">{{ __('Reset') }}</a>
                            <button class="btn btn-outline-secondary {{ (__('lang_dir') == 'rtl' ? 'mr-auto' : 'ml-auto') }}" type="button" data-toggle="collapse" data-target="#collapse-form-advanced" aria-expanded="{{ $errors->has('language') || $errors->has('creativity') || $errors->has('variations') ? 'true' : 'false'}}" aria-controls="collapse-form-advanced">
                                {{ __('Advanced') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if(isset($images))
            <a href="#" class="btn btn-outline-secondary btn-block d-lg-none mt-3" id="ai-form-show-button">{{ __('Show form') }}</a>
        @endif
    </div>

    <div class="col-12 col-lg-7 px-2">
        @if(isset($images))
            <div class="mt-3" id="ai-results">
                @foreach($images as $image)
                    <div class="mt-3">
                        @include('templates.partials.image-result', ['image' => $image])
                    </div>
                @endforeach
            </div>
        @endif

        <div class="position-relative pt-3 h-100 @if(isset($images)) d-none @else d-flex @endif" id="ai-placeholder-results">
            <div class="position-relative h-100 align-items-center justify-content-center d-flex w-100">
                <div class="text-muted font-weight-medium z-1" id="ai-placeholder-text-start">
                    <div class="width-6 height-6 mt-5"></div>
                    <div class="my-3">{{ __('Start by filling the form.') }}</div>
                    <div class="width-6 height-6 mb-5"></div>
                </div>
                <div class="text-muted flex-column font-weight-medium z-1 align-items-center d-none " id="ai-placeholder-text-progress">
                    <div class="width-6 height-6 mt-5"></div>
                    <div class="my-3">{{ __('Generating the content, please wait.') }}</div>
                    <div class="spinner-border spinner-border-sm width-6 height-6 mb-5" role="status"></div>
                </div>
                <div class="position-absolute top-0 right-0 bottom-0 left-0 border rounded border-width-2 border-dashed opacity-20 z-0"></div>
            </div>
        </div>
    </div>
</div>
