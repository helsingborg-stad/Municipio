@if($userGroup !== null && $userGroup->url)
  @button([
    'text' => $userGroup->shortname ?? $userGroup->group->name ?? '',
    'color' => $menuItem->getButtonColor(),
    'icon' => 'real_estate_agent',
    'style' => $menuItem->getButtonStyle(),
    'size' => $menuItem->getButtonSize(),
    'reversePositions' => true,
    'href' => $userGroup->url,
    'classList' => $menuItem->getCssClasses()
  ])
  @endbutton
@endif