@if (!empty($preFooterImageUrl))
    @element([
        'classList' => $preFooterImageClassList,
        'attributeList' => [
            'style' => $preFooterImageStyle,
            'aria-hidden' => 'true',
        ],
    ])
        <img class="site-pre-footer-image__image" src="{{ $preFooterImageUrl }}" alt="" loading="lazy" decoding="async">
    @endelement
@endif
