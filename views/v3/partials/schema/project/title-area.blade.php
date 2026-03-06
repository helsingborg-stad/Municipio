@hero([ 'image' => $image, 'heroView' => 'twoColumn', 'classList' => ['u-color__bg--lightest'] ])
    @slot('content')
        @if (!empty($technology))
            @typography([ 'element' => 'span', 'classList' => ['page-header_meta'] ]) {!! $technology !!} @endtypography
        @endif
        @typography([
            'element' => 'h1',
            'variant' => 'h1',
            'classList' => ['page-header__title', 'u-margin__top--0', 'u-margin__bottom--3']
        ])
            {{ $post->postTitle }}
        @endtypography

        @typography([ 'element' => 'b', ])
            {{$progressLabel}}
        @endtypography
        @progressBar([ 'value' => $progressPercentage ]) @endprogressBar

    @endslot
@endhero
