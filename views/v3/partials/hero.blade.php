@section('hero')
    @if (is_active_sidebar('slider-area'))
        @element([
            'id' => 'hero',
            'classList' => isset($classes) ? $classes : [
                'o-container',
                'o-container--fullwidth',
                'o-container--remove-spacing',
                'u-print-display--none'
            ],
        ])
                @includeIf('partials.sidebar', [
                    'id' => 'slider-area',
                    'classes' => isset($sliderAreaClasses) ? $sliderAreaClasses : ['o-grid', 'o-grid--no-margin', 'o-grid--no-gutter'],
                ])

                {{-- Search in hero --}}
                @includeWhen($showHeroSearch, 'partials.search.hero-search-form')

                {{-- Emblem in hero --}}
                @includeWhen($emblem && $showEmblemInHero, 'partials.emblem')
        @endelement
    @endif
    @if ($quicklinksPlacement !== 'below_content')
        @include('partials.navigation.fixed')
    @endif
@show
