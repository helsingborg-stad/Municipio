@element([
    'classList' => $getParentColumnClasses(),
    'id' => $id,
    'attributeList' => [
        'style' => 'scroll-margin-top: 100px;',
        'data-posts-list-async' => !empty($asyncAttributes) ? 'true' : false,
        'data-posts-list-attributes' => !empty($asyncAttributes) ? json_encode($asyncAttributes) : false,
    ]
 ])
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