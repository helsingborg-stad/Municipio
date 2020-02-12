@if($tags)
    <{{ $componentElement }} id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
        @foreach($tags as $tag)
            @link(['href' => $tag['href'], 'classList' => ['c-tag', 'c-tag--'.  $tag['color']] ])
                {{ $beforeLabel }}{{ $tag['label'] ?? 'Undefined label' }}{{ $afterLabel }}
            @endlink
        @endforeach
    </{{ $componentElement }}>
@endif
