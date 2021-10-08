@extends('templates.header', ['classList' => ['c-header c-header--casual']])

@section('primary-navigation')

    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container o-container--wide">
            <div class="u-display--flex u-justify-content--end u-align-content--center">
                
                @link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex']])
                    @logotype([
                        'src'=> $logotype->url,
                        'alt' => $lang->goToHomepage,
                        'classList' => ['c-nav__logo'],
                        'context' => ['site.header.logo', 'site.header.casual.logo']
                    ])
                    @endlogotype
                @endlink

                @if (!empty($languageMenuItems))
                    <div class="site-language-menu" js-toggle-item="language-menu-toggle" js-toggle-class="is-expanded">
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

                @button([
                    'id' => 'mobile-menu-trigger-open',
                    'color' => 'default',
                    'style' => 'basic',
                    'icon' => 'menu',
                    'classList' => [
                        'mobile-menu-trigger',
                        'u-display--none@lg'
                    ],
                    'attributeList' => [
                        'aria-label' => $lang->menu,
                        'aria-controls' => "navigation",
                        'js-toggle-trigger' => 'js-drawer'
                    ],
                    'context' => ['site.header.menutrigger', 'site.header.casual.menutrigger']
                ])
                @endbutton

                @if (!empty($primaryMenuItems))
                    <nav role="navigation" aria-label="{{ $lang->primaryNavigation }}" class="u-display--none@xs u-display--none@sm u-display--none@md u-print-display--none">
                        @nav([
                            'items' => $primaryMenuItems,
                            'direction' => 'horizontal',
                            'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--end'],
                            'context' => ['site.header.nav', 'site.header.casual.nav']
                        ])
                        @endnav
                    </nav>
                @endif

            </div>
        </div>
    </div>
@stop

@includeIf('partials.navigation.drawer')