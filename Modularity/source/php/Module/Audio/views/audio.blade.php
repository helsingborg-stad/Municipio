@if (!empty($url))
    @if (empty($hideTitle) && !empty($postTitle))
        @typography([
            'id'        => 'mod-audio-' . $ID .'-label',
            'element'   => 'h2', 
            'variant'   => 'h2', 
            'classList' => [
                'module-title',
                'u-text-align--' . $alignment,
            ]
        ])
            {!! $postTitle !!}
        @endtypography
    @endif
    @includeWhen($requiresAcceptance, 'partials.acceptance')
    @includeWhen(!$requiresAcceptance, 'partials.content')
@endif