@includeWhen(!empty($megaMenuItems), 'partials.navigation.trigger.megamenu', [
    'context' => $context ?? [],
    'classList' => [
        'mega-menu-trigger'
    ]
])