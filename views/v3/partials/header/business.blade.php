@extends('templates.header', ['classList' => ['c-header c-header--business']])

@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container">
            <div class="u-display--flex u-justify-content--space-between u-align-content--center">
                
                @link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto', 'u-display--flex']])
                    @logotype([
                        'src'=> $logotype->url,
                        'alt' => $lang->goToHomepage,
                        'classList' => ['c-nav__logo'],
                        'context' => ['site.header.logo', 'site.header.business.logo']
                    ])
                    @endlogotype
                @endlink

                @button([
                    'id' => 'mobile-menu-trigger',
                    'text' => $lang->menu,
                    'color' => 'default',
                    'style' => 'basic',
                    'icon' => 'keyboard_arrow_down',
                    'classList' => [
                        'mobile-menu-trigger',
                        'js-burger',
                        'js-trigger-drawer',
                        'u-display--none@lg'
                    ],
                    'attributeList' => [
                        'aria-label' => $lang->menu,
                        'aria-controls' => "navigation"
                    ],
                    'context' => ['site.header.menutrigger', 'site.header.business.menutrigger']
                ])
                @endbutton

                {{-- Tab menu items --}}
                @includeWhen($tabMenuItems, 'partials.navigation.tabs')

                {{-- Search form in header --}}
                @includeWhen($showHeaderSearch, 'partials.search.header-search-form')
                
            </div>

        </div>

    </div>

    @includeWhen($showMobileSearch, 'partials.search.mobile-search-form')

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
                        'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--space-between'],
                        'context' => ['site.header.nav', 'site.header.business.nav']
                    ])
                    @endnav
                </nav>
            </div>
        </div>
    @endif
@stop

{{-- After header body --}}
@section('mobile-navigation')
    @includeIf('partials.navigation.drawer')
@stop