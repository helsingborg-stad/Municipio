@element([
    'componentElement' => 'section'
])
    {!! $post->getContent() !!}

    @if (!empty($scheduleDescription))
        {!! $scheduleDescription !!}
    @endif
@endelement