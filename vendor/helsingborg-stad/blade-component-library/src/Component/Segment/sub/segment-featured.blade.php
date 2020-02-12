<!-- segment.blade.php > /sub/segment-featured.blade.php -->
<div class="{{ $baseClass }}__content">
    <div class="{{ $baseClass }}__article">
        @if($heading)
            @typography(["variant" => "h1",
                "element" => "h2",
                'classList' => [$baseClass.'__heading']
            ])
                {{ $heading }}
            @endtypography
        @endif

        @if($body)
            @typography([
                'variant' => 'body',
                'element' => 'p',
                'classList' => [$baseClass.'__body']
            ])
                {{ $body }}
            @endtypography
        @endif

        <div class="{{ $baseClass }}__cta">
            @foreach($cta as $button)      
                @button($button)
                @endbutton
            @endforeach
        </div>
    </div>

    <div class="{{ $baseClass }}__slot">
        {{ $slot }}
    </div>
</div>