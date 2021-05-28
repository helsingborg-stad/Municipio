@section('hero')
    <div id="sidebar-slider-area--container" class="o-container o-container--fullwidth u-print-display--none">
        @if (is_active_sidebar('slider-area') === true )
            
            @includeIf('partials.sidebar', ['id' => 'slider-area', 'classes' => ['o-grid']])

            {{-- Search in hero --}}
            @includeWhen($showHeroSearch, 'partials.search.hero-search-form')

            {{-- Emblem in hero --}}
            @includeWhen($emblem, 'partials.emblem')

        @endif
    </div>
@show
