@modal([
    'heading' => !empty($post->title) ? $post->title . ": @" . $post->user_screen_name : "@" . $post->user_screen_name,
    'id' => 'modal-' . $post->id,
    'size' => 'md',
    'padding' => 3
])
    <div class="o-grid">
        @if (!empty($post->oembed) || !empty($post->image))
            <div class="o-grid-6@md">
                @if (!empty($post->oembed))
                    {!! $post->oembed !!}
                @else
                    <div class="u-position--relative">
                        @image([
                            'src' => $post->image,
                            'classList' => ['u-object-fit--cover']
                        ])
                        @endimage

                        @avatar([
                            'image' => $post->user_image,
                            'name' => $post->user_screen_name,
                            'size' => 'sm',
                            'classList' => ['u-margin--2', 'u-border-color--white', 'u-position--absolute', 'u-top--0', 'u-left--0']
                        ])
                        @endavatar

                    </div>
                @endif
            </div>
            <div class="o-grid-5@md u-padding__left--4@md">
        @else
            <div class="o-grid-12">
        @endif

        @if(!empty($post->full_text))
            @typography([
                'classList' => ['u-margin__top--1', 'u-margin__bottom--2']
            ])
                {{ $post->full_text }}
            @endtypography
        @endif

        @typography([
            'element' => 'small',
            'classList' => ['u-color__text--light', 'u-margin__bottom--0', 'u-margin__top--1']
        ])
            Posted on: {{ $post->formatted_date }}
        @endtypography
    </div>
    </div>

    @slot('bottom')
        
        

        @button([
            'text' => $i18n['goToOriginalPost'],
            'color' => 'primary',
            'size' => 'md',
            'style' => 'filled',
            'href' => $post->url,
            'classList' => ['u-width--100']
        ])
        @endbutton
    @endslot
@endmodal
