@extends('templates.single')

@section('before-layout')
    @if ($filterPosition == 'top')
        <div class="o-container">
            @includeFirst([
                "partials.archive.archive-" . sanitize_title($postType) . "-filters",
                "partials.archive.archive-filters"
            ])
        </div>
    @endif
@stop

@section('content')
    <div class="archive s-archive s-archive-template-{{sanitize_title($template)}}  s-{{sanitize_title($postType)}}-archive">
        
        {!! $hook->loopStart !!}

        @includeIf('partials.sidebar', ['id' => 'content-area-top', 'classes' => ['o-grid']])
        
        @if($archiveTitle)
            @typography([
                "variant" => "h1",
                "element" => "h1",
                "classList" => ['t-archive-title', 't-' . $postType . '-archive-title', 'u-margin__bottom--2']
            ])
                {{ $archiveTitle }}
            @endtypography
        @endif

        @if ($filterPosition == 'content')
            @include("partials.archive.archive-filters")
        @endif

        @if (!empty($posts))

            @includefirst(['partials.post.' . $postType . '-' . $template, 'partials.post.post-' . $template], ['posts' => $posts])
            
            @if($showPagination)
                @pagination([
                    'list' => $paginationList, 
                    'classList' => ['u-margin__top--4', 'u-display--flex', 'u-justify-content--center'], 
                    'current' => $currentPage,
                    'linkPrefix' => '?paged='
                ])
                @endpagination
            @endif

        @else

            @notice([
                'type' => 'info',
                'message' => [
                    'text' => $lang->noResult,
                    'size' => 'md'
                ]
            ])
            @endnotice

        @endif

        @includeIf('partials.sidebar', ['id' => 'content-area', 'classes' => ['o-grid']])

        {!! $hook->loopEnd !!}
        
    </div>
@stop