@if ($posts_data_source !== 'input' && !empty($archiveLinkUrl))
    @link([
        'href' => $archiveLinkUrl,
        'classList' => ['u-display-block']
    ])
        @group([
            'classList' => [
                'u-gap-1',
                'u-margin__top--1@xs',
                'u-margin__top--1@sm',
                'u-justify-content--end@md',
                'u-justify-content--end@lg',
                'u-justify-content--end@xl',
                'u-align-items--center'
            ]
        ])
            {{ $archiveLinkTitle ?? $lang['showMore'] }}
            @icon([
                'icon' => 'trending_flat',
                'size' => 'lg',
                'classList' => [$baseClass . '__icon']
            ])
            @endicon
        @endgroup
    @endlink
@endif
