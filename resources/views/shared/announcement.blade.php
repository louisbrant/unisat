@if(request()->cookie('announcement_' . $id) == 0)
    <div class="d-flex flex-column @auth content @endauth" id="announcement-banner">
        <div class="alert-{{ $type }} z-1030">
            <div class="@guest container @else container-fluid container-lg @endguest">
                <div class="alert alert-{{ $type }} alert-dismissible fade show mb-0 mx-n3">
                    {!! __($message) !!}

                    <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-12 p-0" data-dismiss="alert" aria-label="{{ __('Close') }}" id="announcement-banner-dismiss">
                        <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close', ['class' => 'fill-current width-4 height-4'])</span>
                    </button>
                </div>
            </div>
        </div>

        <script>
            'use strict';

            window.addEventListener('DOMContentLoaded', function () {
                document.querySelector('#announcement-banner-dismiss').addEventListener('click', function () {
                    setCookie('announcement_{{ $id }}', 1, new Date().getTime() + (10 * 365 * 24 * 60 * 60 * 1000), '/');
                    document.querySelector('#announcement-banner').classList.add('d-none');
                });
            });
        </script>
    </div>
@endif