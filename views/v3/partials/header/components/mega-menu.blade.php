@includeWhen(!empty($megaMenu['items']), 'partials.navigation.trigger.megamenu', [
    'context' => $context ?? [],
    'buttonAppearance' => $buttonAppearance ?? null,
    'classList' => [
        'mega-menu-trigger'
    ]
])