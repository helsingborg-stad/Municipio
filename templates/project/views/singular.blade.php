@extends('templates.single')
@if ($content)
    @foreach ($content as $key => $item)
        @section($key)
            <{{ $item['elementType'] }} {{ $item['attributes'] }}>
                {!! $item['content'] !!}
                </{{ $item['elementType'] }}>
            @stop
    @endforeach
@endif
