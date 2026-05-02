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

    @if (!empty($heroImagePreload['href']))
        <link
            rel="preload"
            as="image"
            href="{{ $heroImagePreload['href'] }}"
            @if (!empty($heroImagePreload['imagesrcset'])) imagesrcset="{{ $heroImagePreload['imagesrcset'] }}" @endif
            @if (!empty($heroImagePreload['imagesizes'])) imagesizes="{{ $heroImagePreload['imagesizes'] }}" @endif
            @if (!empty($heroImagePreload['fetchpriority'])) fetchpriority="{{ $heroImagePreload['fetchpriority'] }}" @endif
        >
    @endif

    {{-- Wordpress required call to wp_header() --}}
    {!! $wpHeader !!}

    {{-- Rss feed --}}
    @foreach ($rssFeedUrls as $rssFeedUrl)
        <link 
        rel="alternate" 
        type="application/rss+xml" 
        title="{{ $siteName }} » {{ $rssFeedUrl['rss2']->name }}" 
        href="{{ $rssFeedUrl['rss2']->url }}">
    @endforeach
</head>
