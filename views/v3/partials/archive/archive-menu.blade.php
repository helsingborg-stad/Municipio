@if($archiveMenuItems)
  @paper()
    <div class="o-grid u-print-display--none">
      <div class="o-grid-12">
        <div class="u-display--flex u-flex--gridgap u-align-content--center u-justify-content--space-between u-margin__x--2 u-margin__y--1">
          <nav role="navigation" aria-label="{{$lang->archiveNav}}" class="u-display--flex u-flex--gridgap u-margin__left--0@xs u-margin__left--2@sm u-margin__left--2@md u-margin__left--2@lg u-flex-wrap" style="row-gap: 0;">
            @foreach($archiveMenuItems as $item)
                @button([
                    'href'  => $item['href'], 
                    'text'  => $item['label'],
                    'size'  => 'md',
                    'style' => 'basic',
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
                'classList' => ['u-margin__y--auto', 'u-align-self--end'],
            ])
            @endbutton
          @endif
        </div>
      </div>
    </div>
  @endpaper
@endif