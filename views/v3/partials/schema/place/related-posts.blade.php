@element([
    'classList' => ['o-container', 'o-layout-grid', 'o-layout-grid--gap-10']
])
    @typography([
        'element' => 'h2',
        'variant' => 'h2',
    ])
        {{ $lang->related }} {{ $postTypeDetails->labels->name }}
    @endtypography

    @element([
        'classList' => [
            'o-layout-grid',
            'o-layout-grid--cq',
            'o-layout-grid--cols-1',
            'o-layout-grid--cols-3@md',
            'o-layout-grid--gap-10'
        ]
    ])
    @foreach ($relatedPosts as $post)
        <div class="o-grid-4@md u-margin__bottom--8">
            @segment([
                'layout' => 'card',
                'title' => $post->postTitleFiltered,
                'content' => !empty($post->excerptShorter) ? $post->excerptShorter : false,
                'image' => !empty($post->thumbnail['src']) ? $post->thumbnail['src'] : false,
                'buttons' => [['text' => $labels['readMore'], 'href' => $post->permalink]],
                'tags' => !empty($post->termsUnlinked) ? $post->termsUnlinked : false,
                'meta' => !empty($post->readingTime) ? $post->readingTime : false,
                'icon' => !empty($post->termIcon['icon']) ? $post->termIcon : false
            ])

            @if (!empty($post->callToActionItems['floating']['icon']) && !empty($post->callToActionItems['floating']['wrapper']))
                @slot('floating')
                    @element($post->callToActionItems['floating']['wrapper'] ?? [])
                        @icon($post->callToActionItems['floating']['icon'])
                        @endicon
                    @endelement
                @endslot
            @endif
            @endsegment
        </div>
    @endforeach
    @endelement
@endelement