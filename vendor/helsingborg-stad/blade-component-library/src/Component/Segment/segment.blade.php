<!-- segment.blade.php -->
<section id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
    <div class="{{ $baseClass }}__inner">
        @if($template != "slot")
            @includeIf('Segment.sub.segment-' . $template)
        @else
            {{ $slot }}
        @endif
    </div>
</section>