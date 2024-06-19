@extends('templates.header', ['classList' => ['c-header','c-header--modern']])


@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        @group([
            'direction' => 'row',
            'justifyContent' => 'space-between',
            'classList' => [
                'o-container',
                'u-height--100'
            ]
        ])
            <div class="c-header__menu-logotype" aria-hidden="true">
            </div>
            @group([
                'direction' => 'row',
                'alignItems' => 'center',
                'justifyContent' => 'right',
            ])

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
            @endgroup
        @endgroup
    </div>

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
    <div class="c-header__menu c-header__menu--secondary">
        @group([
            'direction' => 'row',
            'justifyContent' => 'space-between',
            'classList' => [
                'o-container',
                'u-height--100'
            ]
        ])

        @group([
            'alignItems' => 'bottom',
            'classList' => [
                'c-header__menu-container',
            ]
        ])
            @link([
                'href' => $homeUrl, 
                'classList' => [
                    'c-header__logotype-link',
                    'u-position--absolute'
                ]
            ])
                @logotype([
                    'src'=> $logotype,
                    'alt' => $lang->goToHomepage,
                    'classList' => [
                        'c-nav__logo', 
                        'c-header__logotype'
                    ],
                    'context' => ['site.header.logo', 'site.header.modern.logo']
                ])
                @endlogotype
            @endlink

                @typography([
                    'element' => 'h2',
                    'variant' => 'h4',
                    'classList' => [
                        'c-header__brand-text',
                        'u-margin__top--0',
                        'u-display--none@xs',
                        'u-display--none@sm',
                        'u-display--none@md',
                        'u-color--primary',
                    ]
                ])
                    {{$brandText[0]}}
                @endtypography
            @endgroup

            @group([
                'direction' => 'row',
                'alignItems' => 'center',
                'justifyContent' => 'right',
                'classList' => [
                    'c-header__menu-container'
                ]
            ])
                    @includeIf(
                        'partials.navigation.primary', 
                        [
                            'context' => [
                                'site.header.nav', 
                                'site.header.business.nav'
                            ],
                            'classList' => [
                                'u-flex-wrap--no-wrap', 
                                'u-display--none@xs', 
                                'u-display--none@sm', 
                                'u-display--none@md', 
                                'u-print-display--none'
                            ],
                            'primaryMenuHeight' => 'md'
                        ]
                    )
                    
                @includeIf('partials.navigation.drawer')
            @endgroup
        @endgroup
    </div>

    @includeIf('partials.navigation.megamenu')
@stop