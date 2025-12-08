@if($userGroup !== null && $userGroup->url)
  @button([
    'text' => $userGroup->shortname ?? $userGroup->group->name ?? '',
    'color' => 'basic',
    'icon' => 'real_estate_agent',
    'style' => 'basic',
    'reversePositions' => true,
    'href' => $userGroup->url,
  ])
  @endbutton
@endif