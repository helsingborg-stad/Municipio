<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{!! $pageTitle !!}</title>

    <meta name="pubdate" content="{{ $pagePublished }}">
    <meta name="moddate" content="{{ $pageModified }}">

    <meta name="mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=yes">
    <meta name="HandheldFriendly" content="true">

    <script>
        var ajaxurl = '{!! $ajaxUrl !!}';
    </script>

    @if ($structuredData)
        <script type="application/ld+json">
        {!! $structuredData !!}
        </script>
    @endif

    {{-- Styles. Use @push('styles') --}}
    @stack('styles')

    {{-- Wordpress required call to wp_header() --}}
    {!! $wpHeader !!}

</head>