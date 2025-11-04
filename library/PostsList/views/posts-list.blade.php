@if($config->getDesign() === \Municipio\PostsList\Config\AppearanceConfig\PostDesign::TABLE)
    @element([ 'classList' => $getParentColumnClasses() ])
        @element(['classList' => ['o-layout-grid--col-span-12']])
            @include('table')
        @endelement
    @endelement
@else
    @element([ 'classList' => $getParentColumnClasses() ])
        @foreach($posts as $post)
            @element(['classList' => $getPostColumnClasses()])
                @include('post.' . $config->getDesign()->value)
            @endelement
        @endforeach
    @endelement
@endif