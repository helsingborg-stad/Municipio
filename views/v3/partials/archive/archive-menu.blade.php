@if($archiveMenuItems)

  <div class="o-grid u-print-display--none">
    <div class="o-grid-12">
      @paper()
        <div class="u-display--flex u-flex--gridgap-col u-flex-direction--column@xs u-flex-direction--column@sm u-align-content--center u-justify-content--space-between">
          <nav aria-label="{{$lang->archiveNav}}">
            @nav([
                'items' => $archiveMenuItems,
                'direction' => 'horizontal',
                'allowStyle' => false,
                'classList' => ['u-flex-wrap', 's-nav-archive', 'u-padding__x--2'],
                'context' => ['site.archive.nav'],
                'height' => 'md',
                'expandLabel' => $lang->expand
            ])
            @endnav
          </nav>
          @if($hasQueryParameters)
            <div class="u-padding--2 u-margin__y--auto@md u-margin__y--auto@lg u-margin__y--auto@xl  u-align-self--end u-width--100@xs u-width--100@sm">
              @button([
                  'href'  => $archiveResetUrl, 
                  'text'  => $lang->resetFacetting,
                  'size'  => 'sm',
                  'style' => 'filled',
                  'icon' => 'close',
                  'classList' => [
                    'u-width--100@xs',
                    'u-width--100@sm'
                  ],
              ])
              @endbutton
            </div>
          @endif
        </div>
      @endpaper
    </div>
  </div>
@endif