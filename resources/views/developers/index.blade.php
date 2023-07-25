@extends('layouts.app')

@section('site_title', formatTitle([__('Developers'), config('settings.title')]))

@section('content')
    <div class="bg-base-1 flex-fill">
        <div class="container h-100 py-6">

            <div class="text-center">
                <h1 class="h2 mb-3 d-inline-block">{{ __('Developers') }}</h1>
                <div class="m-auto">
                    <p class="text-muted font-weight-normal font-size-lg mb-4">{{ __('Explore our API documentation.') }}</p>
                </div>
            </div>

            @php
                $resources = [
                    [
                        'icon' => 'document',
                        'title' => __('Documents'),
                        'description' => __('Manage the documents.'),
                        'route' => route('developers.documents')
                    ],
                    [
                        'icon' => 'image',
                        'title' => __('Images'),
                        'description' => __('Manage the images.'),
                        'route' => route('developers.images')
                    ],
                    [
                        'icon' => 'chat',
                        'title' => __('Chats'),
                        'description' => __('Manage the chats.'),
                        'route' => route('developers.chats')
                    ],
                    [
                        'icon' => 'forum',
                        'title' => __('Messages'),
                        'description' => __('Manage the messages.'),
                        'route' => route('developers.messages')
                    ],
                    [
                        'icon' => 'headphones',
                        'title' => __('Transcriptions'),
                        'description' => __('Manage the transcriptions.'),
                        'route' => route('developers.transcriptions')
                    ],
                    [
                        'icon' => 'portrait',
                        'title' => __('Account'),
                        'description' => __('Manage the account.'),
                        'route' => route('developers.account')
                    ]
                ];
            @endphp

            <div class="row m-n2">
                @foreach($resources as $resource)
                    <div class="col-12 col-md-4 p-2">
                        <div class="card border-0 h-100 shadow-sm">
                            <div class="card-body d-flex">
                                <div class="d-flex position-relative text-primary width-12 height-12 align-items-center justify-content-center flex-shrink-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
                                    <div class="position-absolute bg-primary opacity-10 top-0 right-0 bottom-0 left-0 border-radius-2xl"></div>
                                    @include('icons.' . $resource['icon'], ['class' => 'fill-current width-6 height-6'])
                                </div>
                                <div class="d-flex flex-column justify-content-center">
                                    <a href="{{ $resource['route'] }}" class="text-dark font-weight-medium text-decoration-none stretched-link">{{ $resource['title'] }}</a>

                                    <div class="text-muted">
                                        {{ $resource['description'] }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@include('shared.sidebars.user')
