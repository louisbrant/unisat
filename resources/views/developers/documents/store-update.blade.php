@php
    $parameters[] = [
        'name' => 'name',
        'type' => $type,
        'format' => 'string',
        'description' => __('The document name.')
    ];

    if($type) {
        $parameters[] = [
            'name' => 'prompt',
            'type' => 1,
            'format' => 'string',
            'description' => __('The instructions for the AI.')
        ];
        $parameters[] = [
            'name' => 'creativity',
            'type' => 1,
            'format' => 'float',
            'description' => __('The creative level of the result.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. config('completions.creativities')[$item] .'</code>', 'name' => '<span class="font-weight-medium">'.__(Str::ucfirst($item)).'</span>']); }, array_keys(config('completions.creativities'))))
                ]) . ' ' . __('Defaults to: :value.', ['value' => '<code>0.5</code>'])
        ];
    } else {
        $parameters[] = [
            'name' => 'result',
            'type' => 0,
            'format' => 'string',
            'description' => __('The document result.')
        ];

        $parameters[] = [
            'name' => 'favorite',
            'type' => 0,
            'format' => 'boolean',
            'description' => __('Whether the document is favorite or not.')
        ];
    }
@endphp

@include('developers.parameters')
