@if ($posts)
    @grid([
        'container' => true,
        "columns" => "auto-fit",

        'max_width' => "230px",
        'row_gap' => 6,
        'col_gap' => '4'
    ])
        @foreach($posts as $post)

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

        @endforeach
    @endgrid
@endif
