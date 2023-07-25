@section('menu')
    @php
        $menu = [
            route('admin.dashboard') => [
                'icon' => 'grid-view',
                'title' => __('Dashboard')
            ],
            'settings' => [
                'icon' => 'settings',
                'title' => __('Settings'),
                'menu' => [
                    route('admin.settings', 'general') => [
                        'icon' => 'general',
                        'title' => __('General')
                    ],
                    route('admin.settings', 'advanced') => [
                        'icon' => 'tune',
                        'title' => __('Advanced')
                    ],
                    route('admin.settings', 'appearance') => [
                        'icon' => 'design-services',
                        'title' => __('Appearance')
                    ],
                    route('admin.settings', 'email') => [
                        'icon' => 'email',
                        'title' => __('Email')
                    ],
                    route('admin.settings', 'social') => [
                        'icon' => 'share',
                        'title' => __('Social')
                    ],
                    route('admin.settings', 'authentication') => [
                        'icon' => 'assignment-ind',
                        'title' => __('Authentication')
                    ],
                    route('admin.settings', 'announcements') => [
                        'icon' => 'campaign',
                        'title' => __('Announcements')
                    ],
                    route('admin.settings', 'payment-processors') => [
                        'icon' => 'memory',
                        'title' => __('Payment processors')
                    ],
                    route('admin.settings', 'billing-information') => [
                        'icon' => 'featured-play-list',
                        'title' => __('Billing information')
                    ],
                    route('admin.settings', 'legal') => [
                        'icon' => 'assignment',
                        'title' => __('Legal')
                    ],
                    route('admin.settings', 'captcha') => [
                        'icon' => 'pin',
                        'title' => __('Captcha')
                    ],
                    route('admin.settings', 'webhooks') => [
                        'icon' => 'webhook',
                        'title' => __('Webhooks')
                    ],
                    route('admin.settings', 'cronjob') => [
                        'icon' => 'schedule',
                        'title' => __('Cron job')
                    ],
                    route('admin.settings', 'license') => [
                        'icon' => 'vpn-key',
                        'title' => __('License')
                    ],
                ]
            ],
            route('admin.users') => [
                'icon' => 'people-alt',
                'title' => __('Users')
            ],
            route('admin.pages') => [
                'icon' => 'menu-book',
                'title' => __('Pages')
            ],
            route('admin.payments') => [
                'icon' => 'credit-card',
                'title' => __('Payments')
            ],
            route('admin.plans') => [
                'icon' => 'package',
                'title' => __('Plans')
            ],
            route('admin.coupons') => [
                'icon' => 'confirmation-number',
                'title' => __('Coupons')
            ],
            route('admin.tax_rates') => [
                'icon' => 'price-change',
                'title' => __('Tax rates')
            ],
            route('admin.templates') => [
                'icon' => 'apps',
                'title' => __('Templates')
            ],
            route('admin.documents') => [
                'icon' => 'document',
                'title' => __('Documents')
            ],
            route('admin.images') => [
                'icon' => 'image',
                'title' => __('Images')
            ],
            route('admin.chats') => [
                'icon' => 'chat',
                'title' => __('Chats')
            ],
            route('admin.transcriptions') => [
                'icon' => 'headphones',
                'title' => __('Transcriptions')
            ]
        ];
    @endphp

    <div class="nav d-block text-truncate">
        @foreach ($menu as $key => $value)
            <li class="nav-item">
                <a class="nav-link d-flex px-4 @if (str_starts_with(request()->url(), $key) && !isset($value['menu'])) active @endif" @if(isset($value['menu'])) data-toggle="collapse" href="#sub-menu-{{ $key }}" role="button" @if (array_filter(array_keys($value['menu']), function ($url) { return str_starts_with(request()->url(), $url); })) aria-expanded="true" @else aria-expanded="false" @endif aria-controls="collapse-{{ $key }}" @else href="{{ $key }}" @endif>
                    <span class="sidebar-icon d-flex align-items-center">@include('icons.' . $value['icon'], ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')])</span>
                    <span class="flex-grow-1 text-truncate">{{ $value['title'] }}</span>
                    @if (isset($value['menu'])) <span class="d-flex align-items-center ml-auto sidebar-expand">@include('icons.expand-more', ['class' => 'fill-current text-muted width-3 height-3'])</span> @endif
                </a>
            </li>

            @if (isset($value['menu']))
                <div class="collapse sub-menu @if (array_filter(array_keys($menu[$key]['menu']), function ($url) { return str_starts_with(request()->url(), $url); })) show @endif" id="sub-menu-{{ $key }}">
                    @foreach ($value['menu'] as $subKey => $subValue)
                        <a href="{{ $subKey }}" class="nav-link px-4 d-flex text-truncate @if (str_starts_with(request()->url(), $subKey)) active @endif">
                            <span class="sidebar-icon d-flex align-items-center">@include('icons.' . $subValue['icon'], ['class' => 'fill-current width-4 height-4 '.(__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3')])</span>
                            <span class="flex-grow-1 text-truncate">{{ $subValue['title'] }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </div>
@endsection
