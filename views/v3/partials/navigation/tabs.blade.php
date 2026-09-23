@if(!empty($tabMenu['items']))
    @nav([
        'id' => 'tabs',
        'items' => $tabMenu['items'],
        'direction' => 'horizontal',
        'includeToggle' => false,
        'allowStyle' => true,
        'buttonColor' => $buttonAppearance['color'],
        'buttonStyle' => $buttonAppearance['style'],
        'buttonSize' => $buttonAppearance['size'],
        'height' => 'sm',
        'classList' => [
            'u-width--auto',
            'u-display--none@xs',
            'u-display--none@sm',
            'u-display--none@md'
        ]
    ])
    @endnav
@endif