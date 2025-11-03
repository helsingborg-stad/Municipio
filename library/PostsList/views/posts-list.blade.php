<pre>post-list</pre>

@element([ 'classList' => $getParentColumnClasses() ])
    @foreach($posts as $post)
        @element(['classList' => $getPostColumnClasses()])
            @include($config->getDesign()->value)
        @endelement
    @endforeach
@endelement