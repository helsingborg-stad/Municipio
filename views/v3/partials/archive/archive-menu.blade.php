@if($archiveMenuItems)

  <div class="o-grid u-print-display--none">
    <div class="o-grid-12">
      @paper()
        <div class="u-display--flex u-flex--gridgap-col u-flex-direction--column@xs u-flex-direction--column@sm u-align-content--center u-justify-content--space-between u-padding__x--2 u-padding__y--1">
          <nav role="navigation" aria-label="{{$lang->archiveNav}}" class="u-display--flex u-flex--gridgap u-margin__left--0@xs u-margin__left--0@sm u-margin__left--2@md u-margin__left--2@lg u-flex-wrap" style="row-gap: 0;">
            @foreach($archiveMenuItems as $item)
                @button([
                    'href'  => $item['href'], 
                    'text'  => $item['label'],
                    'size'  => 'md',
                    'color' => $item['active'] ? 'primary' : 'default',
                    'style' => $item['active'] ? 'basic' : 'basic',
                    'icon' => $item['icon']['icon'] ?? null,
                    'attributeList' => [
                        'role' => 'menuitem'
                    ],
                    'classList' => [
                        'u-margin--0'
                    ],
                    'reversePositions' => true
                ])
                @endbutton
            @endforeach
          </nav>
          @if($hasQueryParameters)
            @button([
                'href'  => $archiveBaseUrl, 
                'text'  => $lang->resetFacetting,
                'size'  => 'sm',
                'style' => 'filled',
                'icon' => 'close',
                'classList' => [
                  'u-margin__y--1@xs',
                  'u-margin__y--1@sm',
                  'u-margin__y--auto@md',
                  'u-margin__y--auto@lg',  
                  'u-align-self--end', 
                  'u-width--100@xs', 
                  'u-width--100@sm'
                ],
            ])
            @endbutton
          @endif
        </div>
      @endpaper
    </div>
  </div>
@endif