@if (!empty($post->getContentHeadings()))
    @card([
      'id' => 'table-of-contents', 
      'classList' => [
        'u-margin__bottom--4',
        'u-print-display--none',
        'u-display--none',
        'u-display--block@lg',
        'u-display--block@xl',
        'u-position--sticky',
        'u-top--4',
        'u-level--8',
      ]
    ])
      @element(['element' => 'div', 'classList' => ['c-card__header']])
       @typography(['element' => 'h4', 'variant' => 'h3', 'classList' => ['c-card__heading']])
            {{ $lang->findOnPage }}
        @endtypography
      @endelement

      <nav aria-label="{{ $lang->findOnPage }}">
          @nav([
              'id' => 'menu-toc',
              'items' => $post->getContentHeadings(),
              'context' => ['site.toc.nav'],
              'height' => 'sm',
              'classList' => [
                  'c-nav--sidebar',            
                  'c-nav--bordered',
                  'u-print-display--none',
                  's-nav-toc',
                  'u-padding__bottom--1',
              ],
              'direction' => 'vertical',
              'context' => ['sidebar', 'municipio.sidebar', 'municipio.menu.toc'],
              'expandLabel' => $lang->expand,
              'indentSubLevels' => true,
              'includeToggle' => true
          ])
          @endnav
      </nav>
    @endpaper
@endif