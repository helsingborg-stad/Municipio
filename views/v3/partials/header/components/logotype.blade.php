@link(['id' => 'header-logotype', 'href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex', 'u-no-decoration']])
    @if($headerBrandEnabled && !$headerData['hasSeparateBrandText'])
        @brand([
            'logotype' => [
                'src'=> $logotype,
                'alt' => $lang->goToHomepage
            ],
            'classList' => ['c-nav__logo', 'c-header__logotype'],
            'text' => $brandText,
        ])
        @endbrand
    @else
        @logotype([
            'src'=> $logotype,
            'alt' => $lang->goToHomepage,
            'classList' => ['c-nav__logo', 'c-header__logotype'],
            'context' => ['site.header.logo']
        ])
        @endlogotype
    @endif
@endlink