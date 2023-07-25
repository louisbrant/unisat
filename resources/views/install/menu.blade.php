@php
    $menu = [
        [
            'icon' => 'home',
            'route' => 'install'
        ],
        [
            'icon' => 'checklist',
            'route' => 'install.requirements'
        ],
        [
            'icon' => 'folder-open',
            'route' => 'install.permissions'
        ],
        [
            'icon' => 'dns',
            'route' => 'install.database'
        ],
        [
            'icon' => 'account-circle',
            'route' => 'install.account'
        ],
        [
            'icon' => 'checkmark',
            'route' => 'install.complete'
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