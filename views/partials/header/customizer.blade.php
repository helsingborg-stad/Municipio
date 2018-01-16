@if (isset($headerLayout['panels']) && $headerLayout['panels'] && !empty($headerLayout['panels']))
    <header class="{{$headerLayout['classes']}}" {{$headerLayout['attributes']}}>
        @foreach ($headerLayout['panels'] as $panel)
            <div class="{{$panel['classes']}}" {{$panel['attributes']}}>
                @if (isset($panel['items']) && !empty($panel['items']))
                    <div class="{{$panel['bodyClasses']}}">
                        @foreach ($panel['items'] as $item)
                            <div class="{{$item['classes']}}">
                                <?php dynamic_sidebar($item['id']); ?>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </header>
@endif
