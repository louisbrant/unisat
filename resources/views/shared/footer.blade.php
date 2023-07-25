<footer id="footer" class="footer bg-base-0{{ isset($lightweight) ? ' d-print-none' : '' }}">
    <div class="container py-5">
        @if(isset($lightweight) == false)
            <div class="row">
                <div class="col-12 col-lg">
                    <ul class="nav p-0 mx-n3 mb-3 mb-lg-0 d-flex flex-column flex-lg-row">
                        <li class="nav-item d-flex">
                            <a href="{{ route('contact') }}" class="nav-link py-1">{{ __('Contact') }}</a>
                        </li>

                        <li class="nav-item d-flex">
                            <a href="{{ config('settings.legal_terms_url') }}" class="nav-link py-1">{{ __('Terms') }}</a>
                        </li>

                        <li class="nav-item d-flex">
                            <a href="{{ config('settings.legal_privacy_url') }}" class="nav-link py-1">{{ __('Privacy') }}</a>
                        </li>

                        <li class="nav-item">
                            <a href="{{ route('developers') }}" class="nav-link py-1">{{ __('Developers') }}</a>
                        </li>

                        @foreach ($footerPages as $page)
                            <li class="nav-item d-flex">
                                <a href="{{ route('pages.show', $page['slug']) }}" class="nav-link py-1">{{ __($page['name']) }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-12 col-lg-auto">
                    <div class="mt-auto py-1 d-flex align-items-center">
                        @foreach (['social_facebook' => __('Facebook'), 'social_twitter' => 'Twitter', 'social_instagram' => 'Instagram', 'social_youtube' => 'YouTube'] as $url => $title)
                            @if(config('settings.'.$url))
                                <a href="{{ config('settings.'.$url) }}" class="text-secondary text-decoration-none d-flex align-items-center{{ (__('lang_dir') == 'rtl' ? ' ml-3 ml-lg-0 mr-lg-3' : ' mr-3 mr-lg-0 ml-lg-3') }}" data-tooltip="true" title="{{ $title }}" rel="nofollow noreferrer noopener">
                                    @include('icons.'.strtolower($title), ['class' => 'fill-current width-5 height-5'])
                                    <span class="sr-only">{{ $title }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <hr>
        @endif
        <div class="row">
            <div class="col-12 col-lg order-2 order-lg-1">
                <div class="text-muted py-1">{{ __('© :year :name.', ['year' => now()->year, 'name' => config('settings.title')]) }} {{ __('All rights reserved.') }}</div>
            </div>
            <div class="col-12 col-lg-auto order-1 order-lg-2 d-flex flex-column flex-lg-row">
                <div class="nav p-0 mx-n3 mb-3 mb-lg-0 d-flex flex-column flex-lg-row">
                    <div class="nav-item d-flex">
                        <a href="#" class="nav-link py-1 d-flex align-items-center text-secondary" id="dark-mode" data-tooltip="true" title="{{ __('Change theme') }}">
                            @include('icons.contrast', ['class' => 'width-4 height-4 fill-current ' . (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])
                            <span class="text-muted" data-text-light="{{ __('Light') }}" data-text-dark="{{ __('Dark') }}">{{ (config('settings.dark_mode') == 1 ? __('Dark') : __('Light')) }}</span>
                        </a>
                    </div>

                    @if(count(config('app.locales')) > 1)
                        <div class="nav-item d-flex">
                            <a href="#" class="nav-link py-1 d-flex align-items-center text-secondary" data-toggle="modal" data-target="#change-language-modal" data-tooltip="true" title="{{ __('Change language') }}">
                                @include('icons.language', ['class' => 'width-4 height-4 fill-current ' . (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2')])
                                <span class="text-muted">{{ config('app.locales')[config('app.locale')]['name'] }}</span>
                            </a>
                        </div>

                        <div class="modal fade" id="change-language-modal" tabindex="-1" role="dialog" aria-labelledby="change-language-modal-label" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="dialog">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header">
                                        <h6 class="modal-title" id="change-language-modal-label">{{ __('Change language') }}</h6>
                                        <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-14" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close', ['class' => 'fill-current width-4 height-4'])</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('locale') }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="row">
                                                @foreach(config('app.locales') as $code => $language)
                                                    <div class="col-6">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" id="i-language-{{ $code }}" name="locale" class="custom-control-input" value="{{ $code }}" @if(config('app.locale') == $code) checked @endif>
                                                            <label class="custom-control-label" for="i-language-{{ $code }}" lang="{{ $code }}">{{ $language['name'] }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('shared.cookie-law')
</footer>
