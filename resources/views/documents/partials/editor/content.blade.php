<div id="result-{{ $document->id }}" class="border-0 rounded-0 p-1">{!! clean(encodeQuill($document->result)) !!}</div>

<script src="{{ asset('js/app.extras.js') }}" defer></script>

<script>
    'use strict';

    window.addEventListener('DOMContentLoaded', function () {
        var container = document.getElementById('result-{{ $document->id }}');
        var options = {
            modules: {
                toolbar: '#toolbar-' + {{ $document->id }}
            },
            theme: 'snow',
            @if (request()->is('templates/*'))
                readOnly: true,
            @endif
        };

        var editor = new Quill(container, options);

        editor.on('text-change', function () {
            document.querySelector('#i-result-' + {{ $document->id }}) ? document.querySelector('#i-result-' + {{ $document->id }}).value = editor.root.innerHTML.trim() : '';
        });
    });
</script>
