<div class="archive-compressed">
    @foreach($posts as $post)
        @includeIf('partials.article', 
            [
                'postTitleFiltered' => $post->postTitle, 
                'postContentFiltered' => $post->excerpt, 
                'permalink' => $post->permalink,
                'feature_image' => (object) $post->featuredimage
            ]
        )
    @endforeach
</div>