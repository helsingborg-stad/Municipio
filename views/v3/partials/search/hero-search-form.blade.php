@form([
    'id'        => 'hero-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['search-form', 'c-form--hidden', 'u-box-shadow--5', 'u-print-display--none'],
    'context' => ['hero.search.form']
    ])
    @group([
        'id' => 'hero-search-form__wrapper',
    ])
        @field([
            'id' => 'hero-search-form__field',
            'type' => 'search',
            'name' => 's',
            'required' => false,
            'label' => $lang->searchOn . " " . $siteName,
            'placeholder' => $lang->searchOn . " " . $siteName,
            'size' => 'lg',
            'radius' => 'xs',
            'icon' => [
                'icon' => 'search', 
                'classList' => ['u-display--none@xs', 'c-field__icon']
            ]
        ])
        @endfield
        @button([
            'id' => 'hero-search-form__submit',
            'text' => $lang->search,
            'type' => 'submit',
            'size' => 'lg',
            'attributeList' => [
                'aria-label' => $lang->search,
            ],
            'disableColor' => false,
            'context' => ['hero.search.button'],

        ])
        @endbutton
    @endgroup
@endform
