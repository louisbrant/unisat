@section('site_title', formatTitle([e($page['name']), config('settings.title')]))

@extends('layouts.app')

@section('head_content')

@endsection

@section('content')
<div class="bg-base-1 flex-fill">
    <div class="container py-6">
        <div class="text-center">
            <h1 class="h2 mb-3 d-inline-block">{{ __($page->name) }}</h1>
            <div class="m-auto">
                <p class="text-muted font-weight-normal font-size-lg mb-0">{{ __('Updated at') }}: {{ $page->updated_at->tz(Auth::user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')) }}.</p>
            </div>
        </div>

        <div class="h-100 justify-content-center align-items-center mt-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    {!! __($page->content) !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@include('shared.sidebars.user')