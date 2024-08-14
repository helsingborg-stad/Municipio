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
        @if(!empty($megaMenuItems) && $headerData['hasMegaMenu'])
            @include('partials.navigation.megamenu')
        @endif
        @if ($headerData['hasSearch'])
            @include('partials.search.search-modal')
        @endif
    @endif
@endsection
