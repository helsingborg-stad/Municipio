<section class="pdf-toc {{!empty($cover) ? 'pdf-page-break' : ''}}">
<script class="pdf-script" type="text/php">
    $GLOBALS['chapters'] = array();
    $GLOBALS['max_object'] = 0;
</script>

<h2 class="pdf-heading--h1" style="margin-bottom:-4px;">{{ $lang['tableOfContents'] }}</h2>
<ul class="pdf-toc__list">
    @php
        $tableOfContentsPostIndex = 0;
    @endphp
    @foreach ($sortedPostsArray as $key => $sortedPosts)
        @if(count($sortedPostsArray) > 1)
            <h3 style="margin-bottom: 12px; margin-top: 4px;">{{$key}}</h3>
        @endif
        @foreach ($sortedPosts as $post)
            <li class="pdf-toc__list-item">
                <a class="pdf-toc__item {{strlen($post->postTitleFiltered) > 100 ? 'pdf-toc__item--big' : ''}}" href="#post-{{$tableOfContentsPostIndex}}">
                    <div class="pdf-toc__title">{{$post->postTitleFiltered}}</div>
                    <div class="pdf-toc__number">%%CH{{$tableOfContentsPostIndex + 1}}%%</div>
                </a>
            </li>
            @php 
                $tableOfContentsPostIndex++;
            @endphp
        @endforeach
    @endforeach
</ul>

<script class="pdf-script" type="text/php">
    $GLOBALS['max_object'] = count($pdf->get_cpdf()->objects);
</script>
</section>