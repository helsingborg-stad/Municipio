@link(['href' => $homeUrl, 'classList' => ['c-header__logotype-container', 'u-margin__right--auto', 'u-display--flex', 'u-no-decoration']])
    @logotype([
        'src'=> $logotype,
        'alt' => $lang->goToHomepage,
        'classList' => ['c-nav__logo', 'c-header__logotype'],
        'context' => ['site.header.logo', 'site.header.casual.logo']
    ])
    @endlogotype
@endlink