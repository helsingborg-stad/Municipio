<div class="archive-compressed__article u-margin__bottom--12" data-js-posts-list-item>

    <article id="article" class="c-article">
        
        @typography(["element" => "h1"])
            @link(['href' =>  $post->getPermalink(), 'classList' => ['archive-compressed__title-link']])
                {{ $post->getTitle() }}
            @endlink
        @endtypography
        
        {!! $post->getContent() !!}
        
        @if(!empty($getTags($post)))
            @tags(['tags' => $getTags($post)])
            @endtags
        @endif
        
    </article>

    @if(!empty($getDateTimestamp($post)))
        @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date', 'u-margin__top--4']])
            {{$lang->publish}}: 
            @if($getDateFormat()) 
                @date([
                    'action' => 'formatDate',
                    'timestamp' => $getDateTimestamp($post),
                    'format' => $getDateFormat()
                ])
                @enddate
            @endif
        @endtypography
    @endif
    
</div>