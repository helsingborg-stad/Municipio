@element([
        'classList' => [
            'u-padding__x--3',
            'u-padding__y--5',
            'u-padding__x--8@md',
            'u-padding__y--8@md',
            'u-padding__x--8@lg',
            'u-padding__y--8@lg',
            'u-padding__x--8@xl',
            'u-padding__y--8@xl',
            'u-rounded--16',
        ],
        'attributeList' => [
            'style' => 'background-color: var(--color-secondary);z-index:0;'
        ]
    ])
        @typography([ 'element' => 'h2', 'variant' => 'h2', 'classList' => ['u-margin__bottom--2'], 'attributeList' => ['style' => 'color: var(--color-secondary-contrasting);'] ])
            {!! $lang->relatedEventsTitle !!}
        @endtypography
        @include('posts-list', $postsListData)
    @endelement