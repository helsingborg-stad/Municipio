<article class="pdf-page-break">
    <script type="text/php">
        $GLOBALS['chapters']['{{$index + 1}}'] = $pdf->get_page_number();
    </script>
    @if (!empty($post->postTitleFiltered))
    <h2 class="pdf-post__heading pdf-heading--h1" id="post-{{$index}}">{{ $post->postTitleFiltered }}</h2>
    @endif

    @if (!empty($post->postContentFiltered))
        {!! $post->postContentFiltered !!}
    @endif
</article>