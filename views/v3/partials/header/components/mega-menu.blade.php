@includeWhen(!empty($megaMenu['items']), 'partials.navigation.trigger.megamenu', [
    'context' => $context ?? [],
    'buttonAppearance' => $buttonAppearance,
    'classList' => [
        'mega-menu-trigger'
    ]
])