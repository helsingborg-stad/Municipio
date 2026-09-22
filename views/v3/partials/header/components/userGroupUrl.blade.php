@if($userGroup !== null && $userGroup->url)
  @button([
    'text' => $userGroup->shortname ?? $userGroup->group->name ?? '',
    'color' => $buttonAppearance['color'],
    'icon' => 'real_estate_agent',
    'style' => $buttonAppearance['style'],
    'size' => $buttonAppearance['size'],
    'reversePositions' => true,
    'href' => $userGroup->url,
  ])
  @endbutton
@endif