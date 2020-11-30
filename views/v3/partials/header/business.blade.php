@extends('templates.header', ['classList' => ['c-header c-header--business']])

@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container">
            <div class="u-display--flex u-justify-content--space-between u-align-content--center">
                
                @link(['href' => $homeUrl, 'classList' => ['u-margin__right--auto']])
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

                <nav role="navigation" aria-label="{{$lang->relatedLinks}}" class="u-display--flex@lg u-display--flex@lx u-display--none@xs u-display--none@sm u-display--none@md">
                    @group([
                        'classList' => [
                            'u-justify-content--center@xs', 
                            'u-justify-content--center@sm', 
                            'u-justify-content--end', 
                            'u-box-shadow--1',
                            'u-rounded',
                            'u-margin--auto'
                        ]
                    ])
                        @foreach($tabMenuItems as $item)
                            @button([
                                'href'  => $item['href'], 
                                'text'  => $item['label'],
                                'size'  => 'sm',
                                'style' => 'basic'
                            ])
                            @endbutton
                        @endforeach

                        {{-- Search form in header --}}
                        @includeWhen($showHeaderSearch, 'partials.search.header-search-form')
                        
                    @endgroup
                </nav>

               
                
            </div>

        </div>
    </div>
@stop

@section('secondary-navigation')
    @if (!empty($primaryMenuItems))
        <div class="c-header__menu c-header__menu--secondary u-padding--05 u-display--none@xs u-display--none@sm u-display--none@md">
            <div class="o-container">
                <nav role="navigation" aria-label="{{ $lang->primaryNavigation }}">
                    @nav([
                        'items' => $primaryMenuItems,
                        'direction' => 'horizontal',
                        'classList' => ['u-flex-wrap--no-wrap', 'u-justify-content--space-between']
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