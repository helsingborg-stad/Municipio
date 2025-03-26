@element(['classList' => ['u-display--flex', 'u-align-items--center', 'u-gap-1', 'u-margin__bottom--1']])
    @icon(['icon' => $icon, 'size' => 'md'])@endicon
    @typography(['element' => 'h2', 'classList' => ['u-margin__top--0']])
        {!!$header!!}
    @endtypography
@endelement