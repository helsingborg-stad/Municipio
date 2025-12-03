<section class="pdf-frontpage">
        <div style="{{!empty($cover['cover']['src']) ? 'background-image: url(' . $cover['cover']['src'] . ')' : ''}}; height: 50%; width: 100%; background-size:cover; background-repeat:no-repeat; background-color: {{$styles['color_palette_primary']['contrasting'] ?? '#fff'}}">
        </div>
        <div class="pdf-container" style="height: 50%;">
        @if(!empty($cover['emblem']['src']))
            <img class="pdf-frontpage__emblem" src="{{$cover['emblem']['src']}}">
        @endif
        @if (!empty($cover['heading']))
            <h1 class="pdf-frontpage__heading">{{ $cover['heading'] }}</h1>
        @endif
        @if (!empty($cover['introduction']))
            <div style="text-align:right;" class="pdf-frontpage__introduction">{!! $cover['introduction']!!}</div>
        @endif
    </div>
</section>