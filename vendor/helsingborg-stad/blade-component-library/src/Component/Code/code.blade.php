<!-- code.blade.php -->
<{{$componentElement}} id="{{ $id }}" class="{{$class}}" {!! $attribute !!}>
@if($content)
<p class="{{$baseClass}}__description">{{$content}}</p>
@endif
<{{$preTagElement}} class="{{$baseClass}}__pre"><code class="{{$baseClass}}__output language-{{$language}}" data-type="{{ $language }}">{!!$slot!!}</code></{{$preTagElement}}>
</{{$componentElement}}>