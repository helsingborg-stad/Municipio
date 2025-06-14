@if (!$hideTitle && !empty($postTitle))
    @typography([
        'id'        => 'mod-slider-' . $ID . '-label',
        'element'   => 'h2', 
        'variant'   => 'h2', 
        'classList' => ['module-title']
    ])
        {!! $postTitle !!}
    @endtypography
@endif
@if($slides) 
    @slider([
        'autoSlide'     => $autoslide,
        'ratio'         => $ratio ?? '16:9',
        'repeatSlide'   => $wrapAround,
        'shadow'        => $sidebarContext !== 'sidebar.slider-area',
        'heroStyle'     => $sidebarContext === 'sidebar.slider-area',
        'attributeList' => [
            'aria-labelledby' => (!$hideTitle && !empty($postTitle)) ? 'mod-slider-' . $ID . '-label' : '',
            'data-slides-per-page' => $slidesPerPage,
            'data-slider-focus-center' => '',
            'data-aria-labels' => json_encode($ariaLabels)
        ],
        'context'       => ['module.slider', $sidebarContext . '.module.slider', $sidebarContext . '.animation-item'],
    ])
        @foreach ($slides as $slide)
            @include('partials.item') 
        @endforeach
    @endslider
@else 
    @notice([
        'type' => 'info',
        'message' => [
            'title' => $lang->noSlidesHeading,
            'text' => $lang->noSlides,
            'size' => 'sm'
        ],
        'icon' => [
            'name' => 'report',
            'size' => 'md',
            'color' => 'white'
        ],
        'classList' => [
            'u-margin__y--4'
        ]
    ])
    @endnotice
@endif