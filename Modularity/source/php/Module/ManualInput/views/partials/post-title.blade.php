@if (empty($hideTitle) && !empty($postTitle) && !empty($titleIcon))
    @group([
        'direction' => 'horizontal',
        'justifyContent' => 'space-between'
    ])
        @typography([
            'element' => $element ?? 'h2',
            'variant' => $variant ?? 'h2',
            'classList' => empty($classList) ? ['module-title'] : array_merge(['module-title', 'u-margin--0'], $classList)
        ])
            {!! $postTitle !!}
        @endtypography

        @icon($titleIcon)
        @endicon
    @endgroup
@else
    @if (empty($hideTitle) && !empty($postTitle))
        @typography([
            'element' => $element ?? 'h2',
            'variant' => $variant ?? 'h2',
            'classList' => empty($classList) ? ['module-title', 'u-margin--0'] : array_merge(['module-title', 'u-margin--0'], $classList)
        ])
            {!! $postTitle !!}
        @endtypography
    @endif
@endif