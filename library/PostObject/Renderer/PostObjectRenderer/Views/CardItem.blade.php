@if($gridColumnClass)
    <div class="{!! $gridColumnClass !!}">
@endif

    @card([
        'link' => $postObject->getPermalink(),
        'image' => $postObject?->imageContract ?? $postObject?->images['thumbnail16:9'] ?? null,
        'heading' => $postObject->getTitle(),
        'classList' => ['t-archive-card', 'u-height--100', 'u-display--flex', 'u-level-2'],
        'content' => $postObject->excerptShort ?? null,
        'tags' => $postObject->termsUnlinked ?? null,
        'meta' => $displayReadingTime ? $postObject->readingTime : '',
        'date' => $postObject->archiveDate ?? null,
        'dateBadge' => $postObject->archiveDateFormat ?? null == 'date-badge',
        'context' => ['archive', 'archive.list', 'archive.list.card'],
        'containerAware' => true,
        'hasPlaceholder' => $showPlaceholder  && empty($postObject->images['thumbnail16:9']['src'])
    ])
    @endcard

@if($gridColumnClass)
    </div>
@endif

