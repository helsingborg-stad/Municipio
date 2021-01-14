@form([
    'id'        => 'hero-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['c-form--hidden', 'u-box-shadow--5', 'u-print-display--none']
])
    @group(['direction' => 'horizontal'])
        @field([
            'id' => 'search-form--field',
            'type' => 'text',
            'attributeList' => [
                'type' => 'search',
                'name' => 's',
                'required' => false,
            ],
            'label' => $lang->searchOn . " " . $siteName,
            'classList' => ['u-flex-grow--1'],
            'size' => 'lg',
            'radius' => 'xs',
            'icon' => ['icon' => 'search']
        ])
        @endfield
        @button([
            'id' => 'search-form--submit',
            'text' => $lang->search,
            'color' => 'default',
            'type' => 'basic',
            'size' => 'lg',
            'attributeList' => [
                'id' => 'search-form--submit'
            ]
        ])
        @endbutton
    @endgroup
@endform
