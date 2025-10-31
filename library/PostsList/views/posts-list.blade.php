<pre>post-list</pre>
@foreach($posts as $post)
    @include($config->getDesign()->value)
@endforeach