@element([])
    @if(!empty($marker['title']))
        @typography([
            'element' => 'h3',
            'variant' => 'h6',
        ])
            {{ $marker['title'] }}
        @endtypography
    @endif
    @if(!empty($marker['description']))
        @typography([])
            {{ $marker['description'] }}
        @endtypography
    @endif
    @if(!empty($marker['url']) && !empty($marker['link_text']))
        @link([
            'href' => $marker['url'],
        ])
            {{ $marker['link_text'] }}
        @endlink
    @endif
@endelement