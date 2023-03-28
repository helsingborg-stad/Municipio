@section('hero')
    <div class="o-container o-container--fullwidth u-print-display--none" id="sidebar-slider-area--container">
        @if (is_active_sidebar('slider-area') === true)
            @includeIf('partials.sidebar', [
                'id' => 'slider-area',
                'classes' => ['o-grid', 'o-grid--no-margin', 'o-grid--no-gutter'],
            ])

            {{-- Search in hero --}}
            @includeWhen($showHeroSearch, 'partials.search.hero-search-form')

            {{-- Emblem in hero --}}
            @includeWhen($emblem && $showEmblemInHero, 'partials.emblem')
        @endif
    </div>
    @if (!$placeQuicklinksAfterContent)
        @include('partials.navigation.fixed')
    @endif
@show
