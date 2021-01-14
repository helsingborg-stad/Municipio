
  
@form([
    'id'        => 'mobile-search-form',
    'method'    => 'get',
    'action'    => $homeUrl,
    'classList' => ['u-color__bg--light', 'u-display--none@lg', 'u-print-display--none']
])
  <div class="o-container u-padding__y--1">
    <div class="o-grid o-grid--no-gutter o-grid--no-margin">
      <div class="o-grid-12">
        @group(['direction' => 'horizontal', 'classList' => ['u-margin--auto']])
            @field([
                'id' => 'mobile-search-form--field',
                'type' => 'text',
                'attributeList' => [
                    'type' => 'search',
                    'name' => 's',
                    'required' => false,
                ],
                'label' => $lang->search,
                'classList' => ['u-flex-grow--1', 'u-box-shadow--none', 'u-overflow--hidden'],
                'size' => 'sm',
                'radius' => 'sm',
                'icon' => ['icon' => 'search']
            ])
            @endfield

            @button([
                'id' => 'mobile-search-form--submit',
                'text' => $lang->search,
                'color' => 'default',
                'type' => 'basic',
                'size' => 'sm',
                'attributeList' => [
                    'id' => 'header-search-form--submit'
                ], 
                'classList' => ['u-box-shadow--none']
            ])
            @endbutton

        @endgroup
      </div>
    </div>
  </div>
@endform
