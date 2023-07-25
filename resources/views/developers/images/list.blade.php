@php
    $parameters = [
        [
            'name' => 'search',
            'type' => 0,
            'format' => 'string',
            'description' => __('The search query.')
        ], [
            'name' => 'search_by',
            'type' => 0,
            'format' => 'string',
            'description' => __('Search by') . '. ' . __('Possible values are: :values.', [
                'values' => implode(', ', [
                    __(':value for :name', ['value' => '<code>name</code>', 'name' => '<span class="font-weight-medium">'.__('Name').'</span>'])
                    ])
                ]) .' ' . __('Defaults to: :value.', ['value' => '<code>name</code>'])
        ], [
            'name' => 'resolution',
            'type' => 0,
            'format' => 'string',
            'description' => __('Filter by resolution.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.resolutions')[$item]).'</span>']); }, array_keys(config('images.resolutions'))))
                ])
        ], [
            'name' => 'style',
            'type' => 0,
            'format' => 'string',
            'description' => __('Filter by style.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.styles')[$item]).'</span>']); }, array_keys(config('images.styles'))))
                ])
        ], [
            'name' => 'medium',
            'type' => 0,
            'format' => 'string',
            'description' => __('Filter by medium.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.mediums')[$item]).'</span>']); }, array_keys(config('images.mediums'))))
                ])
        ], [
            'name' => 'filter',
            'type' => 0,
            'format' => 'string',
            'description' => __('Filter by filter.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', array_map(function ($item) { return __(':value for :name', ['value' => '<code>'. $item .'</code>', 'name' => '<span class="font-weight-medium">'.__(config('images.filters')[$item]).'</span>']); }, array_keys(config('images.filters'))))
                ])
        ], [
            'name' => 'favorite',
            'type' => 0,
            'format' => 'boolean',
            'description' => __('Filter by favorite.')
        ], [
            'name' => 'sort_by',
            'type' => 0,
            'format' => 'string',
            'description' => __('Sort by') . '. ' . __('Possible values are: :values.', [
                'values' => implode(', ', [
                    __(':value for :name', ['value' => '<code>id</code>', 'name' => '<span class="font-weight-medium">'.__('Date created').'</span>']),
                    __(':value for :name', ['value' => '<code>name</code>', 'name' => '<span class="font-weight-medium">'.__('Name').'</span>'])
                    ])
                ]) .' ' . __('Defaults to: :value.', ['value' => '<code>id</code>'])
        ], [
            'name' => 'sort',
            'type' => 0,
            'format' => 'string',
            'description' => __('Sort') . '. ' . __('Possible values are: :values.', [
                'values' => implode(', ', [
                    __(':value for :name', ['value' => '<code>desc</code>', 'name' => '<span class="font-weight-medium">'.__('Descending').'</span>']),
                    __(':value for :name', ['value' => '<code>asc</code>', 'name' => '<span class="font-weight-medium">'.__('Ascending').'</span>'])
                    ])
                ]) .' ' . __('Defaults to: :value.', ['value' => '<code>desc</code>'])
        ], [
            'name' => 'per_page',
            'type' => 0,
            'format' => 'integer',
            'description' => __('Results per page') . '. '. __('Possible values are: :values.', [
                'values' => '<code>' . implode('</code>, <code>', [10, 25, 50, 100]) . '</code>'
                ]) .' ' . __('Defaults to: :value.', ['value' => '<code>'.config('settings.paginate').'</code>'])
        ]
    ];
@endphp

@include('developers.parameters')
