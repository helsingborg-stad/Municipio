<div class="search-result-item u-margin__bottom--4">

    @if(isset($result['topMostPostParent']))
        @typography(['variant' => 'h4', 'element' => 'h4', 'classList' => ['u-margin__bottom--1']])

            @link([
                'href' => $result['topMostPostParent']->href,
                'classList' => ['search-result-item__parent-title-link']
            ])

                {{$result['topMostPostParent']->post_title}}

            @endlink

        @endtypography
    @endif

    @typography(['variant' => 'h3', 'element' => 'h3'])
        @link([
            'href' => $result['permalink'],
            'classList' => ['search-result-item__parent-title-link']
        ])
            {{$result['postParent']->post_title}}
        @endlink

        @typography(['variant' => 'span', 'element' => 'span'])
            /
        @endtypography

        @link([
            'href' => $result['permalink'],
            'classList' => ['search-result-item__title-link']
        ])
            {{$result['title']}}
        @endlink
          
    @endtypography

    @typography(['variant' => 'caption'])
        <span class="search-result-item__link-prefix"> 
            Link: 
        </span>
        @link([
            'href' => $result['permalink']
        ])
            {{$result['permalink']}}
        @endlink

    @endtypography
</div>





