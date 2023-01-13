@extends('templates.single')
@if (!empty($content))
    @foreach ($content as $key => $item)
        @section($key)
            <{{ $item['elementType'] }} {{ $item['attributes'] }}>
                {{ $item['content'] }}
                @php
                    echo '<pre>' . print_r($item, true) . '</pre>';
                @endphp
                </{{ $item['elementType'] }}>
            @stop
    @endforeach
@endif
