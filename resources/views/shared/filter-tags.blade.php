@php
    $filterIcons = [
        'user' => 'account-circle',
        'template' => 'apps'
    ];
@endphp

@foreach($filters as $key => $value)
    <div class="input-group input-group-sm mb-3 mb-md-0 {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroup-sizing-sm">@include('icons.' . $filterIcons[$key], ['class' => 'fill-current width-4 height-4-sm'])</span>
        </div>
        <input type="text" class="form-control" value="{{ $value }}" readonly>
    </div>
    <input type="hidden" class="form-control" name="{{ $key }}_id" value="{{ request()->input($key.'_id') }}">
@endforeach
