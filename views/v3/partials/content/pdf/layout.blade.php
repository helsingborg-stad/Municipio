<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Test</title>
    <meta name="author" content="Helsingborgs stad">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"> --}}
    @foreach($fonts as $font) 
        @if(!empty($font['google-font']))
            <link href="{{$font['google-font']}}" rel="stylesheet">
        @endif
    @endforeach
    @include('partials.content.pdf.typography')
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
    @includeWhen(!empty($cover), 'partials.content.pdf.frontpage')
    {{-- @dump($styles['typography_h1']) --}}
    @if (!empty($posts) && is_array($posts)) 
        @includeWhen(count($posts) > 1, 'partials.content.pdf.table-of-contents')
    @foreach ($posts as $index => $post)
        @include('partials.content.pdf.post')
    @endforeach
        @if (count($posts) > 1)
            <script class="pdf-script" type="text/php">
                for ($i = 0; $i <= $GLOBALS['max_object']; $i++) {
                    if (!array_key_exists($i, $pdf->get_cpdf()->objects)) {
                        continue;
                    }
                    $object = $pdf->get_cpdf()->objects[$i];
                    foreach ($GLOBALS['chapters'] as $chapter => $page) {
                        if (array_key_exists('c', $object)) {
                            $object['c'] = str_replace( '%%CH' . $chapter . '%%', $page, $object['c'] );
                            $pdf->get_cpdf()->objects[$i] = $object;
                        }
                    }
                }
            </script>
        @endif
    @endif
</body>

<!-- /Article pages -->

<!-- Back page -->
{{-- @include('pdf.backpage') --}}
<!-- /Back page -->
</html>