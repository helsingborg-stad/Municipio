@element()
    @if(!empty($visitUs['heading']))
        @typography(['element' => 'h2', 'variant' => 'h5'])
            {!! $visitUs['heading'] !!}
        @endtypography
    @endif
    @if(!empty($visitUs['content']))
        {!! $visitUs['content'] !!}
    @endif
@endelement
