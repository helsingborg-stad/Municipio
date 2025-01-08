@paper(['padding' => '4', 'classList' => ['u-display--flex', 'u-flex-direction--column', 'u-margin__bottom--12']]) 
  @typography([
      'element' => 'h2',
      'variant' => 'h2',
      'classList' => [
        'u-margin__bottom--2',
        'u-margin__left--auto',
        'u-margin__right--auto',
      ]
  ])
    Härligt att se dig, {{ $userDetails->firstname }}
  @endtypography

  @button([
      'href' => $userGroupUrl,
      'text' => 'Gå till ' . $userGroup->name,
      'icon' => 'badge',
      'color' => 'primary',
      'style' => 'filled',
      'size' => 'sm',
      'reversePositions' => 'true',
      'classList' => [
        'u-margin__left--auto',
        'u-margin__right--auto',
      ]
  ])
  @endbutton
@endpaper