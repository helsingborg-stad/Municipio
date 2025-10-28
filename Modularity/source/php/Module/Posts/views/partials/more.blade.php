@if ($posts_data_source !== 'input' && !empty($archiveLinkUrl))
    <div class="t-read-more-section u-display--flex u-align-content--center u-margin__y--4">
        @button([
          'text' => $archiveLinkTitle ?? $lang['showMore'],
          'color' => 'secondary',
          'style' => 'filled',
          'href' => $archiveLinkUrl,
          'classList' => ['u-flex-grow--1@xs', 'u-margin__x--auto'],
        ])
        @endbutton
    </div>
@endif
@if($paginationArguments)
  <div class="u-margin__y--4">
    @pagination($paginationArguments)@endpagination
  </div>
@endif
