{{-- <h2 class="pdf-post-heading">Table of contents</h2>
<ul class="">
    @foreach($posts as $index => $post)
        @if(!empty($post->postTitleFiltered))
            <li><a href="#post-{{$index}}">{{ $post->postTitleFiltered }} %%CH2%%</a></li>
        @endif
    @endforeach
</ul> --}}
<script type="text/php">
	$GLOBALS['chapters'] = array();
	$GLOBALS['backside'] = $pdf->open_object();
</script>

<h2 class="pdf-heading--h1">Table of Contents</h2>
<ul class="pdf-toc__list">
    @foreach($posts as $index => $post)
    <li class="pdf-toc__list-item">
        <a class="pdf-toc__item" href="#post-{{$index}}">
            <div class="pdf-toc__title">{{$post->postTitleFiltered}}</div>
            <div class="pdf-toc__number">%%CH{{$index + 1}}%%</div>
        </a>
    </li>
    @endforeach
</ul>

<script type="text/php">
	$pdf->close_object();
</script>
