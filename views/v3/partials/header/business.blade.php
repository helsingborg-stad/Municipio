@extends('templates.header', ['classnames' => ['c-header c-header--business']])

@section('top-navigation')
    {{-- TODO: Replace hard-coded links with wp help-menu --}}
    <div class="c-header__menu c-header__menu--top u-display--none@xs u-display--none@sm u-display--none@md">
        <div class="o-container">
            <nav class="c-nav c-nav--sm u-justify-content--center@xs u-justify-content--center@sm u-justify-content--end">
                <a class="c-nav__link" href="{{$homeUrl}}">
                    <span>Företagare</span>
                </a>
                <a class="c-nav__link" href="{{$homeUrl}}">
                    <span>Självservice</span>
                </a>
                <a class="c-nav__link" href="{{$homeUrl}}">
                    <span>Besökare</span>
                </a>
            </nav>
        </div>
    </div>
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
        <div class="c-header__menu c-header__menu--secondary u-display--none@xs u-display--none@sm u-display--none@md">
            <div class="o-container">
                <nav class="c-nav c-nav--stretch c-nav--lg">
                    @foreach($primaryMenuItems as $item)
                    <a class="c-nav__link" href="{{$item['href']}}"><span>{{$item['label']}}</span></a>
                    @endforeach
                </nav>
            </div>
        </div>
    @endif
@stop

{{-- After header body --}}
@section('mobile-navigation')
    @includeIf('partials.navigation.drawer')
@stop