@extends('templates.header', ['classList' => ['c-header', 'c-header--business']])


@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container c-header__flex-content">

            {{-- Header logo --}}
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
                        'context' => ['site.header.logo', 'site.header.business.logo']
                    ])
                    @endlogotype
                @endif
            @endlink

            {{-- Tab menu items --}}
            @includeWhen($tabMenuItems, 'partials.navigation.tabs')

            {{-- Siteselector menu items --}}
            @includeWhen($siteselectorMenuItems, 'partials.navigation.siteselector')
            
            {{-- Search form in header --}}
            @includeWhen($showHeaderSearch, 'partials.search.header-search-form')

            {{-- User account --}}
            @includeIf('user.account')
            
            {{-- Language selector --}}
            @if (!empty($languageMenuItems))
                <div class="site-language-menu" js-toggle-item="language-menu-toggle" js-toggle-class="is-expanded">
                    @includeIf('partials.navigation.trigger.language')
                    @includeIf('partials.navigation.language')
                </div>
            @endif
            
            {{-- Hambuger menu trigger --}}
            @includeIf('partials.navigation.trigger.megamenu', ['context' => ['site.header.megamenu-trigger', 'site.header.business.megamenu-trigger']])
            
            {{-- Drawer menu --}}
            @includeIf('partials.navigation.drawer')

        </div>
    </div>

    {{-- Mega menu --}}
    @includeIf('partials.navigation.megamenu')

    @includeWhen(
        $showMobileSearch, 
        'partials.search.mobile-search-form',[
            'classList' => [
                'search-form',
                'u-padding__y--2', 
                'u-padding__x--3', 
                'u-width--auto',
                'u-display--none@lg',
                'u-display--none@xl',
                'u-print-display--none'
            ]
        ]
    )

@stop

@section('secondary-navigation')
    {{-- Primary menu --}}
    @if (!empty($primaryMenuItems))
        <div class="c-header__menu c-header__menu--secondary u-display--none@xs u-display--none@sm u-display--none@md u-print-display--none">
            <div class="o-container">
                @includeIf(
                    'partials.navigation.primary', 
                    [
                        'context' => [
                            'site.header.nav', 
                            'site.header.business.nav'
                        ],
                        'classList' => [
                            'u-flex-wrap--no-wrap', 
                        ]
                    ]
                )
            </div>
        </div>
    @endif
@stop