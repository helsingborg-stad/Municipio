@section('hero')
    @if (is_active_sidebar('slider-area'))
        <div class="o-container o-container--fullwidth o-container--remove-spacing u-print-display--none" id="sidebar-slider-area--container">

            @includeIf('partials.sidebar', [
                'id' => 'slider-area',
                'classes' => isset($classes) ? $classes : ['o-grid', 'o-grid--no-margin', 'o-grid--no-gutter'],
            ])

            {{-- Search in hero --}}
            @includeWhen($showHeroSearch, 'partials.search.hero-search-form')

            {{-- Emblem in hero --}}
            @includeWhen($emblem && $showEmblemInHero, 'partials.emblem')

        </div>
    @endif
    @if ($quicklinksPlacement !== 'below_content')
        @include('partials.navigation.fixed')
    @endif
@show
