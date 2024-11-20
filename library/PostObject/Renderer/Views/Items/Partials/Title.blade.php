@typography([
    'element' => $element ?? 'h2',
    'variant' => $variant ?? 'h2',
    'classList' => $classList ?? ['module-title']
])
    {!! $title !!}
@endtypography