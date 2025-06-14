@card([
    'attributeList' => [
        ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-inlaylist-' . $ID . '-label'] : []),
    ],
    'context' => 'module.inlay.list'
])

    @if (!$hideTitle && !empty($postTitle))
        <div class="c-card__header">
            @typography([
                'id'        => 'mod-inlaylist' . $ID . '-label',
                'element'   => 'h2',
                'variant'   => 'h4',
                'classList' => []
            ])
                {!! $postTitle !!}
            @endtypography
        </div>
    @endif

    @if (!empty($items))
        @collection([
            'sharpTop' => true
        ])
        @foreach($items as $item)
            @collection__item([
                'icon' => 'arrow_forward',
                'link' => $item['href']
            ])
                @typography([
                    'element' => 'h2',
                    'variant' => 'h4'
                ])
                    {{$item['label']}}
                @endtypography
            @endcollection__item
        @endforeach
        @endcollection
    @endif
@endcard
