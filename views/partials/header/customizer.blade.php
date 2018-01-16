@if (isset($headerLayout['navbars']) && $headerLayout['navbars'] && !empty($headerLayout['navbars']))
    @foreach ($headerLayout['navbars'] as $navbar)
        <div class="{{$navbar['classes']}}" {{$navbar['attributes']}}>
            @if (isset($navbar['sections']) && !empty($navbar['sections']))
                <div class="{{$navbar['bodyClasses']}}">
                    @foreach ($navbar['sections'] as $section)
                        <div class="{{$section['classes']}}">
                            <?php dynamic_sidebar($section['id']); ?>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
@endif
