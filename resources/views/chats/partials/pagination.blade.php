@if ($paginator->hasPages())
    <nav class="d-block">
        <ul class="pagination pagination-sm mb-0">
            {{-- Next Page Link --}}
            @if (!$paginator->onFirstPage() && isset($next))
                <li class="page-item d-flex align-items-center justify-content-center w-100">
                    <a class="page-link d-flex align-items-center" href="{{ $paginator->previousPageUrl() }}" rel="next" data-tooltip="true" title="{{ __('Newer') }}">@include('icons.expand-more', ['class' => 'fill-current width-3 height-3'])</a>
                </li>
            @endif

            {{-- Previous Page Link --}}
            @if ($paginator->hasMorePages() && isset($previous))
                <li class="page-item d-flex align-items-center justify-content-center w-100">
                    <a class="page-link d-flex align-items-center" href="{{ $paginator->nextPageUrl() }}" rel="prev" data-tooltip="true" title="{{ __('Older') }}">@include('icons.expand-less', ['class' => 'fill-current width-3 height-3'])</a>
                </li>
            @endif
        </ul>
    </nav>
@endif
