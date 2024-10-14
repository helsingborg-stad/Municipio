<div class="collapsible-search-form">
    @form([
        'method'    => 'get',
        'action'    => $homeUrl,
        'classList' => ['collapsible-search-form__form'],
        'attributeList' => ['tab-index' => '-1', 'aria-hidden' => 'true'],
    ])
        @group(['direction' => 'horizontal', 'classList' => ['collapsible-search-form__group']])
            @field([
                'id'            => 'collapsible-search-form__field',
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
                ]
            ])
            @endfield

            <button type="button" aria-label="{{ $lang->search }}" class="collapsible-search-form__submit-icon">
                @icon([
                    'icon' => 'search',
                    'size' => 'md',
                ])
                @endicon
            </button>
        @endgroup

        <button type="button" class="collapsible-search-form__close-button" aria-label="{{ __('Close search', 'municipio') }}">
            @icon([
                'icon' => 'close',
                'size' => 'md',
            ])
            @endicon
        </button>
    @endform

    @button([
        'classList' => ['collapsible-search-form__trigger-button'],
        'text' => __('Search', 'municipio'),
        'style' => 'basic',
        'icon' => 'search',
        'reversePositions' => true,
        'attributeList' => ['aria-expanded' => 'false', 'aria-label' => $lang->search],
    ])
    @endbutton
</div>