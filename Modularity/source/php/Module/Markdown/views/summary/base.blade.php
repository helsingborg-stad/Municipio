@if (!empty($markdownUrl))
    <small class="u-display--block">
        <strong>{{ $language->sourceUrl }}:</strong> <a href="{{ $markdownUrl }}" class="u-text-decoration--none" target="_blank" rel="noopener noreferrer">
            {{ $markdownUrl }}
        </a>
    </small>
@endif
@if (!empty($markdownLastUpdated))
    <small class="u-display--block">
        <strong>{{ $language->lastUpdated }}:</strong> {{ $markdownLastUpdated }}
    </small>
    <small class="u-display--block">
        <strong>{{ $language->nextUpdate }}:</strong> {{ $markdownNextUpdate }}
    </small>
@endif