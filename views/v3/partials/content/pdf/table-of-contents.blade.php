<section class="pdf-toc {{!empty($cover) ? 'pdf-page-break' : ''}}">
<script class="pdf-script" type="text/php">
    $GLOBALS['chapters'] = array();
    $GLOBALS['max_object'] = 0;
</script>

<h2 class="pdf-heading--h1">Table of Contents</h2>
<ul class="pdf-toc__list">
    @foreach($posts as $index => $post)

    <li class="pdf-toc__list-item">
        <a class="pdf-toc__item {{strlen($post->postTitleFiltered) > 100 ? 'pdf-toc__item--big' : ''}}" href="#post-{{$index}}">
            <div class="pdf-toc__title">{{$post->postTitleFiltered}}</div>
            <div class="pdf-toc__number">%%CH{{$index + 1}}%%</div>
        </a>
    </li>
    @endforeach
</ul>

<script class="pdf-script" type="text/php">
    $GLOBALS['max_object'] = count($pdf->get_cpdf()->objects);
</script>
</section>