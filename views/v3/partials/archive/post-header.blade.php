<header>
        @link([
            'href' => the_permalink()
        ])
            @typography([
                "variant" => "h1",
                "element" => "h1",
            ])
                {{the_title()}}
            @endtypography
    @endlink

    @include('partials.archive.post-info')
</header>
