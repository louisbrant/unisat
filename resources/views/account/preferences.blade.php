@section('site_title', formatTitle([__('Preferences'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('account'), 'title' => __('Account')],
    ['title' => __('Preferences')]
]])

<div class="d-flex"><h1 class="h2 mb-3 text-break">{{ __('Preferences') }}</h1></div>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <div class="font-weight-medium py-1">
            {{ __('Preferences') }}
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-templates-tab" data-toggle="pill" href="#pills-templates" role="tab" aria-controls="pills-templates" aria-selected="true">{{ __('Templates') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('account.preferences') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="i-default-language" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Language') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                <select name="default_language" id="i-default-language" class="custom-select{{ $errors->has('default_language') ? ' is-invalid' : '' }}">
                    @foreach(array_intersect_key(config('languages'), array_flip(config('completions.languages'))) as $key => $value)
                        <option value="{{ $key }}" @if ((old('default_language') !== null && old('default_language') == $key) || (old('default_language') == null && $key == Auth::user()->default_language)) selected @endif>{{ __($value['name']) }}</option>
                    @endforeach
                </select>
                @if ($errors->has('default_language'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('default_language') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The language in which the result to be returned.') }}</small>
            </div>

            <div class="form-group">
                <label for="i-default-creativity" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Creativity') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                <select name="default_creativity" id="i-default-creativity" class="custom-select{{ $errors->has('creativity') ? ' is-invalid' : '' }}">
                    @foreach(config('completions.creativities') as $value => $key)
                        <option value="{{ $key }}" @if ((old('default_creativity') !== null && old('default_creativity') == $key) || (old('default_creativity') == null && !isset($default_creativity) && $key == Auth::user()->default_creativity)) selected @endif>{{ __(Str::ucfirst($value)) }}</option>
                    @endforeach
                </select>
                @if ($errors->has('default_creativity'))
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $errors->first('default_creativity') }}</strong>
                    </span>
                @endif
                <small class="form-text text-muted">{{ __('The creative level of result.') }}</small>
            </div>

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-templates" role="tabpanel" aria-labelledby="pills-templates-tab">
                    <div class="form-group">
                        <label for="i-default-variations" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Variations') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                        <select name="default_variations" id="i-default-variations" class="custom-select{{ $errors->has('default_variations') ? ' is-invalid' : '' }}">
                            @foreach(config('completions.variations') as $key)
                                <option value="{{ $key }}" @if ((old('default_variations') !== null && old('default_variations') == $key) || (old('default_variations') == null && !isset($variations) && $key == Auth::user()->default_variations)) selected @endif>{{ $key }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('default_variations'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('default_variations') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('The number of variations of results.') }}</small>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col">
                    <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
                </div>
                <div class="col-auto">
                </div>
            </div>
        </form>
    </div>
</div>
