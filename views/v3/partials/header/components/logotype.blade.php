@link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex', 'u-no-decoration']])
    @if($headerBrandEnabled)
        @brand([
            'logotype' => [
                'src'=> $logotype,
                'alt' => $lang->goToHomepage
            ],
            'text' => $brandText,
        ])
        @endbrand
    @else
        @logotype([
            'src'=> $logotype,
            'alt' => $lang->goToHomepage,
            'classList' => ['c-nav__logo', 'c-header__logotype'],
            'context' => ['site.header.logo', 'site.header.casual.logo']
        ])
        @endlogotype
    @endif
@endlink