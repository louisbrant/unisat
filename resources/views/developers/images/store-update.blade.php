@php
    $parameters[] = [
        'name' => 'name',
        'type' => $type,
        'format' => 'string',
        'description' => __('The image name.')
    ];

    if($type) {
        $parameters[] = [
            'name' => 'description',
            'type' => 1,
            'format' => 'string',
            'description' => __('The image description for the AI.')
        ];
        $parameters[] = [
            'name' => 'resolution',
            'type' => 1,
            'format' => 'string',
            'description' => __('The resolution of the image.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.resolutions')[$item]).'</span>']); }, array_keys(config('images.resolutions'))))
                ])
        ];
        $parameters[] = [
            'name' => 'style',
            'type' => 0,
            'format' => 'string',
            'description' => __('The style of the image.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.styles')[$item]).'</span>']); }, array_keys(config('images.styles'))))
                ])
        ];
        $parameters[] = [
            'name' => 'medium',
            'type' => 0,
            'format' => 'string',
            'description' => __('The medium of the image.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.mediums')[$item]).'</span>']); }, array_keys(config('images.mediums'))))
                ])
        ];
        $parameters[] = [
            'name' => 'filter',
            'type' => 0,
            'format' => 'string',
            'description' => __('The filter of the image.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.filters')[$item]).'</span>']); }, array_keys(config('images.filters'))))
                ])
        ];
    } else {
        $parameters[] = [
            'name' => 'favorite',
            'type' => 0,
            'format' => 'boolean',
            'description' => __('Whether the image is favorite or not.')
        ];
    }
@endphp

@include('developers.parameters')
