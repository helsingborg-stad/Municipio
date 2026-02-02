@php
    $asyncAttrs = $asyncAttributes ?? null;
    $postsPerPage = $asyncAttrs['postsPerPage'] ?? 12;
    $numberOfColumns = $asyncAttrs['numberOfColumns'] ?? 3;
    $ghostColumnClasses = match ((int) $numberOfColumns) {
        2 => ['o-layout-grid--col-span-12', 'o-layout-grid--col-span-6@md'],
        3 => ['o-layout-grid--col-span-12', 'o-layout-grid--col-span-6@md', 'o-layout-grid--col-span-4@lg'],
        4 => ['o-layout-grid--col-span-12', 'o-layout-grid--col-span-6@sm', 'o-layout-grid--col-span-4@md', 'o-layout-grid--col-span-3@lg'],
        default => ['o-layout-grid--col-span-12'],
    };
@endphp
@element([
    'classList' => $getParentColumnClasses(),
    'id' => $id,
    'attributeList' => [
        'style' => 'scroll-margin-top: 100px;',
        'data-posts-list-async' => $asyncAttrs ? 'true' : false,
        'data-posts-list-attributes' => $asyncAttrs ? json_encode($asyncAttrs) : false,
    ]
 ])
    @if($asyncAttrs)
        <template data-posts-list-ghost>
            @for($i = 0; $i < $postsPerPage; $i++)
                @element(['classList' => $ghostColumnClasses])
                    @card([
                        'heading' => '████████████',
                        'content' => '████████ ██████ ████████████ ██████ ████',
                        'classList' => ['u-height--100', 'u-preloader']
                    ])
                    @endcard
                @endelement
            @endfor
        </template>
    @endif
    @if($filterConfig->isTextSearchEnabled() || $filterConfig->isDateFilterEnabled() || !empty($getTaxonomyFilterSelectComponentArguments()))
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @include('parts.filters')
        @endelement
    @endif
    @if(empty($posts))
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @notice([
                'type' => 'info',
                'message' => [
                    'text' => $lang->noResult,
                    'size' => 'md'
                ]
            ])
            @endnotice
        @endelement
    @else
        @if($appearanceConfig->getDesign() === \Municipio\PostsList\Config\AppearanceConfig\PostDesign::TABLE)
            @element(['classList' => ['o-layout-grid--col-span-12']])
                @include('parts.table')
            @endelement
        @else
            @foreach($posts as $post)
                @element(['classList' => $getPostColumnClasses()])
                    @includeFirst(['post.' . $appearanceConfig->getDesign()->value, 'post.card'])
                @endelement
            @endforeach
        @endif
        @if($paginationEnabled() && !empty($getPaginationComponentArguments()))
            @element(['classList' => ['o-layout-grid--col-span-12']])
                @include('parts.pagination')
            @endelement
        @endif
    @endif
@endelement