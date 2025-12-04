@modal([
    'id' => 'modal-' . $post->id,
    'size' => 'md'
])
    <div class="o-grid">
        @if (!empty($post->oembed) || !empty($post->image))
            <div class="o-grid-6@md">
                @if (!empty($post->oembed))
                    {!! $post->oembed !!}
                @else
                    @image([
                        'src' => $post->image
                    ])
                    @endimage
                @endif
            </div>
            <div class="o-grid-5@md u-padding__left--4@md">
            @else
                <div class="o-grid-12">
        @endif
        <header class="o-container o-container--fullwidth o-container--remove-spacing u-margin__left u-margin__top u-margin__bottom--1">
            @avatar([
                'image' => $post->user_image,
                'name' => $post->user_screen_name,
                'size' => 'sm',
                'classList' => ['u-float--left', 'u-margin__right--1']
            ])
            @endavatar

            @typography([
                'variant' => 'h6',
                'classList' => ['u-margin__top--0', 'u-padding--1', 'u-padding__top--0']
            ])
                {{ $post->user_screen_name }}
            @endtypography
        </header>
        @typography([
            'element' => 'small',
            'classList' => ['u-color__text--light', 'u-margin__bottom--0', 'u-margin__top--1']
        ])
            {{ $post->formatted_date }}
        @endtypography
        @if(!empty($post->title))
            @typography([
                'element' => 'h2',
                'variant' => 'h4',
                'classList' => ['u-margin--0']
            ])
                {{ $post->title }}
            @endtypography
        @endif
        @if(!empty($post->full_text))
            @typography([
                'classList' => ['u-margin__top--1', 'u-margin__bottom--2']
            ])
                {{ $post->full_text }}
            @endtypography
        @endif
        @button([
            // TODO: How to position the button centered at the bottom of the post?
            'text' => $i18n['goToOriginalPost'],
            'color' => 'default',
            'size' => 'sm',
            'style' => 'filled',
            'href' => $post->url,
        ])
        @endbutton
    </div>
    </div>
@endmodal
