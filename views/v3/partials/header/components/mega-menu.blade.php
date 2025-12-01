@includeWhen(!empty($megaMenu['items']), 'partials.navigation.trigger.megamenu', [
    'context' => $context ?? [],
    'classList' => [
        'mega-menu-trigger'
    ]
])