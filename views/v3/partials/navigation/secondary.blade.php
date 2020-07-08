@if (is_array($items) && !empty($items))
    <ul>
        @foreach($items as $item)
        <li>
            <a href="{{$item['href']}}">{{$item['label']}}</a>

            @if ($item['children'])
                @include('partials.navigation.secondary', ['items' => $item['children']])
            @endif
        </li>

        @endforeach
    </ul>
@endif