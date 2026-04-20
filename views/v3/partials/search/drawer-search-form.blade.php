@form([
  'id'        => 'drawer-search-form',
  'method'    => 'get',
  'action'    => $homeUrl,
  'classList' => $classList
])
    @group(['direction' => 'horizontal', 'classList' => ['u-margin--auto', 'u-width--100']])
        @field([
            'id'            => 'drawer-search-form__field',
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
            'id'            => 'drawer-search-form__submit',
            'text'          => $lang->search,
            'color'         => 'default',
            'type'          => 'submit',
            'size'          => 'sm',
            'attributeList' => [
                'aria-label' => $lang->search,
            ],
        ])
        @endbutton
    @endgroup
@endform
