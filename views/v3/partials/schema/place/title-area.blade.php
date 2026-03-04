@element([
    'classList' => [
        'o-layout-grid--col-span-12'
    ]
])
@group([
    'justifyContent'=> 'space-between',
])
    @typography([
        'element' => 'h1',
        'variant' => 'h1'
    ])
        {!! $post->getTitle() !!}
    @endtypography
    @if (!empty($post->callToActionItems['floating']['icon']) && !empty($post->callToActionItems['floating']['wrapper']))
        @element($post->callToActionItems['floating']['wrapper'] ?? [])
            @icon($post->callToActionItems['floating']['icon'])
            @endicon
        @endelement
    @endif
@endgroup
@endelement