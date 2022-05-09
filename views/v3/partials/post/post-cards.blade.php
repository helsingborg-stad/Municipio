@if ($posts)
    <div class="o-grid">
        @foreach($posts as $post)
            <div class="{{ $gridColumnClass }}">
                @card([
                    'link' => $post->permalink,
                    'imageFirst' => true,
                    'image' =>  $post->thumbnail,
                    'heading' => $post->postTitle,
                    'classList' => ['t-archive-card', 'u-height--100', 'u-display--flex'],
                    'content' => $post->excerptShort,
                    'tags' => $post->termsunlinked,
                    'date' => $post->archiveDate,
                    'dateBadge' => ($post->archiveDateFormat == 'date-badge'),
                    'context' => ['archive', 'archive.list', 'archive.list.card'],
                    'containerAware' => true,
                    'hasPlaceholder' => $anyPostHasImage && !isset($post->thumbnail['src'])
                ])
                @endcard
            </div>
        @endforeach
    </div>
@endif
