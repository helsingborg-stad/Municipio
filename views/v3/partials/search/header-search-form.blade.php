
@form([
    'id'        => 'header-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['u-print-display--none','u-margin__left--2', 'u-display--flex@lg u-display--flex@xl u-display--none@xs u-display--none@sm u-display--none@md']
])
    @group(['direction' => 'horizontal', 'classList' => ['u-margin--auto']])
        @field([
            'id'            => 'header-search-form--field',
            'type'          => 'text',
            'placeholder'   => $lang->search,
            'size'          => 'sm',
            'radius'        => 'sm',
            'icon'          => ['icon' => 'search'],
            'classList'     => [
                'u-flex-grow--1',
                'u-box-shadow--1',
                'u-rounded__left--8',
                'u-overflow--hidden'
            ],
            'attributeList' => [
                'type'          => 'search',
                'name'          => 's',
                'required'      => false,
            ]
        ])
        @endfield

        @button([
            'id'            => 'search-form--submit',
            'text'          => $lang->search,
            'color'         => 'default',
            'type'          => 'submit',
            'size'          => 'sm',
            'attributeList' => [
                'id'            => 'header-search-form--submit'
            ], 
            'classList'     => ['u-rounded__right--8']
        ])
        @endbutton

    @endgroup
@endform
