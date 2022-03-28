@if ($posts)
    <div class="o-grid">
        @foreach($posts as $post)
            <div class="{{ $gridColumnClass }}">
                @block([
                    'link' => $post->permalink,
                    'heading' =>  $post->postTitle,
                    'ratio' => $archiveProps->format == 'tall' ? '12:16' : '4:3',
                    'meta' => $post->termsunlinked,
                    'filled' => true,
                    'image' => [
                        'src' => $archiveProps->format == 'tall' ? $post->thumbnailTall['src'] : $post->thumbnail['src'],
                        'alt' => $post->thumbnailTall['alt'] ? $post->thumbnailTall['alt'] : $post->postTitle,
                        'backgroundColor' => 'secondary',
                    ],
                    'date' => $post->archiveDate,
                    'dateBadge' => ($post->archiveDateFormat == 'date-badge'),
                    'classList' => ['t-archive-block'],
                    'context' => ['archive', 'archive.list', 'archive.list.block'],
                ])
                @endblock
            </div>
        @endforeach
    </div>
@endif