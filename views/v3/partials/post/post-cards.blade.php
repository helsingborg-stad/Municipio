@if ($posts)
    <div class="o-grid">
        @foreach ($posts as $post)
            <div class="{{ $gridColumnClass }}">
                @card([
                    'link' => $post->permalink,
                    'image' => $post->imageContract ?? $post->images['thumbnail16:9'],
                    'heading' => $post->postTitle,
                    'classList' => ['t-archive-card', 'u-height--100', 'u-display--flex', 'u-level-2'],
                    'content' => $post->excerptShort,
                    'tags' => $post->termsUnlinked,
                    'meta' => $displayReadingTime ? $post->readingTime : '',
                    'date' => $post->archiveDate,
                    'dateBadge' => $post->archiveDateFormat == 'date-badge',
                    'context' => ['archive', 'archive.list', 'archive.list.card'],
                    'containerAware' => true,
                    'hasPlaceholder' => $anyPostHasImage && empty($post->images['thumbnail16:9']['src'])
                ])
                @endcard
            </div>
        @endforeach
    </div>
@endif
