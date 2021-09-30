
{{-- Only wrap top level block --}}
@if($blockType == 'acf/button')
    <div class="{!! $classList !!}">
@endif

    @button([
        'text' => $text,
        'color' => $color,
        'style' => $style,
        'size' => $size,
        'href' => $link,
        'classList' => ['t-block-button']
    ])
    @endbutton

    {{-- Only one level of nesting --}}
    @if($blockType == 'acf/button')
        {!! "<InnerBlocks allowedBlocks=\"' . $allowedBlocks . '\" />" !!}
    @endif

{{-- Only wrap top level block --}}
@if($blockType == 'acf/button')
    </div>
@endif