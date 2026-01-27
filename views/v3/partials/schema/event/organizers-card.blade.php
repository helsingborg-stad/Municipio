@card([
    'heading' => $lang->organizersTitle,
])
    @if(!empty($organizers))
        @slot('belowContent')
            @element(['classList' => ['u-display--flex', 'o-layout-grid--gap-8', 'u-flex-direction--column']])
                @foreach($organizers as $organizer)
                    @element(['classList' => $loop->first ? ['u-margin__top--2'] : []])
                        @if(!empty($organizer['name']))
                            @typography(['element' => 'h3', 'variant' => 'h4', 'classList' => ['u-margin__top--0']])
                                {!! $organizer['name'] !!}
                            @endtypography
                        @endif
                        @if(!empty($organizer['email']))
                            @link(['href' => 'mailto:' . $organizer['email'], 'classList' => ['u-display--block']])
                                {!! $organizer['email'] !!}
                            @endlink
                        @endif
                        @if(!empty($organizer['telephone']))
                            @link(['href' => 'tel:' . $organizer['telephone'], 'classList' => ['u-display--block']])
                                {!! $organizer['telephone'] !!}
                            @endlink
                        @endif
                        @if(!empty($organizer['url']))
                            @link(['href' => $organizer['url'], 'classList' => ['u-display--block']])
                                {!! $organizer['url'] !!}
                            @endlink
                        @endif
                    @endelement
                @endforeach
            @endelement
        @endslot
    @endif
@endcard