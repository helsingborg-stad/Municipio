@form([
    'id'        => 'hero-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['c-form--hidden', 'u-box-shadow--5', 'u-print-display--none']
])
    @group(['direction' => 'horizontal'])
        @field([
            'id' => 'hero-search-form__field',
            'type' => 'search',
            'name' => 's',
            'required' => false,
            'label' => $lang->searchOn . " " . $siteName,
            'placeholder' => $lang->searchOn . " " . $siteName,
            'classList' => ['u-flex-grow--1'],
            'size' => 'lg',
            'radius' => 'xs',
            'icon' => ['icon' => 'search']
        ])
        @endfield
        @button([
            'id' => 'hero-search-form__submit',
            'text' => $lang->search,
            'color' => 'default',
            'type' => 'submit',
            'size' => 'lg',
            'attributeList' => [
                'aria-label' => $lang->search,
            ]
        ])
        @endbutton
    @endgroup
@endform
