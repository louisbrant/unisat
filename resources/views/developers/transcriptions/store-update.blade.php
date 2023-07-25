@php
    $parameters[] = [
        'name' => 'name',
        'type' => $type,
        'format' => 'string',
        'description' => __('The transcription name.')
    ];

    if($type) {
        $parameters[] = [
            'name' => 'file',
            'type' => 1,
            'format' => 'file',
            'description' => __('The audio file.')
        ];
        $parameters[] = [
            'name' => 'description',
            'type' => 0,
            'format' => 'string',
            'description' => __('The description of the audio file.')
        ];
        $parameters[] = [
            'name' => 'language',
            'type' => 0,
            'format' => 'string',
            'description' => __('The language of the audio file.') . ' ' . __('The value must be in :standard standard.', ['standard' => '<a href="https://wikipedia.org/wiki/ISO_3166-1_alpha-2#Officially_assigned_code_elements" target="_blank" rel="nofollow noreferrer noopener">ISO 3166-1 alpha-2</a>'])
        ];
    } else {
        $parameters[] = [
            'name' => 'result',
            'type' => 0,
            'format' => 'string',
            'description' => __('The transcription result.')
        ];

        $parameters[] = [
            'name' => 'favorite',
            'type' => 0,
            'format' => 'boolean',
            'description' => __('Whether the transcription is favorite or not.')
        ];
    }
@endphp

@include('developers.parameters')
