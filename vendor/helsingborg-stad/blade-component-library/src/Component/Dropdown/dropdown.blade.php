<{{$componentElement}} id="{{ $id }}" class="{{ $class }}">   
    
    {{$slot}}
    
    <div class="{{$baseClass}}__list {{$baseClass}}__list--{{$direction}}">
        <div>
            <ul>
                @foreach ($items as $item)
                <{{$itemElement}} href="{{$item['link']}}"><li>{{$item['text']}}</li></{{$itemElement}}>
                @endforeach
            </ul>
        </div>
    </div>  
    
</{{$componentElement}}>