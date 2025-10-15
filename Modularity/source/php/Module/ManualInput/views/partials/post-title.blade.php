
@group([
    'direction' => 'horizontal',
    'justifyContent' => 'space-between'
])
 @if (empty($hideTitle) && !empty($postTitle))
    @typography([
        'element' => $element ?? 'h2',
        'variant' => $variant ?? 'h2',
        'classList' => $classList ?? ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif
@if (!empty($titleIcon))
    @icon($titleIcon)
    @endicon
@endif
@endgroup