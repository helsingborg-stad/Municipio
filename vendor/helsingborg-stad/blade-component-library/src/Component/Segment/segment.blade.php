<!-- segment.blade.php -->
<section id="{{ $id }}" class="{{ $class }}" {!! $attribute !!}>
    @if (!empty($top) || !empty($title) || !empty($sub_title))
        @include('Segment.sub.top')
    @endif

    @if (!empty($slot) || !empty($main) || !empty($text))
        @include('Segment.sub.main')
    @endif

    @if (!empty($bottom))
        @include('Segment.sub.bottom')
    @endif
</section>