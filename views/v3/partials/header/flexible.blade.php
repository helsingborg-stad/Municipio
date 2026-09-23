@if (!empty($headerData))
    @include('partials.header.skip-to-main-content')
    @includeWhen($hasMainMenu, 'partials.header.skip-to-main-menu')
    @includeWhen($hasSideMenu, 'partials.header.skip-to-side-menu')
    @include('partials.header.header.upper')
    @include('partials.header.header.lower')

    @if(
        !empty($megaMenu['items'])
    )
        @include('partials.navigation.megamenu')
    @endif
    @if ($headerData['hasSearch'])
        @include('partials.search.search-modal')
    @endif
@endif
