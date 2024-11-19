<div class="archive-compressed__article u-margin__bottom--12 {{ $config['gridColumnClass'] }}">

    <article id="article" class="c-article">
        
        <!-- Title -->
        @typography(["element" => "h1"])
            @link(['href' =>  $postObject->href, 'classList' => ['archive-compressed__title-link']])
                {{ $postObject->postTitle }}
            @endlink
        @endtypography
        
        <!-- Content -->
        {!! $postObject->postContentFiltered !!}
        
        @if(isset($postObject->terms))
            @tags(['tags' => $postObject->terms])
            @endtags
        @endif
        
    </article>

    <!-- Dates -->
    @if(!empty($postObject->archiveDate))
        @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date', 'u-margin__top--4']])
            {{$lang->publish}}: 
            @date([
                'timestamp' => $postObject->archiveDate
            ])
            @enddate
        @endtypography
    @else
        @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date', 'u-margin__top--4']])
            {{$lang->publish}}: 
            @date([
                'action' => 'formatDate',
                'timestamp' => $postObject->postDate
            ])
            @enddate
        @endtypography	

        @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date']])
            {{$lang->updated}}: 
            @date([
                'action' => 'formatDate',
                'timestamp' => $postObject->postModified
            ])
            @enddate
        @endtypography	
    @endif
    
</div>