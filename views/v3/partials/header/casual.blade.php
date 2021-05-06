@extends('templates.header', ['classList' => ['c-header c-header--casual']])

@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container o-container--wide">
            <div class="u-display--flex u-justify-content--space-between u-align-content--center">
                
                @link(['href' => $homeUrl, 'classList' => ['u-margin__right--4', 'u-display--flex']])
                    @logotype([
                        'src'=> $logotype->url,
                        'alt' => $lang->goToHomepage,
                        'classList' => ['c-nav__logo']
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
                    ]
                ])
                @endbutton

                @if (!empty($primaryMenuItems))
                    <nav  role="navigation" aria-label="{{ $lang->primaryNavigation }}" class="u-display--none@xs u-display--none@sm u-display--none@md u-print-display--none">
                        @nav([
                            'items' => $primaryMenuItems,
                            'direction' => 'horizontal',
                            'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--end']
                        ])
                        @endnav
                    </nav>
                @endif

            </div>
        </div>
    </div>
@stop

{{-- After header body --}}
@section('mobile-navigation')
    @includeIf('partials.navigation.drawer')
@stop