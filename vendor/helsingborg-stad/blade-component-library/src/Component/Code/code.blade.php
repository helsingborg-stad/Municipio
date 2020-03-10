<!-- code.blade.php -->
<{{$componentElement}} id="{{ $id }}" class="{{$class}}" {!! $attribute !!}>
    @if($content)
    <p class="{{$baseClass}}__description">{{$content}}</p>
    @endif

    <div class="{{$baseClass}}__sidebar">
        @icon([
            'icon' => 'code',
            'size' => 'md',
            'color' => 'white'
        ])
        @endicon
    </div>

    <{{$preTagElement}} class="{{$baseClass}}__pre"><code class="{{$baseClass}}__output language-{{$language}}" data-type="{{ $language }}">{!!$slot!!}</code></{{$preTagElement}}>
</{{$componentElement}}>