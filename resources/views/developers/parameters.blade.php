<div class="list-group list-group-flush mb-n3">
    <div class="list-group-item px-0 text-muted">
        <div class="row align-items-center">
            <div class="col-12 col-lg-3">{{ __('Parameter') }}</div>
            <div class="col-12 col-lg-2">{{ __('Type') }}</div>
            <div class="col-12 col-lg-7">{{ __('Description') }}</div>
        </div>
    </div>

    @foreach($parameters as $parameter)
        <div class="list-group-item px-0">
            <div class="row align-items-center">
                <div class="col-12 col-lg-3"><code>{{ $parameter['name'] }}</code></div>
                <div class="col-12 col-lg-2">@if($parameter['type'])<span class="badge badge-danger">{{ mb_strtolower(__('Required')) }}</span>@else<span class="badge badge-primary">{{ mb_strtolower(__('Optional')) }}</span>@endif <span class="badge badge-secondary">{{ $parameter['format'] }}</span></div>
                <div class="col-12 col-lg-7">{!! $parameter['description']  !!}</div>
            </div>
        </div>
    @endforeach
</div>