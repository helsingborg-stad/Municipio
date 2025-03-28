@extends('templates.header', ['classList' => ['c-header','c-header--casual']])

@section('primary-navigation')

    <div class="c-header__menu c-header__menu--primary">
        @element([
            'baseClass' => 'o-container',
            'classList' => ['o-container', 'c-header__flex-content'],
            'context' => ['site.header.casual-container', 'site.header.container']
        ])
            {{-- Header logo --}}
            @link(['id' => 'header-logotype', 'href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex', 'u-no-decoration']])
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

            {{-- Primary menu --}}
            @includeWhen(
                !empty($primaryMenu['items']), 
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
            @includeWhen(!empty($siteselectorMenu['items']), 'partials.navigation.siteselector')

            {{-- Search form in header --}}
            @includeWhen($showHeaderSearchDesktop, 'partials.search.header-search-form')

            @includeWhen(
            $showHeaderSearchMobile, 
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

            {{-- User account --}}
            @includeIf('user.account')

            {{-- Language selector --}}
            @includeWhen(!empty($languageMenu['items']), 'partials.header.components.language')

            {{-- Hambuger menu trigger --}}
            @includeIf('partials.navigation.trigger.megamenu', ['context' => ['site.header.megamenu-trigger', 'site.header.casual.megamenu-trigger']])

            {{-- Drawer menu --}}
            @includeIf('partials.navigation.drawer')

            {{-- User (login/logout) --}}
            @include('partials.header.components.user', ['classList' => ['u-order--11']])

        @endelement

    </div>

    @includeIf('partials.navigation.megamenu')
@stop