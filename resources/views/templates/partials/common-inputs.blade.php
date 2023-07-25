@if(isset($view))
    @if(in_array($view, ['article', 'blog-post']))
        <div class="form-group">
            <label for="i-length">{{ __('Length') }}</label>
            <select name="length" id="i-length" class="custom-select{{ $errors->has('length') ? ' is-invalid' : '' }}">
                @foreach(config('completions.lengths') as $key)
                    <option value="{{ $key }}" @if ((old('length') !== null && old('length') == $key) || (isset($length) && $length == $key && old('length') == null) || (old('length') == null && $key == 'medium')) selected @endif>{{ __(Str::ucfirst($key)) }}</option>
                @endforeach
            </select>
            @if ($errors->has('length'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('length') }}</strong>
                </span>
            @endif
            <small class="form-text text-muted">{{ __('The length of the result.') }}</small>
        </div>
    @endif

    @if(in_array($view, ['subheadline', 'about-us', 'call-to-action', 'headline', 'mission-statement', 'newsletter', 'press-release', 'value-proposition', 'vision-statement', 'video-script', 'social-post', 'social-post-caption', 'welcome-email', 'feature-section']))
        <div class="form-group">
            <label for="i-tone">{{ __('Tone') }}</label>
            <select name="tone" id="i-tone" class="custom-select{{ $errors->has('tone') ? ' is-invalid' : '' }}">
                @foreach(config('completions.tones') as $key => $value)
                    <option value="{{ $key }}" @if ((old('tone') !== null && old('tone') == $key) || (isset($tone) && $tone == $key && old('tone') == null)) selected @endif>{{ __($value['emoji']) }} {{ __($value['name']) }}</option>
                @endforeach
            </select>
            @if ($errors->has('tone'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('tone') }}</strong>
                </span>
            @endif
            <small class="form-text text-muted">{{ __('The tone of result.') }}</small>
        </div>
    @endif
@endif

<div class="collapse {{ $errors->has('language') || $errors->has('creativity') || $errors->has('variations') ? ' show' : ''}}" id="collapse-form-advanced">
    @if(isset($view) && !in_array($view, ['freestyle', 'new']))
        <div class="form-group">
            <label for="i-language">{{ __('Language') }}</label>
            <select name="language" id="i-language" class="custom-select{{ $errors->has('language') ? ' is-invalid' : '' }}">
                @foreach(config('languages') as $key => $value)
                    <option value="{{ $key }}" @if ((old('language') !== null && old('language') == $key) || (isset($language) && $language == $key && old('language') == null) || (old('language') == null && !isset($language) && $key == Auth::user()->default_language)) selected @endif>{{ __($value['name']) }}</option>
                @endforeach
            </select>
            @if ($errors->has('language'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('language') }}</strong>
                </span>
            @endif
            <small class="form-text text-muted">{{ __('The language in which the result to be returned.') }}</small>
        </div>
    @endif

    @if(!request()->is('images/new'))
        <div class="form-group">
            <label for="i-creativity">{{ __('Creativity') }}</label>
            <select name="creativity" id="i-creativity" class="custom-select{{ $errors->has('creativity') ? ' is-invalid' : '' }}">
                @foreach(config('completions.creativities') as $value => $key)
                    <option value="{{ $key }}" @if ((old('creativity') !== null && old('creativity') == $key) || (isset($creativity) && $creativity == $key && old('creativity') == null) || (old('creativity') == null && !isset($creativity) && $key == Auth::user()->default_creativity)) selected @endif>{{ __(Str::ucfirst($value)) }}</option>
                @endforeach
            </select>
            @if ($errors->has('creativity'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('creativity') }}</strong>
                </span>
            @endif
            <small class="form-text text-muted">{{ __('The creative level of result.') }}</small>
        </div>
    @endif

    <div class="form-group">
        <label for="i-variations">{{ __('Variations') }}</label>

        <select name="variations" id="i-variations" class="custom-select{{ $errors->has('variations') ? ' is-invalid' : '' }}">
            @foreach(config('completions.variations') as $key)
                <option value="{{ $key }}" @if ((old('variations') !== null && old('variations') == $key) || (isset($variations) && $variations == $key && old('variations') == null) || (old('variations') == null && !isset($variations) && $key == Auth::user()->default_variations)) selected @endif>{{ $key }}</option>
            @endforeach
        </select>
        @if ($errors->has('variations'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('variations') }}</strong>
            </span>
        @endif
        <small class="form-text text-muted">{{ __('The number of variations of results.') }}</small>
    </div>
</div>
