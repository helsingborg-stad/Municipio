@foreach ($lqipPreloadUrls as $lqipPreloadUrl)
    <link
        rel="preload"
        as="image"
        href="{{ $lqipPreloadUrl }}"
        fetchpriority="{{ $lqipPreloadFetchPriority }}"
    >
@endforeach

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
