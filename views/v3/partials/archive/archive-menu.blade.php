@if($archiveMenuItems)
  <div class="o-grid">
    <div class="o-grid-12">
      @paper()
        <nav role="navigation" aria-label="{{$lang->archiveNav}}"
            class="u-display--flex u-print-display--none u-margin__x--2">
              @foreach($archiveMenuItems as $item)
                  @button([
                      'href'  => $item['href'], 
                      'text'  => $item['label'],
                      'size'  => 'lg',
                      'style' => 'basic',
                      'icon' => $item['icon']['icon'] ?? null,
                      'attributeList' => [
                          'role' => 'menuitem'
                      ],
                      'reversePositions' => true,
                      'classList' => [
                        'u-padding__x--2'
                      ]
                  ])
                  @endbutton
              @endforeach
        </nav>
      </div>
    @endpaper
  </div>
@endif