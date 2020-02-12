<!-- segment.blade.php > /sub/segment-split.blade.php -->
<div class="{{ $baseClass }}__content {{ $baseClass }}--template-{{$template}}">

    <div class="{{ $baseClass }}__column-article">
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
    </div>

    @if($slot == "")
        <div class="{{ $baseClass }}__column-graphics" style="background-image: url('{{ $image }}')">
        </div>
    @else
        <div class="{{ $baseClass }}__column-slot">
            {{ $slot }}
        </div>
    @endif
</div>