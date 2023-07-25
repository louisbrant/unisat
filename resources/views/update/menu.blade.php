@php
    $menu = [
        [
            'icon' => 'home',
            'route' => 'update'
        ],
        [
            'icon' => 'update',
            'route' => 'update.overview'
        ],
        [
            'icon' => 'checkmark',
            'route' => 'update.complete'
        ]
    ];
@endphp

<div class="nav flex-column">
    <ul class="nav nav-pills d-flex justify-content-center mb-4">
        @foreach ($menu as $link)
            <li class="nav-item mx-1">
                @if(Route::currentRouteName() == $link['route'])
                    <a href="{{ route($link['route']) }}" class="btn btn-primary d-flex align-items-center">
                        @include('icons.' . $link['icon'], ['class' => 'width-4 height-4 fill-current'])&#8203;
                    </a>
                @else
                    <a href="#" class="btn d-flex align-items-center disabled">
                        @include('icons.' . $link['icon'], ['class' => 'width-4 height-4 fill-current'])&#8203;
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>