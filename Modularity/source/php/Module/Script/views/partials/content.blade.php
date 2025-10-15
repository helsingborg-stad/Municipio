
 @if (!empty($embed))
    @if($requiresAccept)
             @acceptance([
                 'labels' => $lang,
                 'src' => $scriptSrcArray,
             ])

             <div class="{{ $scriptPadding }}">{!! $embedContent !!}</div>

             @endacceptance
    @else 
        <div class="{{ $scriptPadding }}">{!! $embedContent !!}</div>
    @endif
 @endif
