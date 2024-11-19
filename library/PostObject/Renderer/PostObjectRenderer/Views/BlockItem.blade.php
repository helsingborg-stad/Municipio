@if($gridColumnClass)
    <div class="{{ $gridColumnClass }}">
@endif

    @block([
        'link' => $postObject->permalink,
        'heading' => $postObject->postTitle,
        'ratio' => $format,
        'meta' => $postObject->termsUnlinked,
        'secondaryMeta' => $displayReadingTime ? $postObject->readingTime : '',
        'image' => $postObject->imageContract ? [
            'src' => $postObject->imageContract,
            'backgroundColor' => 'secondary'
        ] : [
            'src' => $format == '12:16' ? $postObject->images['thumbnail3:4']['src'] : $postObject->images['thumbnail16:9']['src'],
            'alt' => $postObject->images['thumbnail16:9']['alt'] ? $postObject->images['thumbnail16:9']['alt'] : $postObject->postTitle,
            'backgroundColor' => 'secondary'
        ],
        'date' => $postObject->archiveDate,
        'dateBadge' => $postObject->archiveDateFormat == 'date-badge',
        'classList' => ['t-archive-block'],
        'context' => ['archive', 'archive.list', 'archive.list.block']
    ])
    @endblock

@if($gridColumnClass)
    </div>
@endif