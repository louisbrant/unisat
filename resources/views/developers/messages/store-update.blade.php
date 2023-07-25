@php
    $parameters[] = [
        'name' => 'name',
        'type' => $type,
        'format' => 'string',
        'description' => __('The chat name.')
    ];

    if($type) {
        $parameters[] = [
            'name' => 'chat_id',
            'type' => $type,
            'format' => 'string',
            'description' => __('The chat ID the message to be saved under.')
        ];
        $parameters[] = [
            'name' => 'role',
            'type' => $type,
            'format' => 'string',
            'description' => __('The role of the message.') . ' ' . __('Possible values are: :values.', [
                'values' => implode(', ', [
                    __(':value for :name', ['value' => '<code>user</code>', 'name' => '<span class="font-weight-medium">'.__('User').'</span>']),
                    __(':value for :name', ['value' => '<code>assistant</code>', 'name' => '<span class="font-weight-medium">'.__('Assistant').'</span>'])
                    ])
                ])
        ];
    } else {
    }
@endphp

@include('developers.parameters')
