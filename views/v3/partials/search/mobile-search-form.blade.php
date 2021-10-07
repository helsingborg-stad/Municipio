
@form([
  'id'        => 'mobile-search-form',
  'method'    => 'get',
  'action'    => $homeUrl,
  'classList' => [
    'u-margin__top--2',
    'u-width--100'
  ]
])
  @group(['direction' => 'horizontal', 'classList' => ['u-margin--auto']])
      @field([
          'id'            => 'mobile-search-form--field',
          'type'          => 'text',
          'placeholder'   => $lang->search,
          'size'          => 'sm',
          'radius'        => 'sm',
          'label'         => $lang->searchQuestion,
          'hideLabel'     => true,
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
          'id'            => 'mobile-search-form--submit',
          'text'          => $lang->search,
          'color'         => 'default',
          'type'          => 'submit',
          'size'          => 'sm',
          'attributeList' => [
              'id'            => 'mobile-search-form--submit'
          ],
          'classList'     => ['u-rounded__right--8']
      ])
      @endbutton

  @endgroup
@endform
