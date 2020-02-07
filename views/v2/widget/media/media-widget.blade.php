@if(isset($image) && !empty($image))
{!! $args['before_widget'] !!}

    @if(isset($url) && !empty($url))
        <a class="c-media" href="{{$url}}">
            {!! $image !!}
        </a>
    @else
        <div class="c-media">
            {!! $image !!}
        </div>
    @endif

{!! $args['after_widget'] !!}
@endif
