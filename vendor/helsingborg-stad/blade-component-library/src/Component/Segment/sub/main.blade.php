<div class="{{ $baseClass }}__main">

    @if($slot)
        {{$slot}}
    @endif

    @if (isset($main))
        {{ $main }}
    @endif

    @if(!empty($text))
        @typography([
            'variant' => 'body',
            'element' => 'p',
            'classList' =>  [$baseClass.'__body']
        ])
            {{ $text}}
        @endtypography
    @endif
</div>