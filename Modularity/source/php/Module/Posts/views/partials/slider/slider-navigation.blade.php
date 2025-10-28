    <div class="o-grid-12@sm o-grid-4@md o-grid-4@lg u-display--flex u-align-items--end u-justify-content--end">
        @if (($posts_data_source !== 'input' && $archiveLinkUrl))
        <div class="t-read-more-section">
            @button([
                'text' => $archiveLinkTitle ?? $lang['showMore'],
                'color' => 'default',
                'style' => 'basic',
                'href' => $archiveLinkUrl,
                'classList' => ['u-flex-grow--1@xs', 'u-margin__right--2']
            ])
            @endbutton
        </div>
        @endif
        <div class="c-slider__arrows" id="slider_{{ $sliderId }}">
            @button([
                'icon' => 'keyboard_arrow_left',
                'style' => 'filled',
                'color' => 'primary',
                'ariaLabel' => $ariaLabels->prev,
                'attributeList' => [
                    'data-js-slider-prev' => true
                ]
            ])
            @endbutton
            @button([
                'icon' => 'keyboard_arrow_right',
                'style' => 'filled',
                'color' => 'primary',
                'ariaLabel' => $ariaLabels->next,
                'attributeList' => [
                    'data-js-slider-next' => true
                ]
            ])
            @endbutton
        </div>
    </div>