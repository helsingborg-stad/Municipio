@if(!empty($accessibilityFeatures))
    @card([
        'heading' => $lang->accessibilityTitle
    ])
        @slot('aboveContent')
            @element(['componentElement' => 'ul'])
                @foreach($accessibilityFeatures as $feature)
                    @element(['componentElement' => 'li']){!! $feature !!}@endelement
                @endforeach
            @endelement
        @endslot
    @endcard
@endif