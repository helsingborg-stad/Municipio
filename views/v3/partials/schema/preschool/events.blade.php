@element()
    @typography(['element' => 'h2', 'classList' => ['u-margin__bottom--2']])
        {{ $lang->eventsLabel }}
    @endtypography
    {!!$eventsBlock!!}
@endelement