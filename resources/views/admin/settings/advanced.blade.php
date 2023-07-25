@section('site_title', formatTitle([__('Advanced'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Advanced') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Advanced') }}</div></div>
    <div class="card-body">

        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true">{{ __('General') }}</a>
            </li>
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-crawler-tab" data-toggle="pill" href="#pills-crawler" role="tab" aria-controls="pills-crawler" aria-selected="false">{{ __('Crawler') }}</a>
            </li>
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-openai-tab" data-toggle="pill" href="#pills-openai" role="tab" aria-controls="pills-openai" aria-selected="false">{{ __('OpenAI') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('admin.settings', 'shortener') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
                    <div class="form-group">
                        <label for="i-bad-words">{{ __('Bad words') }}</label>
                        <textarea name="bad_words" id="i-bad-words" class="form-control{{ $errors->has('bad_words') ? ' is-invalid' : '' }}" rows="3">{{ config('settings.bad_words') }}</textarea>
                        @if ($errors->has('bad_words'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bad_words') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('One per line.') }}</small>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-crawler" role="tabpanel" aria-labelledby="pills-crawler-tab">
                    <div class="form-group">
                        <label for="i-request-user-agent">{{ __('User-Agent') }}</label>
                        <input type="text" name="request_user_agent" id="i-request-user-agent" class="form-control{{ $errors->has('request_user_agent') ? ' is-invalid' : '' }}" value="{{ old('request_user_agent') ?? config('settings.request_user_agent') }}">
                        @if ($errors->has('request_user_agent'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('request_user_agent') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-request-proxy">{{ __('Proxies') }}</label>
                        <textarea name="request_proxy" id="i-request-proxy" class="form-control{{ $errors->has('request_proxy') ? ' is-invalid' : '' }}" rows="3" placeholder="http://username:password@ip:port
">{{ config('settings.request_proxy') }}</textarea>
                        @if ($errors->has('request_proxy'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('request_proxy') }}</strong>
                            </span>
                        @endif
                        <small class="form-text text-muted">{{ __('One per line.') }}</small>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-openai" role="tabpanel" aria-labelledby="pills-openai-tab">
                    <div class="form-group">
                        <label for="i-openai-key">{{ __('API key') }}</label>
                        <input type="password" name="openai_key" id="i-openai-key" class="form-control{{ $errors->has('openai_key') ? ' is-invalid' : '' }}" value="{{ old('openai_key') ?? config('settings.openai_key') }}">
                        @if ($errors->has('openai_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('openai_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-openai-completions-model" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Model') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                        <select name="openai_completions_model" id="i-openai-completions-model" class="custom-select{{ $errors->has('openai_completions_model') ? ' is-invalid' : '' }}">
                            @foreach(['gpt-4', 'gpt-3.5-turbo'] as $key)
                                <option value="{{ $key }}" @if ((old('openai_completions_model') !== null && old('openai_completions_model') == $key) || (old('openai_completions_model') == null && $key == config('settings.openai_completions_model'))) selected @endif>{{ $key }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="i-default-language" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Language') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                        <select name="openai_default_language" id="i-default-language" class="custom-select{{ $errors->has('openai_default_language') ? ' is-invalid' : '' }}">
                            @foreach(array_intersect_key(config('languages'), array_flip(config('completions.languages'))) as $key => $value)
                                <option value="{{ $key }}" @if ((old('openai_default_language') !== null && old('openai_default_language') == $key) || (old('openai_default_language') == null && $key == config('settings.openai_default_language'))) selected @endif>{{ __($value['name']) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="i-ai-assistant-name">{{ __('Assistant name') }}</label>
                        <input type="text" name="ai_assistant_name" id="i-ai-assistant-name" class="form-control{{ $errors->has('ai_assistant_name') ? ' is-invalid' : '' }}" value="{{ old('ai_assistant_name') ?? config('settings.ai_assistant_name') }}">
                        @if ($errors->has('ai_assistant_name'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('ai_assistant_name') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-ai-assistant-email">{{ __('Assistant email') }}</label>
                        <input type="text" name="ai_assistant_email" id="i-ai-assistant-email" class="form-control{{ $errors->has('ai_assistant_email') ? ' is-invalid' : '' }}" value="{{ old('ai_assistant_email') ?? config('settings.ai_assistant_email') }}">
                        @if ($errors->has('ai_assistant_email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('ai_assistant_email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>
