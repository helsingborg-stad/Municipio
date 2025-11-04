@element([ 'classList' => $getParentColumnClasses() ])
    
    @element(['classList' => ['o-layout-grid--col-span-12']])
        @includeWhen($filterConfig->isEnabled(), 'parts.filters')
    @endelement

    @if($appearanceConfig->getDesign() === \Municipio\PostsList\Config\AppearanceConfig\PostDesign::TABLE)
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @include('table')
        @endelement
    @else
        @foreach($posts as $post)
            @element(['classList' => $getPostColumnClasses()])
                @include('post.' . $appearanceConfig->getDesign()->value)
            @endelement
        @endforeach
    @endif
@endelement