@element([])
    {{$post->getSchemaProperty('employerOverview') ?? ''}}
    {!! $post->getContent() !!}
@endelement
