@extends('templates.header', ['classList' => ['c-header', 'c-header--flexible']])

@section('primary-navigation')

    @if (!empty($headerData))
        <div class="c-header__main-upper-area-container">
            <div class="c-header__main-upper-area o-container">   
                @include('partials.header.components.headerLoop', ['row' => 'upper', 'align' => 'left'])
                @include('partials.header.components.headerLoop', ['row' => 'upper', 'align' => 'center'])
                @include('partials.header.components.headerLoop', ['row' => 'upper', 'align' => 'right'])
            </div>
        </div>
        <div class="c-header__main-lower-area-container">
                <div class="c-header__main-lower-area o-container">
                    @include('partials.header.components.headerLoop', ['row' => 'lower', 'align' => 'left'])
                    @include('partials.header.components.headerLoop', ['row' => 'lower', 'align' => 'center'])
                    @include('partials.header.components.headerLoop', ['row' => 'lower', 'align' => 'right'])
                </div>
        </div>
        
        @if(!empty($megaMenuItems) && (isset($headerData['lower']['mega-menu']) || isset($headerData['upper']['mega-menu'])))
            @include('partials.navigation.megamenu')
        @endif
        @if (isset($headerData['lower']['search-modal']) || isset($headerData['upper']['search-modal']))
            @include('partials.search.search-modal')
        @endif
    @endif
@endsection
