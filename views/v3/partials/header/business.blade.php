@extends('templates.header', ['classList' => ['c-header c-header--business']])

@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container">
            <div class="u-display--flex u-justify-content--space-between u-align-content--center u-align-items--center">
                
                @link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex']])
                    @logotype([
                        'src'=> $logotype->url,
                        'alt' => $lang->goToHomepage,
                        'classList' => ['c-nav__logo'],
                        'context' => ['site.header.logo', 'site.header.business.logo']
                    ])
                    @endlogotype
                @endlink

                @includeIf('partials.navigation.hamburgermenu-trigger', ['context' => ['site.header.hamburgermenutrigger', 'site.header.casual.hamburgermenutrigger']])

                @button([
                    'id' => 'mobile-menu-trigger-open',
                    'text' => $lang->menu,
                    'color' => 'default',
                    'style' => 'basic',
                    'icon' => 'keyboard_arrow_down',
                    'classList' => [
                        'mobile-menu-trigger',
                        'u-display--none@lg'
                    ],
                    'attributeList' => [
                        'aria-label' => $lang->menu,
                        'aria-controls' => "navigation",
                        'js-toggle-trigger' => 'js-drawer'
                    ],
                    'context' => ['site.header.menutrigger', 'site.header.business.menutrigger']
                ])
                @endbutton

                {{-- Tab menu items --}}
                @includeWhen($tabMenuItems, 'partials.navigation.tabs')

                {{-- Search form in header --}}
                @includeWhen($showHeaderSearch, 'partials.search.header-search-form')
                
                {{-- Translation menu --}}
                @if (!empty($languageMenuItems))
                    <div class="site-language-menu u-margin__left--1" js-toggle-item="language-menu-toggle" js-toggle-class="is-expanded">
                        @button([
                            'id' => '',
                            'color' => 'default',
                            'style' => 'basic',
                            'icon' => 'language',
                            'classList' => [
                                'site-language-menu-button'
                            ],
                            'attributeList' => [
                                'js-toggle-trigger' => 'language-menu-toggle',
                                'aria-label' => __("Select language", 'municipio')
                            ]
                        ])
                        @endbutton

                        @includeIf('partials.navigation.language')
                    </div>
                @endif

            </div>

        </div>

    </div>

    @includeWhen(
        $showMobileSearch, 
        'partials.search.mobile-search-form',[
            'classList' => [
                'u-padding__y--2', 
                'u-padding__x--3', 
                'u-width--auto',
                'u-display--none@lg',
                'u-print-display--none'
            ]
        ]
    )

@stop

@section('secondary-navigation')
    @if (!empty($primaryMenuItems))
        <div class="c-header__menu c-header__menu--secondary u-padding--05 u-display--none@xs
                        u-display--none@sm u-display--none@md u-print-display--none">
            <div class="o-container">
                <nav role="navigation" aria-label="{{ $lang->primaryNavigation }}">
                    @nav([
                        'items' => $primaryMenuItems,
                        'direction' => 'horizontal',
                        'allowStyle' => true,
                        'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--space-between'],
                        'context' => ['site.header.nav', 'site.header.business.nav']
                    ])
                    @endnav
                </nav>
            </div>
        </div>
    @endif

    @includeIf('partials.navigation.hamburgermenu')
@stop

@includeIf('partials.navigation.drawer')