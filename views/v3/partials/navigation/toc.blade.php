@if (!empty($post) && method_exists($post, 'getContentHeadings') && !empty($post->getContentHeadings()))
    @element([
      'id' => 'table-of-contents',
      'componentElement' => 'div',
      'classList' => [
        'u-margin__bottom--4',
        'u-print-display--none',
        'u-display--none',
        'u-display--block@lg',
        'u-display--block@xl',
        'u-position--sticky',
      ]
    ])
      @element(['componentElement' => 'div', 'classList' => ['s-toc__header']])
        @typography([
          'id' => 'table-of-contents-heading', 
          'element' => 'h4', 
          'variant' => 'h4',
        ])
            {{ $lang->findOnPage }}
        @endtypography
      @endelement

      <nav aria-labelledby="table-of-contents-heading">
          @nav([
              'id' => 'menu-toc',
              'items' => $post->getContentHeadings(),
              'context' => ['site.toc.nav'],
              'height' => 'sm',
              'classList' => [
                  'c-nav--sidebar',            
                  'u-print-display--none',
                  's-nav-toc'
              ],
              'direction' => 'vertical',
              'context' => ['sidebar', 'municipio.sidebar', 'municipio.menu.toc'],
              'expandLabel' => $lang->expand,
              'indentSubLevels' => false
          ])
          @endnav
      </nav>
    @endelement
@endif