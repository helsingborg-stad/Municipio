
@card([
        'href' => $post->permalink,
        'imageFirst' => false,
        'image' =>  ['src' => $post->featuredimage, 'alt' => 'featured image'],
        'heading' => $post->postTitle,
        'classList' => ['c-card--shadow-on-hover'],
        'byline' => ['text' => $post->postDate, 'position' => 'body'],
        'content' => $post->postContent,
        'buttons' => [['text' => 'Go', 'href' => $post->permalink]],
    ])

@endcard
