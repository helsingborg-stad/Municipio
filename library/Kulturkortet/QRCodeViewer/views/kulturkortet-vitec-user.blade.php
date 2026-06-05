@element([
    'classList' => [
        'u-display--flex',
        'u-align-items--center',
        'u-flex-direction--column',
        'o-layout-grid--gap-12'
    ]
])
    @element([
        'classList' => [
            'kulturkortet__content-wrapper',
            'o-layout-grid',
            'o-layout-grid--gap-8'
        ]
    ])
        @element([
            'classList' => ['u-display--flex', 'u-align-items--center', 'o-layout-grid--gap-4']
        ])
            @typography([
                'element' => 'h2',
                'variant' => 'h4',
                'classList' => ['u-margin__bottom--0']
            ])
                {{ $lang['yourCultureCard'] }}
            @endtypography
            @include('partials.actions')
        @endelement
        @include('partials.ticket')
    @endelement
@endelement

{{-- @dump($debug) --}}