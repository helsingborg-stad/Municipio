<article class="pdf-post {{ !empty($cover) || $hasMoreThanOnePost ? 'pdf-page-break' : '' }}">
    <script class="pdf-script" type="text/php">
        $GLOBALS['chapters']['{{$postIndex + 1}}'] = $pdf->get_page_number();
    </script>
    @if (!empty($post->postTitleFiltered))
        <h2 class="pdf-post__heading pdf-heading--h1" id="post-{{$postIndex}}">{{ $post->postTitleFiltered }}</h2>
    @endif
    @if (!empty($post->images['featuredImage']['src']))
        <img class="pdf-post__featured-image" src="{{$post->images['featuredImage']['src']}}" alt="{{$post->images['featuredImage']['alt'] ?? ''}}">
    @endif
    @if (!empty($post->postContentFiltered))
        {!! $post->postContentFiltered !!}
    @endif
</article>