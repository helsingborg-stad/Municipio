@element([ 'classList' => $getParentColumnClasses() ])
    
    @if($filterConfig->isEnabled())
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @include('parts.filters')
        @endelement
    @endif

    @if($appearanceConfig->getDesign() === \Municipio\PostsList\Config\AppearanceConfig\PostDesign::TABLE)
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @include('parts.table')
        @endelement
    @else
        @foreach($posts as $post)
            @element(['classList' => $getPostColumnClasses()])
                @include('post.' . $appearanceConfig->getDesign()->value)
            @endelement
        @endforeach
    @endif
@endelement