@php
    $parameters[] = [
        'name' => 'name',
        'type' => $type,
        'format' => 'string',
        'description' => __('The chat name.')
    ];

    $parameters[] = [
        'name' => 'behavior',
        'type' => 0,
        'format' => 'string',
        'description' => __('The behavior of the assistant.')
    ];

    if($type) {
    } else {
        $parameters[] = [
            'name' => 'favorite',
            'type' => 0,
            'format' => 'boolean',
            'description' => __('Whether the chat is favorite or not.')
        ];
    }
@endphp

@include('developers.parameters')
