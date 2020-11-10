@extends('templates.header', ['classList' => ['c-header c-header--business']])

@section('top-navigation')
    @if($tabMenuItems) 
        <div class="c-header__menu c-header__menu--top">
            <div class="o-container">
                <nav role="navigation" aria-label="Related websites">
                    @group(['classList' => ['u-justify-content--center@xs', 'u-justify-content--center@sm', 'u-justify-content--end']])
                        @foreach($tabMenuItems as $item)
                            @button([
                                'href'  => $item['href'], 
                                'text'  => $item['label'],
                                'size'  => 'sm',
                                'style' => 'outlined'
                            ])
                            @endbutton
                        @endforeach
                    @endgroup
                </nav>
            </div>
        </div>
    @endif
@stop

@section('primary-navigation')
    <div class="c-header__menu c-header__menu--primary">
        <div class="o-container">
            <nav class="c-nav">
                <a class="c-nav__brand u-mr-auto" href="{{$homeUrl}}">
                    <img class="c-nav__logo" src="{{$logotype->standard['url']}}">
                </a>
                
                <button class="hamburger hamburger--stacked@sm hamburger--reverse@md hamburger--slider c-header__button c-nav__action u-display--none@lg u-display--none@xl js-burger js-trigger-drawer" type="button"
                aria-label="Menu" aria-controls="navigation">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                    <span class="hamburger-label">
                        Meny
                    </span>
                </button>
            </nav>
        </div>
    </div>
@stop

@section('secondary-navigation')
    @if (!empty($primaryMenuItems))
        <div class="c-header__menu c-header__menu--secondary u-padding--05 u-display--none@xs u-display--none@sm u-display--none@md">
            <div class="o-container">
                <nav role="navigation" aria-label="{{$lang->primaryNavigation}}">
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