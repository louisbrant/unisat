<div class="card border-0 rounded-top shadow-sm overflow-hidden">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="d-flex align-items-center font-weight-medium py-1">
                    <div class="d-flex align-items-center text-truncate">
                        <a href="{{ route('images.show', $image->id) }}" class="text-body text-truncate">{{ $image->name }}</a>

                        @if($image->favorite) <div class="d-flex flex-shrink-0 width-4 height-4 text-warning {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Favorite') }}">@include('icons.star', ['class' => 'fill-current width-4 height-4 flex-shrink-0'])</div> @endif
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        <a href="{{ route('images.show', $image->id) }}" class="btn btn-sm d-flex align-items-center" data-tooltip="true" title="{{ __('View') }}">
                            @include('icons.eye', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('images.edit', $image->id) }}" class="btn btn-sm d-flex align-items-center" data-tooltip="true" title="{{ __('Edit') }}">
                            @include('icons.edit', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ $image->url }}" class="btn btn-sm d-flex align-items-center" data-tooltip="true" title="{{ __('Download') }}" download="{{ $image->name }}">
                            @include('icons.export', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-3">
        <img src="{{ $image->url }}" class="w-100">
    </div>
    <div class="card-footer text-muted small">
        <div class="row">
            <div class="col-auto">
                <div data-tooltip="true" title="{{ __('Resolution') }}">{{ config('images.resolutions')[$image->resolution] }}</div>
            </div>
        </div>
    </div>
</div>
