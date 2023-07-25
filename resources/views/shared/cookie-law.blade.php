@if(config('settings.legal_cookie_url'))
    @if(request()->cookie('cookie_law') == 0)
        <div class="fixed-bottom pointer-events-none">
            <div class="d-flex justify-content-center align-items-center">
                <div class="border-0 mt-0 mr-3 mb-3 ml-3 p-2 rounded cookie-banner backdrop-filter-blur pointer-events-auto" id="cookie-banner">
                    <div class="row align-items-center p-1">
                        <div class="col">
                            {!! __('By using this website, you agree to our :policy.', ['policy' => mb_strtolower('<a href="'. config('settings.legal_cookie_url') .'">'. __('Cookie policy') .'</a>')]) !!}
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-outline-primary btn-sm" id="cookie-banner-dismiss">{{ __('OK') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif