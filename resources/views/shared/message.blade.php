@if (request()->session()->get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ request()->session()->get('success') }}
        <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-12 p-0" data-dismiss="alert" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close', ['class' => 'fill-current width-4 height-4'])</span>
        </button>
    </div>
@endif

@if (request()->session()->get('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ request()->session()->get('error') }}
        <button type="button" class="close d-flex align-items-center justify-content-center width-12 height-12 p-0" data-dismiss="alert" aria-label="{{ __('Close') }}">
            <span aria-hidden="true" class="d-flex align-items-center">@include('icons.close', ['class' => 'fill-current width-4 height-4'])</span>
        </button>
    </div>
@endif