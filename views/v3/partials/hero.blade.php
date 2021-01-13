@section('hero')
    <div id="sidebar-slider-area--container" class="o-container o-container--fullwidth u-print-display--none">
        @if (is_active_sidebar('slider-area') === true )
            
            @includeIf('partials.sidebar', ['id' => 'slider-area', 'classes' => ['o-grid']])

            @includeWhen($showHeroSearch, 'partials.search.hero-search-form')

        @endif
    </div>
@show
