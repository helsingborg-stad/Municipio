<div class="collapsible-search-form">
    @form([
        'method'    => 'get',
        'action'    => $homeUrl,
        'classList' => [
            'collapsible-search-form__form',
            'u-visibility--hidden'
        ],
        'attributeList' => ['tabindex' => '-1', 'aria-hidden' => 'true'],
    ])
        @group(['direction' => 'horizontal', 'classList' => ['collapsible-search-form__group']])
            @field([
                'type'          => 'text',
                'name'          => 's',
                'required'      => false,
                'radius'        => 'sm',
                'label'         => $lang->searchQuestion,
                'hideLabel'     => true,
                'icon'          => ['icon' => ''],
                'classList'     => [
                    'u-flex-grow--1',
                    'u-box-shadow--1',
                ],
            ])
            @endfield

            @button([
                'classList' => [
                    'collapsible-search-form__submit-icon'
                ],
                'style' => 'primary',
                'type' => 'submit',
                'icon' => 'search',
                'attributeList' => [
                    'aria-label' => $lang->search,
                ],
            ])
            @endbutton
        @endgroup

        @button([
            'classList' => [
                'collapsible-search-form__close-button'
            ],
            'style' => 'primary',
            'icon' => 'close',
            'attributeList' => [
                'aria-label' => __('Close search', 'municipio'),
            ]
        ])
        @endbutton
    @endform

    @button([
        'classList' => ['collapsible-search-form__trigger-button', 's-header-button'],
        'text' => __('Search', 'municipio'),
        'style' => 'basic',
        'icon' => 'search',
        'reversePositions' => true,
        'attributeList' => ['aria-expanded' => 'false', 'aria-label' => $lang->search],
    ])
    @endbutton
</div>