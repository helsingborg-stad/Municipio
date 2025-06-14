@card([
    'heading' => false,
    'attributeList' => [
        ...(!$hideTitle && !empty($postTitle) ? ['aria-labelledby' => 'mod-posts-' . $ID . '-label'] : []),
    ],
    'context' => 'module.posts.list'
])
@if ((!$hideTitle && !empty($postTitle)) || !empty($titleCTA))
<div class="c-card__header">
    @include('partials.post-title', ['variant' => 'h4', 'classList' => [], 'titleCTA' => $titleCTA ?? null])
</div>
@endif

    @if (!empty($posts))
        <div class="o-grid{{ !empty($stretch) ? ' o-grid--stretch' : '' }}">
            <div class="o-grid-12">
                @collection([
                    'sharpTop' => true,
                    'bordered' => true
                ])
                    @foreach ($posts as $post)
                        @if ($post->permalink && $post->getTitle())
                            @collection__item([
                                'icon' => $post->icon,
                                'link' => $post->permalink,
                                'attributeList' => array_merge($post->attributeList, [
                                    'aria-labelledby' => 'post-' . $ID . '-' . $post->getId() . '-title'
                                ]),
                                'classList' => $post->classList
                            ])
                                @typography([
                                    'element' => 'h2',
                                    'variant' => 'h4',
                                    'id' => 'post-' . $ID . '-' . $post->getId() . '-title',
                                ])
                                    {{ $post->getTitle() }}
                                @endtypography
                            @endcollection__item
                        @endif
                    @endforeach
                @endcollection
            </div>
        </div>
    @endif
@endcard

@include('partials.more')