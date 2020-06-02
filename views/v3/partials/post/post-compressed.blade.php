
@foreach($posts as $post)
    <article id="article" class="archive-compressed__article u-margin__bottom--12">
        <!-- Title -->
        @typography(["element" => "h4", 'classList' => ['u-margin__bottom--2']])
        {{ ucfirst($postType) }}
        @endtypography
        
        <!-- Title -->
        @typography(["element" => "h1"])
        @link(['href' =>  $post->href, 'classList' => ['archive-compressed__title-link']])
        {{ $post->postTitle }}
        @endlink
        @endtypography
        
        <!-- Content -->
        @typography([])
        {!! $post->postContentFiltered !!}
        @endtypography	
        
        @if(isset($post->terms))
            @tags(['tags' => $post->terms])
            @endtags
        @endif

        <!-- Dates -->
        @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date', 'u-margin__top--4']])
            Published: {{$post->postDate}}
        @endtypography	

        @typography(['variant' => 'meta', 'element' => 'p', 'classList' => ['archive-compressed__date']])
            Updated: {{$post->postModified}}
        @endtypography	
        
        <!-- Comments -->
        @includeIf('comments')
        
    </article>
@endforeach
