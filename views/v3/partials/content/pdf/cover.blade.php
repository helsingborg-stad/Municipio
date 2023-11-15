<section class="pdf-frontpage">
    <div class="pdf-container">
        <header>
        @if(!empty($styles['logotype']))
            <img class="pdf-logotype" src="{{$styles['logotype']}}" style="max-width: 50%; max-height: 100px;">
        @endif
        </header>
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