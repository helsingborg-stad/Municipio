@if($userGroup !== null && $userGroup->url)
  @button([
    'text' => $userGroup->shortname ?? $userGroup->group->name ?? '',
    'color' => $buttonAppearance['color'] ?? 'inherit',
    'icon' => 'real_estate_agent',
    'style' => $buttonAppearance['style'] ?? 'basic',
    'size' => $buttonAppearance['size'] ?? 'md',
    'reversePositions' => true,
    'href' => $userGroup->url,
  ])
  @endbutton
@endif