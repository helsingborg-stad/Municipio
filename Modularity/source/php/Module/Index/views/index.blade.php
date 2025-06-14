@if (!$hideTitle && !empty($postTitle))
    @typography([
        'id' => 'mod-posts-' . $ID . '-label',
        'element' => 'h2',
        'variant' => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif
<div class="o-grid">
    @foreach ($items as $item)
        <div class="{{ apply_filters('Municipio/Controller/Archive/GridColumnClass', $columnClass) }}">
            @card([
                'link'              => $item['permalink'] ?? false,
                'classList'         => ['u-height--100', 'u-height-100'],
                'context'           => 'module.index',
                'hasAction'         => !empty($item['permalink']),
                'image'             => isset($item['thumbnail'][0]) ? $item['thumbnail'][0] : false,
                'heading'           => isset($item['title']) && !empty($item['title']) ? $item['title'] : false,
                'content'           => $item['lead'],
                'containerAware'    => true
            ])
            @endcard
        </div>
    @endforeach
</div>
