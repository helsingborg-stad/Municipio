@if ($posts)
    <div class="o-grid">
        @foreach ($posts as $post)
            <div class="{{ $gridColumnClass }}">
                @block([
                    'link' => $post->permalink,
                    'heading' => $post->postTitle,
                    'ratio' => $archiveProps->format == 'tall' ? '12:16' : '1:1',
                    'meta' => $post->termsUnlinked,
                    'secondaryMeta' => $displayReadingTime ? $post->readingTime : '',
                    'image' => $post->imageContract ?? null ? [
                        'src' => $post->imageContract,
                        'backgroundColor' => 'secondary'
                    ] : [
                        'src' => $archiveProps->format == 'tall' ? $post->images['thumbnail3:4']['src'] ?? false : $post->images['thumbnail16:9']['src'] ?? false,
                        'alt' => $post->images['thumbnail16:9']['alt'] ?? '' ? $post->images['thumbnail16:9']['alt'] ?? '' : $post->postTitle,
                        'backgroundColor' => 'secondary'
                    ],
                    'date' => [
                        'timestamp' => $post->getArchiveDateTimestamp(),
                        'format'    => $post->getArchiveDateFormat(),
                    ],
                    'dateBadge' => \Municipio\Helper\DateFormat::getUnresolvedDateFormat($post) == 'date-badge',
                    'classList' => ['t-archive-block'],
                    'context' => ['archive', 'archive.list', 'archive.list.block'],
                ])
                @endblock
            </div>
        @endforeach
    </div>
@endif
