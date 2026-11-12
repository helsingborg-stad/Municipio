{{-- TODO: Must implement dynamic ids --}}
@form([
    'id'        => 'header-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => array_merge([
        'search-form',
        'u-print-display--none'
    ], $menuItem->getCssClasses())
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
