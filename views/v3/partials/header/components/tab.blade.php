@if(!empty($tabMenu['items']))
    @nav([
        'id' => 'tabs',
        'items' => $tabMenu['items'],
        'direction' => 'horizontal',
        'includeToggle' => false,
        'allowStyle' => true,
        'buttonColor' => $menuItem->getButtonColor(),
        'buttonStyle' => $menuItem->getButtonStyle(),
        'buttonSize' => $menuItem->getButtonSize(),
        'height' => 'sm',
        'classList' => $menuItem->getCssClasses()
    ])
    @endnav
@endif