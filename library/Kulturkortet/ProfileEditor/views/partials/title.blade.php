
@element([])
    @element([
        'classList' => ['u-display--flex', 'u-align-items--center', 'o-layout-grid--gap-4']
    ])
        @typography([
            'element' => 'h2',
            'variant' => 'h4',
            'classList' => ['u-margin__bottom--0']
        ])
            {{$profile['firstname'] . ' ' . $profile['lastname']}}
        @endtypography
        @include('partials.actions')
    @endelement
    @notice([
        'type' => 'success',
        'classList' => ['u-display--inline-flex', 'u-padding__x--2', 'u-padding__y--1', 'u-margin__top--1'],
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