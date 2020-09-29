@if ($posts)
    <div class="o-grid">
        @foreach($posts as $post)
        <div class="o-grid-12 {{$gridColumnClass}}">

                @card([
                    'href' => $post->permalink,
                    'imageFirst' => true,
                    'image' =>  ['src' => $post->featuredImage['src'], 'alt' => 'featured image'],
                    'heading' => $post->postTitle,
                    'classList' => ['archive-card'],
                    'byline' => ['text' => $post->postDate, 'position' => 'body'],
                    'content' => $post->excerpt,
                    'buttons' => [['text' => 'Go', 'href' => $post->permalink]]
                    ])
                @endcard

            </div>
        @endforeach
    </div>
@endif
