<div class="{!! $classList !!}">
    @button([
        'text' => $text,
        'color' => $color,
        'style' => $style,
        'size' => $size,
        'href' => $link,
        'classList' => ['t-block-button']
    ])
    @endbutton

    {!! '<InnerBlocks />' !!}
</div>