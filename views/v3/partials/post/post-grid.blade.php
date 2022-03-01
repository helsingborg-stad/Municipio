@if ($posts)
    <div class="o-grid">
        @foreach($posts as $post)
            <div class="o-grid-12 {{ $gridColumnClass }}">
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
                    'classList' => ['t-archive-block'],
                    'context' => ['archive', 'archive.list', 'archive.list.block'],
                ])
                @endblock
            </div>
        @endforeach
    </div>
@endif
