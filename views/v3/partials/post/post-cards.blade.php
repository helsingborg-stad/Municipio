@if ($posts)
    <div class="o-grid">
        @foreach($posts as $post)
            <div class="o-grid-12 {{ $gridColumnClass }}">
                @card([
                    'link' => $post->permalink,
                    'imageFirst' => true,
                    'image' =>  $post->thumbnail,
                    'heading' => $post->postTitle,
                    'classList' => ['t-archive-card', 'u-height--100', 'u-height-100', 'u-flex-direction--column', 'u-display--flex'],
                    'byline' => ['text' => $post->postDate, 'position' => 'body'],
                    'content' => $post->excerptShort,
                    'tags' => $post->termsunlinked,
                    'context' => ['archive', 'archive.list', 'archive.list.card']
                ])
                @endcard
            </div>
        @endforeach
    </div>
@endif
