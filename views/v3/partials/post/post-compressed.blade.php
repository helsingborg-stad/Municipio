<div class="o-grid">
    @foreach($posts as $post)
        <div class="archive-compressed__article u-margin__bottom--12 {{ $gridColumnClass }}">

            <article id="article" class="c-article">
                
                <!-- Title -->
                @typography(["element" => "h1"])
                    @link(['href' =>  $post->href, 'classList' => ['archive-compressed__title-link']])
                        {{ $post->postTitle }}
                    @endlink
                @endtypography
                
                <!-- Content -->
                {!! $post->postContentFiltered !!}
                
                @if(isset($post->terms))
                    @tags(['tags' => $post->terms])
                    @endtags
                @endif
                
            </article>

            <!-- Dates -->
            @if(!empty($post->archiveDate))
                @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date', 'u-margin__top--4']])
                    {{$lang->publish}}: 
                    @date([
                        'timestamp' => $post->archiveDate
                    ])
                    @enddate
                @endtypography
            @else
                @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date', 'u-margin__top--4']])
                    {{$lang->publish}}: 
                    @date([
                        'action' => 'formatDate',
                        'timestamp' => $post->getPublishedTime()
                    ])
                    @enddate
                @endtypography	

                @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date']])
                    {{$lang->updated}}: 
                    @date([
                        'action' => 'formatDate',
                        'timestamp' => $post->getModifiedTime()
                    ])
                    @enddate
                @endtypography	
            @endif
            
        </div>
    @endforeach
</div>