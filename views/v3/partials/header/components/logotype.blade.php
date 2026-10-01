@link([
    'id' => 'header-logotype', 'href' => $homeUrl,
    'classList' => array_merge([
        'u-margin__right--auto',
        'u-display--flex',
        'u-no-decoration'
        ], $menuItem->getCssClasses() ?? [])
    ])
    @if($headerBrandEnabled && !$headerData['hasSeparateBrandText'])
        @brand([
            'logotype' => [
                'src'=> $logotype,
                'alt' => $lang->goToHomepage
            ],
            'classList' => ['c-nav__logo', 'c-header__logotype'],
            'text' => $brandText,
            'aspectRatio' => $headerData['logoScrollShrinkAspectRatio'] ?? null,
        ])
        @endbrand
    @else
        @logotype([
            'src'=> $logotype,
            'alt' => $lang->goToHomepage,
            'aspectRatio' => $headerData['logoScrollShrinkAspectRatio'] ?? null,
            'classList' => ['c-nav__logo', 'c-header__logotype'],
            'context' => ['site.header.logo']
        ])
        @endlogotype
    @endif
@endlink
