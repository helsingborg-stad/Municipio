
@element([])
    @typography([
        'element' => 'h2',
        'variant' => 'h4'
    ])
        {{$profile['firstname'] . ' ' . $profile['lastname']}}
    @endtypography
    @notice([
        'type' => 'success',
        'classList' => ['u-display--inline-flex', 'u-padding__x--2', 'u-padding__y--1'],
    ])
        @element([
            'componentElement' => 'span',
            'classList' => ['u-display--flex', 'u-align-items--center', 'o-layout-grid--gap-1']
        ])
            {{$lang['activeUntil']}}: {{$ticket['validUntil']}}
            @icon([
                'icon' => 'check_circle',
                'size' => 'md'
            ])
            @endicon
        @endelement
    @endnotice
@endelement