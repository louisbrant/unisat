<div class="card border-0 rounded-top shadow-sm overflow-hidden">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col d-flex align-items-center">
                <div class="d-flex align-items-center font-weight-medium py-1">
                    <div class="d-flex align-items-center {{ (__('lang_dir') == 'rtl' ? 'ml-3' : 'mr-3') }}" data-tooltip="true" title="{{ __($document->template->name) }}">
                        @if($document->template->isCustom())
                            <span class="width-4 height-4">
                                <span class="position-absolute width-4 height-4 d-flex align-items-center justify-content-center user-select-none">{{ $document->template->icon }}</span>
                            </span>
                        @else
                            @include('icons.' . $document->template->icon, ['class' => 'fill-current width-4 height-4 text-' . categoryColor($document->template->category_id)])
                        @endif
                    </div>

                    <div class="d-flex align-items-center text-truncate">
                        <a href="{{ route('documents.show', $document->id) }}" class="text-body text-truncate">{{ $document->name }}</a>

                        @if($document->favorite) <div class="d-flex flex-shrink-0 width-4 height-4 text-warning {{ (__('lang_dir') == 'rtl' ? 'mr-2' : 'ml-2') }}" data-tooltip="true" title="{{ __('Favorite') }}">@include('icons.star', ['class' => 'fill-current width-4 height-4 flex-shrink-0'])</div> @endif
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="form-row">
                    <div class="col">
                        <a href="{{ route('documents.show', $document->id) }}" class="btn btn-sm d-flex align-items-center" data-tooltip="true" title="{{ __('View') }}">
                            @include('icons.eye', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                        </a>
                    </div>
                    <div class="col">
                        <a href="{{ route('documents.edit', $document->id) }}" class="btn btn-sm d-flex align-items-center" data-tooltip="true" title="{{ __('Edit') }}">
                            @include('icons.edit', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                        </a>
                    </div>
                    <div class="col">
                        <button class="btn btn-sm d-flex align-items-center" data-tooltip-copy="true" title="{{ __('Copy') }}" data-text-copy="{{ __('Copy') }}" data-text-copied="{{ __('Copied') }}" data-clipboard="true" data-clipboard-target="#result-{{ $document->id }}">
                            @include('icons.content-copy', ['class' => 'fill-current width-4 height-4 text-secondary'])&#8203;
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-3">
        <div class="form-group mb-0">
            <div id="toolbar-{{ $document->id }}" class="border-0 p-0"></div>
            @include('shared.editor.content', ['id' => $document->id, 'text' => $document->result])
        </div>
    </div>
    <div class="card-footer text-muted small">
        <div class="row">
            <div class="col-auto">
                {{ __(':number words', ['number' => number_format($document->words, 0, __('.'), __(','))]) }}
            </div>

            <div class="col-auto">
                {{ __(':number characters', ['number' => number_format(mb_strlen($document->result), 0, __('.'), __(','))]) }}
            </div>
        </div>
    </div>
</div>
