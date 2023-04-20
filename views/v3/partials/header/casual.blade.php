@extends('templates.header', ['classList' => ['c-header','c-header--casual']])

@section('primary-navigation')

    <div class="c-header__menu c-header__menu--primary">

        <div class="c-header__flex-content o-container o-container--wide">
            
            {{-- Header logo --}}
            @link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex']])
                @logotype([
                    'src'=> $logotype,
                    'alt' => $lang->goToHomepage,
                    'classList' => ['c-nav__logo', 'c-header__logotype'],
                    'context' => ['site.header.logo', 'site.header.casual.logo']
                ])
                @endlogotype
            @endlink

            {{-- Primary menu --}}
            @includeWhen(
                !empty($primaryMenuItems), 
                'partials.navigation.primary', 
                [
                    'context' => [
                        'site.header.nav', 
                        'site.header.casual.nav'
                    ],
                    'classList' => [
                        'u-flex-wrap--no-wrap', 
                    ]
                ]
            )

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
            @includeIf('partials.navigation.trigger.hamburgermenu', ['context' => ['site.header.hamburgermenu-trigger', 'site.header.casual.hamburgermenu-trigger']])

            {{-- Drawer menu --}}
            @includeIf('partials.navigation.drawer')
            
        </div>
    </div>

    @includeIf('partials.navigation.hamburgermenu')
@stop