@if($slides) 
    @slider([
        'autoSlide'     => $autoslide,
        'ratio'         => $ratio ?? '16:9',
        'title'         => (!$hideTitle && !empty($postTitle)) ? $postTitle : false,
        'preamble'      => $preamble ?? false,
        'padding'       => 0,
        'gap'           => 2,
        'peekSlides'    => !empty($peekSlides),
        'slideByPage'   => !empty($slideByPage),
        'navigationHover' => $navigationHover ?? true,
        'compactNavigation' => $compactNavigation ?? true,
        'slidesPerPage' => $slidesPerPage ?? 1,
        'shadow'        => $sidebarContext !== 'sidebar.slider-area',
        'heroStyle'     => $sidebarContext === 'sidebar.slider-area',
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
