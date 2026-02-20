
{{-- Only wrap top level block --}}
@if($blockType == 'acf/button')
    <div class="{!! $classList !!}" id="{{ $anchor }}">
@endif

    @button([
        'text' => $text,
        'color' => $color,
        'style' => $style,
        'size' => $size,
        'href' => $link,
        'classList' => ['t-block-button'],
        'target' => !empty($open_in_new_window) ? '_blank' : '_top',
        'attributeList' => [
            'id' => $anchor ?? '',
        ]
    ])
    @endbutton

    {{-- Only one level of nesting --}}
    @if($blockType == 'acf/button')
        {!! '<InnerBlocks allowedBlocks="false" />' !!}
    @endif

{{-- Only wrap top level block --}}
@if($blockType == 'acf/button')
    </div>
@endif
