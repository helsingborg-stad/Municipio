
  
@form([
    'id'        => 'mobile-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['u-display--none@lg', 'u-margin__bottom--0']
])
  <div class="o-container u-padding__y--1">
    <div class="o-grid o-grid--no-gutter o-grid--no-margin">
      <div class="o-grid-12">
        @group(['direction' => 'horizontal', 'classList' => ['u-margin--auto', 'u-box-shadow--1']])
            @field([
                'id' => 'mobile-search-form--field',
                'type' => 'text',
                'attributeList' => [
                    'type' => 'search',
                    'name' => 's',
                    'required' => false,
                ],
                'placeholder' => $lang->search,
                'classList' => ['u-flex-grow--1', 'u-box-shadow--none', 'u-overflow--hidden', 'u-color__text--darkest'],
                'size' => 'sm',
                'radius' => 'sm',
                'icon' => ['icon' => 'search', 'color' => 'default', 'classList' => ['u-color__text--darkest'] ]
            ])
            @endfield

            @button([
                'id' => 'mobile-search-form--submit',
                'text' => $lang->search,
                'color' => 'default',
                'type' => 'submit',
                'size' => 'sm',
                'attributeList' => [
                    'id' => 'header-search-form--submit'
                ], 
                'classList' => ['u-box-shadow--none', 'u-color__text--darkest']
            ])
            @endbutton

        @endgroup
      </div>
    </div>
  </div>
@endform
