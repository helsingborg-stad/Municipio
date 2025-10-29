<pre>post-list</pre>
@foreach($posts as $post)
    @include($config->getAppearanceConfig()->getDesign()->value)
@endforeach