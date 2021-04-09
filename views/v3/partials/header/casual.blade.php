@extends('templates.header', ['classList' => ['c-header c-header--casual']])

@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container o-container--wide">
            <div class="u-display--flex u-justify-content--space-between u-align-content--center">
                
                @link(['href' => $homeUrl, 'classList' => ['u-margin__right--4']])
                    @logotype([
                        'src'=> $logotype->standard['url'],
                        'alt' => $lang->goToHomepage,
                        'classList' => ['c-nav__logo']
                    ])
                    @endlogotype
                @endlink

                <button class="hamburger hamburger--stacked@sm hamburger--reverse@md hamburger--slider c-header__button c-nav__action u-display--none@lg u-display--none@xl js-burger js-trigger-drawer" type="button" aria-label="Menu" aria-controls="navigation">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                    <span class="hamburger-label">
                        {{ $lang->menu }}
                    </span>
                </button>

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