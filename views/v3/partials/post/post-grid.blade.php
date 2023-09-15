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
                    'filled' => true,
                    'image' => [
                        'src' => $post->thumbnailTall['src'],
                        'alt' => $post->thumbnailTall['alt'] ? $post->thumbnailTall['alt'] : $post->postTitle,
                        'backgroundColor' => 'secondary'
                    ],
                    'date' => $post->archiveDate,
                    'dateBadge' => $post->archiveDateFormat == 'date-badge',
                    'classList' => ['t-archive-block'],
                    'context' => ['archive', 'archive.list', 'archive.list.block'],
                    'hasPlaceholder' => $anyPostHasImage && !isset($post->thumbnail['src']) && !isset($post->thumbnailTall['src'])
                ])
                @endblock
            </div>
        @endforeach
    </div>
@endif
