<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{!empty($cover['heading']) ? $cover['heading'] : $lang['generatedPdf']}}</title>
    @include('partials.content.pdf.typography')
    @include('partials.content.pdf.style')
</head>
<body>
    <footer>
        <span></span>
    </footer>
    
    @includeWhen(!empty($cover), 'partials.content.pdf.frontpage')

    @if (!empty($posts) && is_array($posts)) 
        @includeWhen($hasMoreThanOnePost, 'partials.content.pdf.table-of-contents')
    @foreach ($posts as $index => $post)
        @include('partials.content.pdf.post')
    @endforeach
        @if ($hasMoreThanOnePost)
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
</html>