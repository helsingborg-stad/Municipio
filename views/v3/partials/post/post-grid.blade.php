@if ($posts)
    <div class="o-grid">
        @foreach($posts as $post)
            <div class="{{ $gridColumnClass }}">
                @block([
                    'link' => $post->permalink,
                    'heading' =>  $post->postTitle,
                    'ratio' => '12:16',
                    'meta' => $post->termsunlinked,
                    'filled' => true,
                    'image' => [
                        'src' => $post->thumbnailTall['src'],
                        'alt' => $post->thumbnailTall['alt'] ? $post->thumbnailTall['alt'] : $post->postTitle,
                        'backgroundColor' => 'secondary',
                    ],
                    'date' => $post->archiveDate,
                    'classList' => ['t-archive-block'],
                    'context' => ['archive', 'archive.list', 'archive.list.block'],
                    'ratio' => $archiveProps->format == 'tall' ? '12:16' : '4:3',
                ])
                @endblock
            </div>
        @endforeach
    </div>
@endif