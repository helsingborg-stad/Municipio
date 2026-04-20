@form([
    'id'        => 'header-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => $classList ?? [
        'search-form', 
        'u-margin__bottom--0',
        'u-print-display--none', 
        'u-display--flex@lg', 
        'u-display--flex@xl', 
        'u-display--none@xs', 
        'u-display--none@sm', 
        'u-display--none@md'
    ]
])
    @group(['direction' => 'horizontal', 'classList' => ['u-margin--auto']])
        @field([
            'id'            => 'header-search-form__field',
            'type'          => 'search',
            'name'          => 's',
            'required'      => false,
            'size'          => 'sm',
            'label'         => $lang->searchQuestion,
            'hideLabel'     => true,
            'icon'          => ['icon' => 'search'],
        ])
        @endfield

        @button([
            'id'            => 'header-search-form__submit',
            'text'          => $lang->search,
            'type'          => 'submit',
            'size'          => 'sm',
            'attributeList' => [
                'aria-label' => $lang->search,
            ],
        ])
        @endbutton

    @endgroup
@endform
