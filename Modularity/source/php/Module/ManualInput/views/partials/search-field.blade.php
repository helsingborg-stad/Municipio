<div class="c-card__body" aria-label="{{ $lang['search'] }}">
        @field([
            'type' => 'search',
            'name' => 'search',
            'label' => $lang['search'],
            'hideLabel' => true,
            'attributeList' => [
                'js-filter-input' => $ID
            ],
            'placeholder' => $lang['search']
        ])
        @endfield
</div>