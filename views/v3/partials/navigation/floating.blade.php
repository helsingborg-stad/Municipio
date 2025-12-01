
@if(!empty($floatingMenu['items']))
    @if(count($floatingMenu['items']) == 1)
        <!-- Single link item -->
        @button([
            'id' => 'fab-item',
            'classList' => ['u-fixed--bottom-right', 'u-margin--2'],
            'icon' => !empty($floatingMenu['items'][0]['icon']['icon']) ? $floatingMenu['items'][0]['icon']['icon'] : $floatingMenuLabels->buttonIcon,
            'size' => 'md',
            'color' => 'primary',
            'text' => $floatingMenu['items'][0]['label'] ? $floatingMenu['items'][0]['label'] : $floatingMenuLabels->buttonLabel,
            'reversePositions' => true,
            'href' => $floatingMenu['items'][0]['href']
        ])
        @endbutton
    @else
        <!-- Multiple link items -->
        @fab([
            'id' => 'fab-item',
            'position' => 'bottom-right',
            'heading' => $floatingMenuLabels->heading,
            'button' => [
                'icon' => $floatingMenuLabels->buttonIcon,
                'size' => 'md',
                'color' => 'primary',
                'text' => $floatingMenuLabels->buttonLabel,
                'reversePositions' => true,
            ],
            'closeLabel' => $lang->close,
            'closeIcon' =>'close',
            'context' => ['site.navigation.floating']
        ])
            @nav([
                'id' => 'menu-floating',
                'items' => $floatingMenu['items'],
                'direction' => 'vertical',
                'includeToggle' => false,
                'classList' => ['s-nav-floating'],
                'context' => ['site.navigation.floating.nav'],
                'height' => 'sm',
                'expandLabel' => $lang->expand
            ])
            @endnav
        @endfab
    @endif
@endif