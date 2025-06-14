<!-- Map -->
<div class="o-grid o-grid--equal-elements modularity-map-container">
    <div class="modularity-map-container__map-box {{$cardMapCss}}">
        @card([
            'classList' => [
                'modularity-map-container__map',
                'c-card__map'
            ],
            'attributeList' => [
                ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-map-' . $ID . '-label'] : []),
                'style' => 'min-height: ' . $height . 'px;'
            ],
            'context' => 'module.map'
        ])
            @if (!$hideTitle && !empty($postTitle))
                <div class="c-card__header">        
                    @typography([
                        'element' => 'h2',
                        'variant' => 'body',
                        'id'      => 'mod-map-' . $id .'-label'
                    ])
                        {!! $postTitle !!}
                    @endtypography
                </div>
            @endif
        
            @if($show_button)
                @button([
                    'type' => 'filled',
                    'color' => 'primary',
                    'text' => $button_label,
                    'size' => 'sm',
                    'href' => $button_url,
                    'classList' => [
                        'u-display--block@xs', 
                        'u-display--block@sm', 
                        'modularity-mod-map__button',
                        'u-level-1'
                    ],
                    'target' => '_blank'
                ])
                @endbutton
            @endif
            
            <div class="c-card__body">
                @iframe([
                    'src' => $map_url,
                    'height' => $height,
                    'classList' => [
                        'u-width--100', 'u-display--block'
                    ],
                    'title' => $map_description,
                    'labels' => $lang,
                ])
                @endiframe
            </div>
        @endcard
    </div>
    
    <!-- More information -->
    @if ($more_info_button)
        
        <div class="modularity-map-container__more-info {{$cardMoreInfoCss}}">
            @card([
                'attributeList' => [
                    ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-map-' . $ID . '-label-moreinfo'] : []),
                ],
                'context' => 'module.map'
            ])
                @if (!$hideTitle && !empty($postTitle))
                    <div class="c-card__header">
                        @icon(['icon' => 'info', 'size' => 'md', 'color' => 'primary', 'classList' => ['u-margin__right--1']])
                        @endicon
                        
                        @typography([
                            'element' => 'h2',
                            'variant' => 'p',
                            'id'      => 'mod-map-' . $id .'-label-moreinfo'
                        ])
                        
                            {!! $more_info_title !!}
                        @endtypography
                    </div>
                @endif
                <div class="c-card__body">
                    {!! $more_info !!}
                </div>
            @endcard
        </div>
        
    @endif
</div>
