@extends('layouts.app')

@section('site_title', formatTitle([__('Messages'), __('Developers'), config('settings.title')]))

@section('head_content')

@endsection

@section('content')
    <div class="bg-base-1 flex-fill">
        <div class="container h-100 py-3 my-3">

            @include('shared.breadcrumbs', ['breadcrumbs' => [
                ['url' => route('home'), 'title' => __('Home')],
                ['url' => route('developers'), 'title' => __('Developers')],
                ['title' => __('Messages')]
            ]])

            <h1 class="h2 mb-0 d-inline-block">{{ __('Messages') }}</h1>

            @include('developers.notes')

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header align-items-center">
                    <div class="row">
                        <div class="col"><div class="font-weight-medium py-1">{{ __('List') }}</div></div>
                    </div>
                </div>

                <div class="card-body">
                    <p class="mb-1">
                        {{ __('API endpoint') }}:
                    </p>

<div class="bg-dark text-light p-3 rounded d-flex align-items-center mb-3" dir="ltr">
    <span class="badge bg-light text-success px-2 py-1 mr-3">GET</span>
    <pre class="m-0 text-light">{{ route('api.messages.index') }}</pre>
</div>

                    <p class="mb-1">
                        {{ __('Request example') }}:
                    </p>
<pre class="bg-dark text-light p-3 mb-0 rounded text-left" dir="ltr">
curl --location --request GET '{{ route('api.messages.index') }}' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer <span class="text-success">{api_key}</span>'
</pre>

                @include('developers.messages.list', ['type' => 0])
                </div>
            </div>

            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header align-items-center">
                    <div class="row">
                        <div class="col"><div class="font-weight-medium py-1">{{ __('Store') }}</div></div>
                    </div>
                </div>

                <div class="card-body">
                    <p class="mb-1">
                        {{ __('API endpoint') }}:
                    </p>

<div class="bg-dark text-light p-3 rounded d-flex align-items-center mb-3" dir="ltr">
<span class="badge bg-light text-warning px-2 py-1 mr-3">POST</span>
<pre class="m-0 text-light">{{ route('api.messages.store') }}</pre>
</div>
                    <p class="mb-1">
                        {{ __('Request example') }}:
                    </p>
<pre class="bg-dark text-light p-3 rounded text-left" dir="ltr">
curl --location --request POST '{{ route('api.messages.store') }}' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--header 'Authorization: Bearer <span class="text-success">{api_key}</span>' \
--data-urlencode 'name=<span class="text-success">{name}</span>' \
--data-urlencode 'description=<span class="text-success">{description}</span>'
</pre>

                    @include('developers.messages.store-update', ['type' => 1])
                </div>
            </div>
        </div>
    </div>
@endsection

@include('shared.sidebars.user')
