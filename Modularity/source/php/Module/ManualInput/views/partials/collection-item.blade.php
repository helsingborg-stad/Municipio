@collection__item([
    'link' => $link,
    'classList' => [...($classList ?? []), $columnSize],
    'context' => $context,
    'containerAware' => true,
    'bordered' => true,
    'attributeList' => [
        ...($attributeList ?? []),
        'target' => $linkTarget ?? null,
        ...($link ? ['aria-labelledby' => $id . '-title'] : [])
    ]
])
    @slot('before')
        @if (!empty($image))
            @image($image)
            @endimage
        @endif
    @endslot
    @group([
        'direction' => 'vertical'
    ])
        @group([
            'justifyContent' => 'space-between'
        ])
            @typography([
                'element' => 'h2',
                'variant' => 'h3',
                'id'      => $id . '-title'
            ])
                {{ $title }}
            @endtypography
            @if (!empty($icon))
                @element([
                    'classList' => [
                        'u-display--flex',
                        'u-detail-shadow-3'
                    ]
                ])
                    @icon([
                        'icon' => $icon,
                        'size' => 'md',
                        'color' => 'black'
                    ])
                    @endicon
                @endelement
            @endif
        @endgroup
        @if(!empty($content))
            @typography([])
                {!! $content !!}
            @endtypography
        @endif
    @endgroup
@endcollection__item
