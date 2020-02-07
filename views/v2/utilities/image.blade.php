@if(empty($src) && $placeholder === true)
    <div class="placeholder {{$class}}" title="{{$alt}}"></div>
@elseif(empty($src))
    <img src="{{$src}}" alt="{{$alt}}" class="{{$class}}"/>
@endif
