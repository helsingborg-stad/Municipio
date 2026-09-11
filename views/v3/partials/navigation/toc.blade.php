@if (!empty($post) && method_exists($post, 'getContentHeadings') && !empty($post->getContentHeadings()))
    @card([
      'id' => 'table-of-contents', 
      'classList' => [
        'u-margin__bottom--4',
        'u-print-display--none',
        'u-display--none',
        'u-display--block@lg',
        'u-display--block@xl',
        'u-position--sticky',
        'u-level--7',
      ]
    ])
      @card__header()
        @typography([
          'id' => 'table-of-contents-heading', 
          'element' => 'h4', 
          'variant' => 'h4',
          'classList' => ['u-margin__y--0']
        ])
            {{ $lang->findOnPage }}
        @endtypography
      @endcard__header

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
              'indentSubLevels' => false,
              'style' => 'padding: inherit'
          ])
          @endnav
      </nav>
    @endcard
@endif