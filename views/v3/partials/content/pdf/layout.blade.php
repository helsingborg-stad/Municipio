<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Test</title>
    <meta name="author" content="Helsingborgs stad">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

    @include('partials.content.pdf.style')
</head>
<body>
    <footer>
        <span></span>
    </footer>

    {{-- @dump(get_option('kirki_downloaded_font_files')) --}}
    {{-- @dump(get_theme_mods()) --}}
    {{-- <div class="pagi"><span></span></div> --}}
    <!-- Front page -->
    {{-- @include('pdf.frontpage') --}}
    <!-- /Front page -->
    <!-- Article pages -->
    @include('partials.content.pdf.frontpage')
    {{-- @dump($styles['typography_h1']) --}}
    @if (!empty($posts)) 
    <section class="pdf-toc" class="pdf-page-break">
        @include('partials.content.pdf.table-of-contents')
    </section>
    @foreach ($posts as $index => $post)
        <article class="pdf-post">
            @include('partials.content.pdf.post')
        </article>
    @endforeach
    @endif


<script type="text/php">
	foreach ($GLOBALS['chapters'] as $chapter => $page) {
		$pdf->get_cpdf()->objects[$GLOBALS['backside']]['c'] = str_replace( '%%CH'.$chapter.'%%' , $page , $pdf->get_cpdf()->objects[$GLOBALS['backside']]['c'] );
	}
	$pdf->page_script('
		if ($PAGE_NUM==2 ) {
			$pdf->add_object($GLOBALS["backside"],"add");
			$pdf->stop_object($GLOBALS["backside"]);
		} 
	');
</script>
    
</body>

<!-- /Article pages -->

<!-- Back page -->
{{-- @include('pdf.backpage') --}}
<!-- /Back page -->
</html>