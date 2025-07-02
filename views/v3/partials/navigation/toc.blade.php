@if (!empty($post->getContentHeadings()))
    @paper([
      'id' => 'table-of-contents', 
      'padding' => '4', 
      'class' => 'u-margin__bottom--4'
    ])
        @typography(['element' => 'h2', 'variant' => 'h2'])
            {{ $lang->findOnPage }}
        @endtypography

        @listing([
            'list' => $post->getContentHeadings(),
            'elementType' => 'ul',
            'class' => 'toc-list',
            'baseClass' => 'toc-item',
            'icon' => 'home',
        ])
        @endlisting
    @endpaper
@endif