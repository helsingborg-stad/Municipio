<section class="pdf-frontpage">
    {{-- <header>
         @if(!empty($styles['logotype']))
        <img class="pdf-logotype" src="{{$styles['logotype']}}" style="max-width: 50%; max-height: 100px;">
        @endif
    </header> --}}
        <div style="{{
        !empty($cover['cover']['src']) ? 'background-image: url(' . $cover['cover']['src'] . ');' : ''}}; height: 50%; width: 100%; background-size:cover; background-repeat:no-repeat; background-color: {{$styles['color_palette_primary']['contrasting'] ?? '#fff'}}">
        </div>
   {{--  @else 
        @if(!empty($styles['logotype']))
            <img class="pdf-logotype" src="{{$styles['logotype']}}" style="max-width: 50%; max-height: 100px;">
        @endif --}}
    <div class="pdf-container" style="height: 50%;">
        <div class="pdf-frontpage__heading" class="pdf-frontpage__heading">
            @if (!empty($cover['heading']))
                <h1 class="pdf-heading">{{ $cover['heading'] }}</h1>
            @endif
            @if (!empty($cover['introduction']))
                <p class="pdf-introduction">{!! $cover['introduction'] !!}</p>
            @endif
        </div>
    </div>
</section>