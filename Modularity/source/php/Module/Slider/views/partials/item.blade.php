@slider__item([
    'title'             => $slide['textblock_title'],
    'text'              => $slide['textblock_content'],
    'layout'            => $slide['textblock_position'],
    'image'             => !empty($slide['hasImageContract']) ? $slide['image'] : ($slide['image']['src'] ?? false),
    'video'             => $slide['video_mp4']['url'] ?? false,
    'link'              => $slide['link_type'] !== "false" ? ($slide['link_url'] ?? null) : null,
    'linkDescription'   => $slide['link_url_description'] ?? null,
    'alt'               => !empty($slide['hasImageContract']) ? null : ($slide['image']['alt'] ?? null),
    'heroStyle'         => $sidebarContext === 'sidebar.slider-area',
    'cta'               => $slide['call_to_action'] ?? null,
    'context'           => [
        'module.slider-item', 
        $sidebarContext . '.module.slider-item',
         (
            $sidebarContext === 'sidebar.slider-area' ? 
            'module.slider.hero.slider-item' : 'module.slider.default.slider-item'
        )
    ]
])
@endslider__item
