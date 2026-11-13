@collection__item([
    'icon' => $icon ?? 'arrow_forward',
    'link' => $link,
    'classList' => $classList ?? [],
    'attributeList' => [
        ...($attributeList ?? []),
        'target' => $linkTarget ?? null,
        ...($link ? ['aria-labelledby' => $id . '-title'] : [])
    ]
])
    @typography([
        'element' => 'h2',
        'variant' => 'h4',
        'id'      => $id . '-title'
    ])
        {{ $title }}
    @endtypography
@endcollection__item
